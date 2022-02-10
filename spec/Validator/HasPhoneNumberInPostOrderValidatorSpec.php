<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasPhoneNumberInPostOrder;
use BitBag\SyliusInPostPlugin\Validator\HasPhoneNumberInPostOrderValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother\AddressObjectMother;
use Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother\ShipmentObjectMother;

class HasPhoneNumberInPostOrderValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HasPhoneNumberInPostOrderValidator::class);
    }

    function it_should_do_nothing_if_value_is_not_instance_of_order_interface(
        ExecutionContextInterface $context,
        Constraint $constraint
    ): void {
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();
        $value = new stdClass();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    function it_should_do_nothing_if_value_is_not_implementing_inpost_points_aware_interface(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value
    ): void {
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    function it_should_do_nothing_if_selected_shipping_method_is_not_inpost(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value
    ): void {
        $shipment = ShipmentObjectMother::createWithShippingMethodWithFooCode();

        $value->implement(InPostPointsAwareInterface::class);
        $value->getShipments()->willReturn(new ArrayCollection([$shipment]));
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    function it_should_do_nothing_if_phone_number_is_set(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value
    ): void {
        $shipment = ShipmentObjectMother::createWithShippingMethodWithInPostCode();
        $shippingAddress = AddressObjectMother::createWithPhoneNumber();

        $value->implement(InPostPointsAwareInterface::class);
        $value->getShippingAddress()->willReturn($shippingAddress);
        $value->getShipments()->willReturn(new ArrayCollection([$shipment]));

        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    function it_should_add_violation_if_phone_number_is_not_set(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value,
        ConstraintViolationBuilderInterface $violationBuilder
    ): void {
        $shipment = ShipmentObjectMother::createWithShippingMethodWithInPostCode();
        $shippingAddress = AddressObjectMother::createSimple();

        $value->implement(InPostPointsAwareInterface::class);
        $value->getShippingAddress()->willReturn($shippingAddress);
        $value->getShipments()->willReturn(new ArrayCollection([$shipment]));

        $violationBuilder->atPath('shippingAddress.phoneNumber')->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $violation = $context->buildViolation(HasPhoneNumberInPostOrder::PHONE_NUMBER_IS_REQUIRED_MESSAGE);
        $violation->shouldBeCalled()->willReturn($violationBuilder);

        $this->initialize($context);
        $this->validate($value, $constraint);
    }
}
