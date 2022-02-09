<?php

namespace spec\BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Validator\Constraint\HasAllowedPaymentMethodInPostOrder;
use BitBag\SyliusInPostPlugin\Validator\HasAllowedPaymentMethodInPostOrderValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother\PaymentObjectMother;
use Tests\BitBag\SyliusInPostPlugin\Spec\ObjectMother\ShipmentObjectMother;

class HasAllowedPaymentMethodInPostOrderValidatorSpec extends ObjectBehavior
{
    public const FOO = 'foo';

    function it_is_initializable(): void
    {
        $this->shouldHaveType(HasAllowedPaymentMethodInPostOrderValidator::class);
        $this->shouldBeAnInstanceOf(ConstraintValidator::class);
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

    function it_should_add_violation_if_selected_shipping_method_is_inpost_and_payment_method_is_cash_on_deliver(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value,
        ConstraintViolationBuilderInterface $violationBuilder
    ): void
    {
        $shipment = ShipmentObjectMother::createWithShippingMethodWithInPostCode();
        $payment = PaymentObjectMother::createWithPaymentMethodWithCashOnDeliveryCode();

        $value->implement(InPostPointsAwareInterface::class);
        $value->getShipments()->willReturn(new ArrayCollection([$shipment]));
        $value->getPayments()->willReturn(new ArrayCollection([$payment]));

        $violationBuilder->atPath('payments.method')->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $violation = $context->buildViolation(HasAllowedPaymentMethodInPostOrder::NOT_ALLOWED_PAYMENT_METHOD_MESSAGE);
        $violation->shouldBeCalled();
        $violation->willReturn($violationBuilder);

        $this->initialize($context);
        $this->validate($value, $constraint);
    }
}
