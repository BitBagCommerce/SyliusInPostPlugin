<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother;

use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;

class PaymentObjectMother
{
    public const CASH_ON_DELIVERY = 'cash_on_delivery';

    public static function createWithPaymentMethodWithCashOnDeliveryCode(): PaymentInterface
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setCode(self::CASH_ON_DELIVERY);

        $payment = new Payment();
        $payment->setMethod($paymentMethod);

        return $payment;
    }
}
