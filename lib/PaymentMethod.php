<?php


namespace Tap;


class PaymentMethod
{
    const OBJECT_NAME = 'payment_method';

    // Available Payment Methods
    const KNET = 'src_kw.knet';
    const CARDS = 'src_card';
    const MADA = 'src_sa.mada';
    const BENEFIT = 'src_bh.benefit';
    const FAWRY = 'src_eg.fawry';
}
