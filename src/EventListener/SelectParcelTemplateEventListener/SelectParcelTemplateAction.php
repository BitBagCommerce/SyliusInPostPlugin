<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener\SelectParcelTemplateEventListener;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class SelectParcelTemplateAction implements SelectParcelTemplateActionInterface
{
    private const INFO = 'info';

    private const TRANSLATION_KEY = 'bitbag_sylius_inpost_plugin.ui.parcel_template.select_action_performed';

    private ShippingExportRepositoryInterface $shippingExportRepository;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    public function __construct(
        ShippingExportRepositoryInterface $shippingExportRepository,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ) {
        $this->shippingExportRepository = $shippingExportRepository;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function execute(ShippingExportInterface $shippingExport): void
    {
        $message = $this->translator->trans(self::TRANSLATION_KEY);
        $this->shippingExportRepository->add($shippingExport);
        $this->requestStack->getSession()->getBag('flashes')->add(self::INFO, $message);
    }
}
