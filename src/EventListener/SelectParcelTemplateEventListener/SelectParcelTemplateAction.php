<?php

namespace BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SelectParcelTemplateAction implements SelectParcelTemplateActionInterface
{
    private const INFO = 'info';
    private const TRANSLATION_KEY = 'bitbag_sylius_inpost_plugin.ui.parcel_template.select_action_performed';

    private ShippingExportRepositoryInterface $shippingExportRepository;
    private RequestStack $requestStack;

    public function __construct(ShippingExportRepositoryInterface $shippingExportRepository, RequestStack $requestStack)
    {
        $this->shippingExportRepository = $shippingExportRepository;
        $this->requestStack = $requestStack;
    }

    public function execute(ShippingExportInterface $shippingExport): void
    {
        $this->shippingExportRepository->add($shippingExport);
        $this->requestStack->getSession()->getBag('flashes')->add(self::INFO, self::TRANSLATION_KEY);
    }
}
