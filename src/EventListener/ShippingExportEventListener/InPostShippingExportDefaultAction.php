<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
