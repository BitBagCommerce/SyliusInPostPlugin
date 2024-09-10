<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionProviderInterface;
use BitBag\SyliusInPostPlugin\Exception\InvalidInPostResponseException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\OrderBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShipmentBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingExportBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingGatewayBuilder;

final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    private const ERROR = 'error';

    private const SHIPPING_EXPORT_ERROR_MESSAGE = 'bitbag.ui.shipping_export_error';

    private const INPOST = 'inpost';

    private const SHIPMENT_ID = 1;

    private const ORDER_NUMBER = '0000000001';

    private const EXPECTED_CREATE_SHIPMENT_RESPONSE = [
        'id' => '1',
    ];

    private const EXPECTED_GET_SHIPMENT_RESPONSE = [
        'id' => '1',
        'status' => 'created',
    ];

    public function let(
        WebClientInterface $webClient,
        InPostShippingExportActionProviderInterface $shippingExportActionProvider,
        RequestStack $requestStack,
        LoggerInterface $logger,

    ): void {
        $this->beConstructedWith($webClient, $shippingExportActionProvider, $requestStack, $logger);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    public function it_should_throw_exception_if_subject_is_not_shipping_export_instance(ResourceControllerEvent $event): void
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('exportShipment', [$event])
        ;
    }

    public function it_should_throw_exception_if_shipping_gateway_is_null(ResourceControllerEvent $event): void
    {
        $shippingExport = ShippingExportBuilder::create()->build();
        $event->getSubject()->willReturn($shippingExport);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('exportShipment', [$event]);
    }

    public function it_should_throw_exception_if_shipment_is_null(ResourceControllerEvent $event): void
    {
        $shippingGateway = ShippingGatewayBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()->withShippingGateway($shippingGateway)->build();
        $event->getSubject()->willReturn($shippingExport);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('exportShipment', [$event]);
    }

    public function it_should_do_nothing_if_shipping_gateway_code_is_not_inpost(
        ResourceControllerEvent $event,
        WebClientInterface $webClient
    ): void {
        $shippingGateway = ShippingGatewayBuilder::create()->withCode('not_inpost')->build();
        $shipment = ShipmentBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $webClient->setShippingGateway(Argument::any())->shouldNotBeCalled();

        $this->exportShipment($event);
    }

    public function it_should_handle_when_create_shipment_throws_exception(
        ResourceControllerEvent $event,
        WebClientInterface $webClient,
        FlashBagInterface $flashBag,
        LoggerInterface $logger,
        RequestStack $requestStack,
        SessionInterface $session,
        ): void {
        $order = OrderBuilder::create()->withNumber(self::ORDER_NUMBER)->build();
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shipment = ShipmentBuilder::create()->withOrder($order)->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $webClient->setShippingGateway($shippingGateway);
        $webClient->createShipment($shipment, $shippingExport)->willThrow(InvalidInPostResponseException::class);
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(self::ERROR, self::SHIPPING_EXPORT_ERROR_MESSAGE)->shouldBeCalled();
        $logger->error(Argument::type('string'))->shouldBeCalled();

        $this->exportShipment($event);
    }

    public function it_should_handle_when_get_shipment_by_id_throws_exception(
        ResourceControllerEvent $event,
        WebClientInterface $webClient,
        FlashBagInterface $flashBag,
        LoggerInterface $logger,
        RequestStack $requestStack,
        SessionInterface $session,
        ): void {
        $order = OrderBuilder::create()->withNumber(self::ORDER_NUMBER)->build();
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shipment = ShipmentBuilder::create()->withOrder($order)->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $webClient->setShippingGateway($shippingGateway);
        $webClient->createShipment($shipment, $shippingExport)->willReturn(self::EXPECTED_CREATE_SHIPMENT_RESPONSE);
        $webClient->getShipmentById(self::SHIPMENT_ID)->willThrow(InvalidInPostResponseException::class);
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(self::ERROR, self::SHIPPING_EXPORT_ERROR_MESSAGE)->shouldBeCalled();
        $logger->error(Argument::type('string'))->shouldBeCalled();

        $this->exportShipment($event);
    }

    public function it_should_handle_when_provide_shipment_action_throws_exception(
        ResourceControllerEvent $event,
        WebClientInterface $webClient,
        FlashBagInterface $flashBag,
        LoggerInterface $logger,
        InPostShippingExportActionProviderInterface $shippingExportActionProvider,
        RequestStack $requestStack,
        SessionInterface $session,
        ): void {
        $order = OrderBuilder::create()->withNumber(self::ORDER_NUMBER)->build();
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shipment = ShipmentBuilder::create()->withOrder($order)->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $webClient->setShippingGateway($shippingGateway);
        $webClient->createShipment($shipment, $shippingExport)->willReturn(self::EXPECTED_CREATE_SHIPMENT_RESPONSE);
        $webClient->getShipmentById(self::SHIPMENT_ID)->willReturn(self::EXPECTED_GET_SHIPMENT_RESPONSE);
        $shippingExportActionProvider->provide(Argument::type('string'))->willThrow(\Exception::class);
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(self::ERROR, self::SHIPPING_EXPORT_ERROR_MESSAGE)->shouldBeCalled();
        $logger->error(Argument::type('string'))->shouldBeCalled();

        $this->exportShipment($event);
    }

    public function it_should_execute_shipping_export_action(
        ResourceControllerEvent $event,
        WebClientInterface $webClient,
        InPostShippingExportActionProviderInterface $shippingExportActionProvider,
        InPostShippingExportActionInterface $shippingExportAction,
        ): void {
        $shippingGateway = ShippingGatewayBuilder::create()->withCode(self::INPOST)->build();
        $shipment = ShipmentBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withShippingGateway($shippingGateway)
            ->withShipment($shipment)
            ->build();
        $event->getSubject()->willReturn($shippingExport);

        $webClient->setShippingGateway($shippingGateway);
        $webClient->createShipment($shipment, $shippingExport)->willReturn(self::EXPECTED_CREATE_SHIPMENT_RESPONSE);
        $webClient->getShipmentById(self::SHIPMENT_ID)->willReturn(self::EXPECTED_GET_SHIPMENT_RESPONSE);
        $shippingExportActionProvider->provide(Argument::type('string'))->willReturn($shippingExportAction);
        $shippingExportAction->execute($shippingExport)->shouldBeCalled();

        $this->exportShipment($event);
    }
}
