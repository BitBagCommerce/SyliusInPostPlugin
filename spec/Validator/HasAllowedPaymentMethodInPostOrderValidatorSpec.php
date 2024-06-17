<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\Validator;

use BitBag\SyliusInPostPlugin\Checker\ShippingMethodCheckerInterface;
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
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\PaymentBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\PaymentMethodBuilder;

class HasAllowedPaymentMethodInPostOrderValidatorSpec extends ObjectBehavior
{
    public const FOO = 'foo';

    public function let(ShippingMethodCheckerInterface $shippingMethodChecker): void
    {
        $this->beConstructedWith($shippingMethodChecker);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(HasAllowedPaymentMethodInPostOrderValidator::class);
        $this->shouldBeAnInstanceOf(ConstraintValidator::class);
    }

    public function it_should_do_nothing_if_value_is_not_instance_of_order_interface(
        ExecutionContextInterface $context,
        Constraint $constraint
    ): void {
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();
        $value = new stdClass();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    public function it_should_do_nothing_if_value_is_not_implementing_inpost_points_aware_interface(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value
    ): void {
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    public function it_should_do_nothing_if_selected_shipping_method_is_not_inpost(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value,
        ShippingMethodCheckerInterface $shippingMethodChecker
    ): void {
        $value->implement(InPostPointsAwareInterface::class);
        $context->buildViolation(Argument::type('string'))->shouldNotBeCalled();
        $shippingMethodChecker->isInPost($value)->willReturn(false);

        $this->initialize($context);
        $this->validate($value, $constraint);
    }

    public function it_should_add_violation_if_selected_shipping_method_is_inpost_and_payment_method_is_cash_on_deliver(
        ExecutionContextInterface $context,
        Constraint $constraint,
        OrderInterface $value,
        ConstraintViolationBuilderInterface $violationBuilder,
        ShippingMethodCheckerInterface $shippingMethodChecker
    ): void {
        $paymentMethod = PaymentMethodBuilder::create()->withCode('cash_on_delivery')->build();
        $payment = PaymentBuilder::create()->withPaymentMethod($paymentMethod)->build();

        $value->implement(InPostPointsAwareInterface::class);
        $value->getPayments()->willReturn(new ArrayCollection([$payment]));
        $shippingMethodChecker->isInPost($value)->willReturn(true);

        $violationBuilder->atPath('payments.method')->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $violation = $context->buildViolation(HasAllowedPaymentMethodInPostOrder::NOT_ALLOWED_PAYMENT_METHOD_MESSAGE);
        $violation->shouldBeCalled();
        $violation->willReturn($violationBuilder);

        $this->initialize($context);
        $this->validate($value, $constraint);
    }
}
