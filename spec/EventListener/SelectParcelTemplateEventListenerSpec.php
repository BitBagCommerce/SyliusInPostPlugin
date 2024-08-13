<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener;
use BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener\SelectParcelTemplateActionInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShipmentBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingExportBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingGatewayBuilder;

final class SelectParcelTemplateEventListenerSpec extends ObjectBehavior
{
    private const INCORRECT_PARCEL_TEMPLATE = 'x-large';

    private const EXPECTED_PARCEL_TEMPLATE = 'small';

    private const INPOST = 'inpost';

    public function let(
        SelectParcelTemplateActionInterface $action
    ): void {
        $this->beConstructedWith($action);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SelectParcelTemplateEventListener::class);
    }

    public function it_should_throw_exception_if_subject_is_not_shipping_export_instance(ResourceControllerEvent $event): void
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('setParcelTemplate', [$event])
        ;
    }

    public function it_should_throw_exception_if_incorrect_parcel_template_is_passed(ResourceControllerEvent $event): void
    {
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shipment = ShipmentBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->withParcelTemplate(self::INCORRECT_PARCEL_TEMPLATE)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $this
            ->shouldThrow(\Exception::class)
            ->during('setParcelTemplate', [$event])
        ;
    }

    public function it_should_perform_set_parcel_template_action(ResourceControllerEvent $event): void
    {
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shippingGateway->setConfig(['service' => 'inpost_locker_standard']);
        $shipment = ShipmentBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->withParcelTemplate(self::EXPECTED_PARCEL_TEMPLATE)
            ->build();
        $event->getSubject()->willReturn($shippingExport);
        $this->setParcelTemplate($event);
    }
}
