<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\Checker\ShippingMethodCheckerInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasPhoneNumberInPostOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasPhoneNumberInPostOrderValidator extends ConstraintValidator
{
    public const CASH_ON_DELIVERY = 'cash_on_delivery';

    private ShippingMethodCheckerInterface $shippingMethodChecker;

    public function __construct(ShippingMethodCheckerInterface $shippingMethodChecker)
    {
        $this->shippingMethodChecker = $shippingMethodChecker;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof OrderInterface) {
            return;
        }

        if (!$value instanceof InPostPointsAwareInterface) {
            return;
        }

        $isInPostShipment = $this->shippingMethodChecker->isInPost($value);
        if (false === $isInPostShipment) {
            return;
        }

        if (false === $this->validatePhoneNumber($value)) {
            $this->addPhoneNumberViolation();
        }
    }

    private function validatePhoneNumber(OrderInterface $order): bool
    {
        if (null === $order->getShippingAddress()) {
            return false;
        }

        return null !== $order->getShippingAddress()->getPhoneNumber();
    }

    private function addPhoneNumberViolation(): void
    {
        $this->context->buildViolation(HasPhoneNumberInPostOrder::PHONE_NUMBER_IS_REQUIRED_MESSAGE)
            ->atPath('shippingAddress.phoneNumber')
            ->addViolation();
    }
}
