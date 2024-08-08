<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\Exception\InPostException;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class InPostShippingExportConfirmedAction implements InPostShippingExportActionInterface
{
    private const CONFIRMED = 'confirmed';

    private const LABEL_EXTENSION = 'pdf';

    private const SUCCESS = 'success';

    private const TRANSLATION_KEY = 'bitbag.ui.shipment_data_has_been_exported';

    private ShippingExportRepositoryInterface $shippingExportRepository;

    private WebClientInterface $webClient;

    private Filesystem $filesystem;

    private RequestStack $requestStack;

    private string $shippingLabelsPath;

    public function __construct(
        ShippingExportRepositoryInterface $shippingExportRepository,
        WebClientInterface $webClient,
        Filesystem $filesystem,
        RequestStack $requestStack,
        string $shippingLabelsPath,
    ) {
        $this->shippingExportRepository = $shippingExportRepository;
        $this->webClient = $webClient;
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->shippingLabelsPath = $shippingLabelsPath;
    }

    /**
     * @throws InPostException
     */
    public function execute(ShippingExportInterface $shippingExport): void
    {
        $this->validateShippingExport($shippingExport);

        $label = $this->webClient->getLabels([$shippingExport->getExternalId()]);
        Assert::notNull($label, 'Label cannot be null');
        $labelPath = $this->getShippingLabelPath($shippingExport);
        $shippingExport->setLabelPath($labelPath);

        $this->filesystem->dumpFile($labelPath, $label);

        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED);
        $shippingExport->setExportedAt(new \DateTime());

        $this->shippingExportRepository->add($shippingExport);
        $this->requestStack->getSession()->getBag('flashes')->add(self::SUCCESS, self::TRANSLATION_KEY);
    }

    private function validateShippingExport(ShippingExportInterface $shippingExport): void
    {
        Assert::notNull($shippingExport->getExternalId(), 'Shipping export external id cannot be null');
        Assert::notNull($shippingExport->getShipment(), 'Shipment cannot be null');
        Assert::notNull($shippingExport->getShipment()->getOrder(), 'Order cannot be null');
        Assert::notNull($shippingExport->getShipment()->getOrder()->getNumber(), 'Order number cannot be null');
    }

    private function getShippingLabelPath(ShippingExportInterface $shippingExport): string
    {
        return sprintf('%s/%s.%s', $this->shippingLabelsPath, $this->getFilename($shippingExport), self::LABEL_EXTENSION);
    }

    private function getFilename(ShippingExportInterface $shippingExport): string
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $shippingExport->getShipment();
        /** @var OrderInterface $order */
        $order = $shipment->getOrder();

        return implode(
            '_',
            [
                $shipment->getId(),
                $order->getNumber(),
            ],
        );
    }

    public function supports(string $statusCode): bool
    {
        return self::CONFIRMED === $statusCode;
    }
}
