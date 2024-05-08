<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportDefaultAction;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class InPostShippingExportDefaultActionSpec extends ObjectBehavior
{
    private const FOO = 'foo';

    public function let(ShippingExportRepositoryInterface $shippingExportRepository, RequestStack $requestStack): void
    {
        $this->beConstructedWith($shippingExportRepository, $requestStack);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(InPostShippingExportDefaultAction::class);
        $this->shouldBeAnInstanceOf(InPostShippingExportActionInterface::class);
    }

    public function it_should_set_state_and_exported_at_properties(
        ShippingExportInterface $shippingExport,
        RequestStack $requestStack,
        SessionInterface $session,
        FlashBagInterface $flashBag
        ): void {
        $shippingExport->setState(ShippingExportInterface::STATE_PENDING)->shouldBeCalled();
        $shippingExport->setExportedAt(Argument::type(\DateTime::class))->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);

        $this->execute($shippingExport);
    }

    public function it_should_save_shipping_export_changes(
        ShippingExportInterface $shippingExport,
        ShippingExportRepositoryInterface $shippingExportRepository,
        RequestStack $requestStack,
        SessionInterface $session,
        FlashBagInterface $flashBag
        ): void {
        $shippingExportRepository->add($shippingExport)->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);

        $this->execute($shippingExport);
    }

    public function it_should_always_support(): void
    {
        $this->supports(self::FOO)->shouldReturn(true);
    }
}
