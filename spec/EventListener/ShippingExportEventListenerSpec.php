<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class ShippingExportEventListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $manager, FlashBagInterface $flashBag, WebClientInterface $webClient, Filesystem $filesystem): void
    {
        $shippingLabelsPath = 'labels';
        $this->beConstructedWith($manager, $flashBag, $webClient, $filesystem, $shippingLabelsPath);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingExportEventListener::class);
    }

    function it_exports_shipment(
        ResourceControllerEvent $exportShipmentEvent,
        ShippingGatewayInterface $shippingGateway,
        ShipmentInterface $shipment,
        WebClientInterface $webClient,
        ShippingExportInterface $shippingExport,
        Order $order
    ): void {
        $shippingGateway->getCode()->willReturn(ShippingExportEventListener::INPOST_SHIPPING_GATEWAY_CODE);
        $shippingGateway->getConfigValue('wsdl')->willReturn('wsdl');

        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
        $webClient->createShipment($shipment)->shouldBeCalled();
        $shippingExport->setExternalId("10")->shouldBeCalled();
        $webClient->getLabels([10])->shouldBeCalled();
        $shipment->getId()->shouldBeCalled();
        $shippingExport->setLabelPath("labels/_.pdf")->shouldBeCalled();
        $shippingExport->setState('exported')->shouldBeCalled();

        /** @var \DateTime $date */
        $date = Argument::type(\DateTime::class);
        $shippingExport->setExportedAt($date)->shouldBeCalled();

        $exportShipmentEvent->getSubject()->willReturn($shippingExport);
        $shippingExport->getShippingGateway()->willReturn($shippingGateway);
        $shippingExport->getShipment()->willReturn($shipment);

        $webClient->createShipment($shipment)->willReturn(['id' => 10]);

        $webClient->getShipmentById(10)->willReturn(['status' => WebClientInterface::CONFIRMED_STATUS, 'id' => 10]);
        $webClient->getLabels()->willReturn('Label content');
        $shipment->getOrder()->willReturn($order);

        $this->exportShipment($exportShipmentEvent);
    }

}