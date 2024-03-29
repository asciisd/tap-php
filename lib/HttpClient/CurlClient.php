<?php

namespace Tap\HttpClient;

use Tap\Exception;
use Tap\Tap;
use Tap\Util;
use Tap\Util\RandomGenerator;

// @codingStandardsIgnoreStart
// PSR2 requires all constants be upper case. Sadly, the CURL_SSLVERSION
// constants do not abide by those rules.

// Note the values come from their position in the enums that
// defines them in cURL's source code.

// Available since PHP 5.5.19 and 5.6.3
if (!defined('CURL_SSLVERSION_TLSv1_2')) {
    define('CURL_SSLVERSION_TLSv1_2', 6);
}
// @codingStandardsIgnoreEnd

// Available since PHP 7.0.7 and cURL 7.47.0
if (!defined('CURL_HTTP_VERSION_2TLS')) {
    define('CURL_HTTP_VERSION_2TLS', 4);
}

class CurlClient implements ClientInterface
{
    private static $instance;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** @var RandomGenerator */
    protected $randomGenerator;

    protected $defaultOptions;

    protected $userAgentInfo;

    protected $enablePersistentConnections = true;

    protected $enableHttp2 = null;

    protected $curlHandle = null;

    /**
     * CurlClient constructor.
     *
     * Pass in a callable to $defaultOptions that returns an array of CURLOPT_* values to start
     * off a request with, or an flat array with the same format used by curl_setopt_array() to
     * provide a static set of options. Note that many options are overridden later in the request
     * call, including timeouts, which can be set via setTimeout() and setConnectTimeout().
     *
     * Note that request() will silently ignore a non-callable, non-array $defaultOptions, and will
     * throw an exception if $defaultOptions returns a non-array value.
     *
     * @param ?array|?callable $defaultOptions
     * @param null $randomGenerator
     */
    public function __construct($defaultOptions = null, $randomGenerator = null)
    {
        $this->defaultOptions = $defaultOptions;
        $this->randomGenerator = $randomGenerator ?: new RandomGenerator();
        $this->initUserAgentInfo();

        $this->enableHttp2 = $this->canSafelyUseHttp2();
    }

    public function __destruct()
    {
        $this->closeCurlHandle();
    }

    public function initUserAgentInfo()
    {
        $curlVersion = curl_version();
        $this->userAgentInfo = [
            'httplib' => 'curl ' . $curlVersion['version'],
            'ssllib' => $curlVersion['ssl_version'],
        ];
    }

    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    public function getUserAgentInfo()
    {
        return $this->userAgentInfo;
    }

    /**
     * @return boolean
     */
    public function getEnablePersistentConnections()
    {
        return $this->enablePersistentConnections;
    }

    /**
     * @param boolean $enable
     */
    public function setEnablePersistentConnections($enable)
    {
        $this->enablePersistentConnections = $enable;
    }

    /**
     * @return boolean
     */
    public function getEnableHttp2()
    {
        return $this->enableHttp2;
    }

    /**
     * @param boolean $enable
     */
    public function setEnableHttp2($enable)
    {
        $this->enableHttp2 = $enable;
    }

    // USER DEFINED TIMEOUTS

    const DEFAULT_TIMEOUT = 80;
    const DEFAULT_CONNECT_TIMEOUT = 30;

    private $timeout = self::DEFAULT_TIMEOUT;
    private $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

    public function setTimeout($seconds)
    {
        $this->timeout = (int)max($seconds, 0);
        return $this;
    }

    public function setConnectTimeout($seconds)
    {
        $this->connectTimeout = (int)max($seconds, 0);
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    // END OF USER DEFINED TIMEOUTS

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        $method = strtolower($method);

        $opts = [];
        if (is_callable($this->defaultOptions)) { // call defaultOptions callback, set options to return value
            $opts = call_user_func_array($this->defaultOptions, func_get_args());
            if (!is_array($opts)) {
                throw new Exception\UnexpectedValueException("Non-array value returned by defaultOptions CurlClient callback");
            }
        } elseif (is_array($this->defaultOptions)) { // set default curlopts from array
            $opts = $this->defaultOptions;
        }

//        $params = Util\Util::objectsToIds($params);

        if ($method == 'get') {
            if ($hasFile) {
                throw new Exception\UnexpectedValueException(
                    "Issuing a GET request with a file parameter"
                );
            }
            $opts[CURLOPT_HTTPGET] = 1;
            if (count($params) > 0) {
                $encoded = Util\Util::encodeParameters($params);
                $absUrl = "$absUrl?$encoded";
            }
        } elseif ($method == 'post') {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $hasFile ? $params : Util\Util::encodePostParameters($params);
        } elseif ($method == 'put') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $hasFile ? $params : Util\Util::encodePostParameters($params);
        } elseif ($method == 'delete') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if (count($params) > 0) {
                $encoded = Util\Util::encodeParameters($params);
                $absUrl = "$absUrl?$encoded";
            }
        } else {
            throw new Exception\UnexpectedValueException("Unrecognized method $method");
        }

        // It is only safe to retry network failures on POST requests if we
        // add an Idempotency-Key header
        if (($method == 'post') && (Tap::$maxNetworkRetries > 0)) {
            if (!$this->hasHeader($headers, "Idempotency-Key")) {
                array_push($headers, 'Idempotency-Key: ' . $this->randomGenerator->uuid());
            }
        }

        // By default for large request body sizes (> 1024 bytes), cURL will
        // send a request without a body and with a `Expect: 100-continue`
        // header, which gives the server a chance to respond with an error
        // status code in cases where one can be determined right away (say
        // on an authentication problem for example), and saves the "large"
        // request body from being ever sent.
        //
        // Unfortunately, the bindings don't currently correctly handle the
        // success case (in which the server sends back a 100 CONTINUE), so
        // we'll error under that condition. To compensate for that problem
        // for the time being, override cURL's behavior by simply always
        // sending an empty `Expect:` header.
        array_push($headers, 'Expect: ');

        $absUrl = Util\Util::utf8($absUrl);
        $opts[CURLOPT_URL] = $absUrl;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[CURLOPT_TIMEOUT] = $this->timeout;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        if (!Tap::getVerifySslCerts()) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
        }

        if (!isset($opts[CURLOPT_HTTP_VERSION]) && $this->getEnableHttp2()) {
            // For HTTPS requests, enable HTTP/2, if supported
            $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2TLS;
        }

        list($rbody, $rcode, $rheaders) = $this->executeRequestWithRetries($opts, $absUrl);

        return [$rbody, $rcode, $rheaders];
    }

    /**
     * @param array $opts cURL options
     * @param $absUrl
     * @return array
     * @throws Exception\ApiConnectionException
     */
    private function executeRequestWithRetries($opts, $absUrl)
    {
        $numRetries = 0;
        $isPost = array_key_exists(CURLOPT_POST, $opts) && $opts[CURLOPT_POST] == 1;

        while (true) {
            $rcode = 0;
            $errno = 0;
            $message = null;

            // Create a callback to capture HTTP headers for the response
            $rheaders = new Util\CaseInsensitiveArray();
            $headerCallback = function ($curl, $header_line) use (&$rheaders) {
                // Ignore the HTTP request line (HTTP/1.1 200 OK)
                if (strpos($header_line, ":") === false) {
                    return strlen($header_line);
                }
                list($key, $value) = explode(":", trim($header_line), 2);
                $rheaders[trim($key)] = trim($value);
                return strlen($header_line);
            };
            $opts[CURLOPT_HEADERFUNCTION] = $headerCallback;

            $this->resetCurlHandle();
            curl_setopt_array($this->curlHandle, $opts);
            $rbody = curl_exec($this->curlHandle);

            if ($rbody === false) {
                $errno = curl_errno($this->curlHandle);
                $message = curl_error($this->curlHandle);
            } else {
                $rcode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
            }
            if (!$this->getEnablePersistentConnections()) {
                $this->closeCurlHandle();
            }

            if ($this->shouldRetry($errno, $rcode, $rheaders, $numRetries)) {
                $numRetries += 1;
                $sleepSeconds = $this->sleepTime($numRetries, $rheaders);
                usleep(intval($sleepSeconds * 1000000));
            } else {
                break;
            }
        }

        if ($rbody === false) {
            $this->handleCurlError($absUrl, $errno, $message, $numRetries);
        }

        return [$rbody, $rcode, $rheaders];
    }

    /**
     * @param string $url
     * @param int $errno
     * @param string $message
     * @param int $numRetries
     * @throws Exception\ApiConnectionException
     */
    private function handleCurlError($url, $errno, $message, $numRetries)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to Tap ($url).  Please check your "
                    . "internet connection and try again.  If this problem persists, "
                    . "you should check Tap's service status at "
                    . "https://twitter.com/tapstatus, or";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify Tap's SSL certificate.  Please make sure "
                    . "that your network is not intercepting certificates.  "
                    . "(Try going to $url in your browser.)  "
                    . "If this problem persists,";
                break;
            default:
                $msg = "Unexpected error communicating with Tap.  "
                    . "If this problem persists,";
        }
        $msg .= " let us know at support@tap.com.";

        $msg .= "\n\n(Network error [errno $errno]: $message)";

        if ($numRetries > 0) {
            $msg .= "\n\nRequest was retried $numRetries times.";
        }

        throw new Exception\ApiConnectionException($msg);
    }

    /**
     * Checks if an error is a problem that we should retry on. This includes both
     * socket errors that may represent an intermittent problem and some special
     * HTTP statuses.
     *
     * @param int $errno
     * @param int $rcode
     * @param array|CaseInsensitiveArray $rheaders
     * @param int $numRetries
     *
     * @return bool
     */
    private function shouldRetry($errno, $rcode, $rheaders, $numRetries)
    {
        if ($numRetries >= Tap::getMaxNetworkRetries()) {
            return false;
        }

        // Retry on timeout-related problems (either on open or read).
        if ($errno === CURLE_OPERATION_TIMEOUTED) {
            return true;
        }

        // Destination refused the connection, the connection was reset, or a
        // variety of other connection failures. This could occur from a single
        // saturated server, so retry in case it's intermittent.
        if ($errno === CURLE_COULDNT_CONNECT) {
            return true;
        }

        // The API may ask us not to retry (eg; if doing so would be a no-op)
        // or advise us to retry (eg; in cases of lock timeouts); we defer to that.
        if (isset($rheaders['tap-should-retry'])) {
            if ($rheaders['tap-should-retry'] === 'false') {
                return false;
            }
            if ($rheaders['tap-should-retry'] === 'true') {
                return true;
            }
        }

        // 409 Conflict
        if ($rcode === 409) {
            return true;
        }

        // Retry on 500, 503, and other internal errors.
        //
        // Note that we expect the tap-should-retry header to be false
        // in most cases when a 500 is returned, since our idempotency framework
        // would typically replay it anyway.
        if ($rcode >= 500) {
            return true;
        }

        return false;
    }

    /**
     * Provides the number of seconds to wait before retrying a request.
     *
     * @param int $numRetries
     * @param array|CaseInsensitiveArray $rheaders
     *
     * @return int
     */
    private function sleepTime($numRetries, $rheaders)
    {
        // Apply exponential backoff with $initialNetworkRetryDelay on the
        // number of $numRetries so far as inputs. Do not allow the number to exceed
        // $maxNetworkRetryDelay.
        $sleepSeconds = min(
            Tap::getInitialNetworkRetryDelay() * 1.0 * pow(2, $numRetries - 1),
            Tap::getMaxNetworkRetryDelay()
        );

        // Apply some jitter by randomizing the value in the range of
        // ($sleepSeconds / 2) to ($sleepSeconds).
        $sleepSeconds *= 0.5 * (1 + $this->randomGenerator->randFloat());

        // But never sleep less than the base sleep seconds.
        $sleepSeconds = max(Tap::getInitialNetworkRetryDelay(), $sleepSeconds);

        // And never sleep less than the time the API asks us to wait, assuming it's a reasonable ask.
        $retryAfter = isset($rheaders['retry-after']) ? floatval($rheaders['retry-after']) : 0.0;
        if (floor($retryAfter) == $retryAfter && $retryAfter <= Tap::getMaxRetryAfter()) {
            $sleepSeconds = max($sleepSeconds, $retryAfter);
        }

        return $sleepSeconds;
    }

    /**
     * Initializes the curl handle. If already initialized, the handle is closed first.
     */
    private function initCurlHandle()
    {
        $this->closeCurlHandle();
        $this->curlHandle = curl_init();
    }

    /**
     * Closes the curl handle if initialized. Do nothing if already closed.
     */
    private function closeCurlHandle()
    {
        if (!is_null($this->curlHandle)) {
            curl_close($this->curlHandle);
            $this->curlHandle = null;
        }
    }

    /**
     * Resets the curl handle. If the handle is not already initialized, or if persistent
     * connections are disabled, the handle is reinitialized instead.
     */
    private function resetCurlHandle()
    {
        if (!is_null($this->curlHandle) && $this->getEnablePersistentConnections()) {
            curl_reset($this->curlHandle);
        } else {
            $this->initCurlHandle();
        }
    }

    /**
     * Indicates whether it is safe to use HTTP/2 or not.
     *
     * @return boolean
     */
    private function canSafelyUseHttp2()
    {
        // Versions of curl older than 7.60.0 don't respect GOAWAY frames
        // (cf. https://github.com/curl/curl/issues/2416), which Tap use.
        $curlVersion = curl_version()['version'];
        return (version_compare($curlVersion, '7.60.0') >= 0);
    }

    /**
     * Checks if a list of headers contains a specific header name.
     *
     * @param string[] $headers
     * @param string $name
     * @return boolean
     */
    private function hasHeader($headers, $name)
    {
        foreach ($headers as $header) {
            if (strncasecmp($header, "{$name}: ", strlen($name) + 2) === 0) {
                return true;
            }
        }

        return false;
    }
}
