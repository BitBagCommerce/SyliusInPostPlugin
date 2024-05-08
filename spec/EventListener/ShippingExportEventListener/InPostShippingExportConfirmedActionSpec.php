<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportConfirmedAction;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\OrderBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShipmentBuilder;
use Tests\BitBag\SyliusInPostPlugin\Spec\Builder\ShippingExportBuilder;
use Webmozart\Assert\InvalidArgumentException;

class InPostShippingExportConfirmedActionSpec extends ObjectBehavior
{
    private const SHIPPING_LABELS_PATH = '/srv/shipping_labels';

    private const SHIPPING_LABEL_CONTENT = '<MY_CONTENT>';

    private const FOO = 'foo';

    private const CONFIRMED = 'confirmed';

    private const SHIPMENT_ID = 1;

    private const ORDER_NUMBER = '0000000001';

    public function let(
        ShippingExportRepositoryInterface $shippingExportRepository,
        WebClientInterface $webClient,
        Filesystem $filesystem,
        RequestStack $requestStack
    ): void {
        $this->beConstructedWith($shippingExportRepository, $webClient, $filesystem, $requestStack, self::SHIPPING_LABELS_PATH);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(InPostShippingExportConfirmedAction::class);
        $this->shouldBeAnInstanceOf(InPostShippingExportActionInterface::class);
    }

    public function it_should_support_the_correct_status_code(): void
    {
        $this->supports(self::CONFIRMED)->shouldReturn(true);
    }

    public function it_should_not_support_the_incorrect_status_code(): void
    {
        $this->supports(self::FOO)->shouldReturn(false);
    }

    public function it_should_throw_exception_if_shipment_external_id_is_null(): void
    {
        $shippingExport = ShippingExportBuilder::create()->build();
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('execute', [$shippingExport]);
    }

    public function it_should_throw_exception_if_shipment_is_null(): void
    {
        $shippingExport = ShippingExportBuilder::create()->withExternalId(self::FOO)->build();

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('execute', [$shippingExport]);
    }

    public function it_should_throw_exception_if_order_is_null(): void
    {
        $shipment = ShipmentBuilder::create()->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withExternalId(self::FOO)
            ->withShipment($shipment)
            ->build();

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('execute', [$shippingExport]);
    }

    public function it_should_throw_exception_if_order_number_is_null(): void
    {
        $order = OrderBuilder::create()->build();
        $shipment = ShipmentBuilder::create()->withOrder($order)->build();
        $shippingExport = ShippingExportBuilder::create()
            ->withExternalId(self::FOO)
            ->withShipment($shipment)
            ->build();

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('execute', [$shippingExport]);
    }

    public function it_should_save_label_under_the_correct_path(
        WebClientInterface $webClient,
        ShipmentInterface $shipment,
        Filesystem $filesystem,
        RequestStack $requestStack,
        SessionInterface $session,
        FlashBagInterface $flashBag
        ): void {
        $order = OrderBuilder::create()->withNumber(self::ORDER_NUMBER)->build();
        $shipment->getOrder()->willReturn($order);
        $shipment->getId()->willReturn(self::SHIPMENT_ID);
        $shippingExport = ShippingExportBuilder::create()
            ->withExternalId(self::FOO)
            ->withShipment($shipment->getWrappedObject())
            ->build();

        $webClient->getLabels([self::FOO])->willReturn(self::SHIPPING_LABEL_CONTENT);

        $expectedPath = sprintf('%s/%s_%s.pdf', self::SHIPPING_LABELS_PATH, self::SHIPMENT_ID, self::ORDER_NUMBER);
        $filesystem->dumpFile($expectedPath, self::SHIPPING_LABEL_CONTENT)->shouldBeCalled();
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);

        $this->execute($shippingExport);
    }

    public function it_should_update_shipping_export_entity(
        WebClientInterface $webClient,
        ShippingExportRepositoryInterface $shippingExportRepository,
        ShipmentInterface $shipment,
        ShippingExportInterface $shippingExport,
        RequestStack $requestStack,
        SessionInterface $session,
        FlashBagInterface $flashBag
        ): void {
        $webClient->getLabels([self::FOO])->willReturn(self::SHIPPING_LABEL_CONTENT);
        $order = OrderBuilder::create()->withNumber(self::ORDER_NUMBER)->build();
        $shipment->getOrder()->willReturn($order);
        $shipment->getId()->willReturn(self::SHIPMENT_ID);
        $shippingExport->getExternalId()->willReturn(self::FOO);
        $shippingExport->getShipment()->willReturn($shipment->getWrappedObject());

        $shippingExport->setLabelPath(Argument::type('string'))->shouldBeCalled();
        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED)->shouldBeCalled();
        $shippingExport->setExportedAt(Argument::type(\DateTime::class))->shouldBeCalled();
        $shippingExportRepository->add($shippingExport)->shouldBeCalled();
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);

        $this->execute($shippingExport);
    }
}
