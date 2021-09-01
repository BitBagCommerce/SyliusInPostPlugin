<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\SoapClientInterface;
use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    function let(WebClientInterface $webClient, SoapClientInterface $soapClient): void
    {
        $this->beConstructedWith($webClient, $soapClient);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    function it_export_shipment(
        ExportShipmentEvent $exportShipmentEvent,
        ShippingExportInterface $shippingExport,
        ShippingGatewayInterface $shippingGateway,
        ShipmentInterface $shipment,
        WebClientInterface $webClient,
        SoapClientInterface $soapClient,
        Order $order
    ): void {
        $webClient->setShippingGateway($shippingGateway);

        $shippingGateway->getCode()->willReturn(ShippingExportEventListener::INPOST_POINT_SHIPPING_GATEWAY_CODE);
        $shippingGateway->getConfigValue('wsdl')->willReturn('wsdl');

//        $webClient->getRequestData()->willReturn([]);
        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
//        $webClient->setShipment($shipment)->shouldBeCalled();

        $shippingExport->getShipment()->willReturn($shipment);

        $exportShipmentEvent->getShippingExport()->willReturn($shippingExport);
        $exportShipmentEvent->addSuccessFlash()->shouldBeCalled();
        $exportShipmentEvent->exportShipment()->shouldBeCalled();
        $exportShipmentEvent->saveShippingLabel('', 'pdf')->shouldBeCalled();
        $shippingExport->getShippingGateway()->willReturn($shippingGateway);

        $order->getNumber()->willReturn(1000);
        $shipment->getOrder()->willReturn($order);

        $soapClient->createShipment([], 'wsdl')->willReturn(
            (object) ['createShipmentResult' => (object) ['label' => (object) [
                'labelContent' => '',
                'labelType' => 't',
            ],
            ],
            ]
        );

        $this->exportShipment($exportShipmentEvent);
    }
}