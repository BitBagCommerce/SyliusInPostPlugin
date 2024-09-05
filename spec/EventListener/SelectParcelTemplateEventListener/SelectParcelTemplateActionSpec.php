<?php

namespace spec\BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener\SelectParcelTemplateAction;
use BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener\SelectParcelTemplateActionInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SelectParcelTemplateActionSpec extends ObjectBehavior
{
    public function let(
        ShippingExportRepositoryInterface $shippingExportRepository,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ): void {
        $this->beConstructedWith($shippingExportRepository, $requestStack, $translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SelectParcelTemplateAction::class);
        $this->shouldBeAnInstanceOf(SelectParcelTemplateActionInterface::class);
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

}
