<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class InPostShippingExportDefaultAction implements InPostShippingExportActionInterface
{
    private const INFO = 'info';

    private const TRANSLATION_KEY = 'bitbag.ui.shipment_data_has_been_marked_as_pending';

    private ShippingExportRepositoryInterface $shippingExportRepository;

    private RequestStack $requestStack;

    public function __construct(ShippingExportRepositoryInterface $shippingExportRepository, RequestStack $requestStack)
    {
        $this->shippingExportRepository = $shippingExportRepository;
        $this->requestStack = $requestStack;
    }

    public function execute(ShippingExportInterface $shippingExport): void
    {
        $shippingExport->setState(ShippingExportInterface::STATE_PENDING);
        $shippingExport->setExportedAt(new \DateTime());

        $this->shippingExportRepository->add($shippingExport);

        $this->requestStack->getSession()->getBag('flashes')->add(self::INFO, self::TRANSLATION_KEY);
    }

    public function supports(string $statusCode): bool
    {
        return true;
    }
}
