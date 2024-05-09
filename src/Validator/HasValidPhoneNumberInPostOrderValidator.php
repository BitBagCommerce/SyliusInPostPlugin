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
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasValidPhoneNumberInPostOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasValidPhoneNumberInPostOrderValidator extends ConstraintValidator
{
    private ShippingMethodCheckerInterface $shippingMethodChecker;

    public function __construct(ShippingMethodCheckerInterface $shippingMethodChecker)
    {
        $this->shippingMethodChecker = $shippingMethodChecker;
    }

    /**
     * @param mixed $value
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

        $phone = $value->getShippingAddress()->getPhoneNumber();
        if (null === $phone) {
            return;
        }

        $length = strlen($phone);
        if (HasValidPhoneNumberInPostOrder::POLISH_PHONE_NUMBER_DEFAULT_LENGTH > $length) {
            $this->addPhoneNumberViolation(HasValidPhoneNumberInPostOrder::PHONE_NUMBER_IS_TOO_SHORT_MESSAGE);
        }

        if (HasValidPhoneNumberInPostOrder::POLISH_PHONE_NUMBER_DEFAULT_LENGTH < $length) {
            $this->addPhoneNumberViolation(HasValidPhoneNumberInPostOrder::PHONE_NUMBER_IS_TOO_LONG_MESSAGE);
        }
    }

    private function addPhoneNumberViolation(string $message): void
    {
        $violationBuilder = $this->context->buildViolation($message);
        $violationBuilder->setParameter('{{ limit }}', (string) HasValidPhoneNumberInPostOrder::POLISH_PHONE_NUMBER_DEFAULT_LENGTH);
        $violationBuilder->atPath('shippingAddress.phoneNumber')
            ->addViolation();
    }
}
