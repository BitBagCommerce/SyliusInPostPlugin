<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasPhoneNumberInPostOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasPhoneNumberInPostOrderValidator extends ConstraintValidator
{
    public const CASH_ON_DELIVERY = 'cash_on_delivery';

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

        $isInPostShipment = $this->isSelectedInPostShippingMethod($value);
        if (false === $isInPostShipment) {
            return;
        }

        if (false === $this->validatePhoneNumber($value)) {
            $this->addPhoneNumberViolation();
        }
    }

    private function isSelectedInPostShippingMethod(OrderInterface $order): bool
    {
        return $order->getShipments()->exists(function (int $index, ShipmentInterface $shipment): bool {
            if (null === $shipment->getMethod()) {
                return false;
            }

            $shipmentCode = $shipment->getMethod()->getCode();
            $isInPostPoint = ShippingExportEventListener::INPOST_POINT_SHIPPING_GATEWAY_CODE === $shipmentCode;
            $isInPost = ShippingExportEventListener::INPOST_SHIPPING_GATEWAY_CODE === $shipmentCode;

            return $isInPostPoint || $isInPost;
        });
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
