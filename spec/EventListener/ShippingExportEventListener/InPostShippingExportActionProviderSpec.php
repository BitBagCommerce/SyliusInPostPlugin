<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionProvider;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class InPostShippingExportActionProviderSpec extends ObjectBehavior
{
    private const FOO = 'foo';

    function let(
        InPostShippingExportActionInterface $action1,
        InPostShippingExportActionInterface $action2
    ): void {
        $this->beConstructedWith([$action1, $action2]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(InPostShippingExportActionProvider::class);
        $this->shouldBeAnInstanceOf(InPostShippingExportActionProviderInterface::class);
    }

    function it_should_throw_exception_if_no_action_supports_passed_status_code(
        InPostShippingExportActionInterface $action1,
        InPostShippingExportActionInterface $action2
    ): void {
        $action1->supports(Argument::type('string'))->willReturn(false);
        $action2->supports(Argument::type('string'))->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [self::FOO]);
    }

    function it_should_return_first_supported_action(
        InPostShippingExportActionInterface $action1,
        InPostShippingExportActionInterface $action2
    ): void {
        $action1->supports(Argument::type('string'))->willReturn(true);
        $action2->supports(Argument::type('string'))->willReturn(true);

        $this->provide(self::FOO)->shouldReturn($action1);
    }
}
