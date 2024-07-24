<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Spec\Builder;

use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentBuilder
{
    private PaymentInterface $payment;

    public static function create(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->payment = new Payment();
    }

    public function withPaymentMethod(PaymentMethodInterface $paymentMethod): self
    {
        $this->payment->setMethod($paymentMethod);

        return $this;
    }

    public function build(): PaymentInterface
    {
        return $this->payment;
    }
}
