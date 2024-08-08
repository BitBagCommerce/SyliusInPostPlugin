<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\Checker\ShippingMethodCheckerInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasValidPhoneNumberInPostOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class HasValidPhoneNumberInPostOrderValidator extends ConstraintValidator
{
    private ShippingMethodCheckerInterface $shippingMethodChecker;

    public function __construct(ShippingMethodCheckerInterface $shippingMethodChecker)
    {
        $this->shippingMethodChecker = $shippingMethodChecker;
    }

    /* @param mixed $value */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderInterface::class);

        Assert::isInstanceOf($value, InPostPointsAwareInterface::class);

        $isInPostShipment = $this->shippingMethodChecker->isInPost($value);
        if (false === $isInPostShipment) {
            return;
        }

        $phone = $value->getShippingAddress()->getPhoneNumber();
        if (null === $phone) {
            return;
        }

        $formatedPhone = $this->formatPhoneNumber($phone);

        $length = strlen($formatedPhone);

        if (HasValidPhoneNumberInPostOrder::PHONE_NUMBER_DEFAULT_LENGTH !== $length) {
            $this->addPhoneNumberViolation(HasValidPhoneNumberInPostOrder::PHONE_NUMBER_LENGTH_INCORRECT);
        }
    }

    private function addPhoneNumberViolation(string $message): void
    {
        $violationBuilder = $this->context->buildViolation($message);
        $violationBuilder->atPath('shippingAddress.phoneNumber')
            ->addViolation();
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (9 < strlen($phone)) {
            $escaped_prefixes = array_map('preg_quote', HasValidPhoneNumberInPostOrder::POSSIBLE_PHONE_PREFIXES);
            $pattern = '/^(' . implode('|', $escaped_prefixes) . ')/';
            $phone = preg_replace($pattern, '', $phone);
        }

        return (string) $phone;
    }
}
