<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Exception\ClientException;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Webmozart\Assert\Assert;

final class ShippingExportEventListener
{
    public const INPOST_SHIPPING_GATEWAY_CODE = 'inpost';

    public const INPOST_POINT_SHIPPING_GATEWAY_CODE = 'inpost_point';

    private WebClientInterface $webClient;

    private FlashBagInterface $flashBag;

    private Filesystem $filesystem;

    private string $shippingLabelsPath;

    private ObjectManager $shippingExportManager;

    public function __construct(
        ObjectManager $shippingExportManager,
        FlashBagInterface $flashBag,
        WebClientInterface $webClient,
        Filesystem $filesystem,
        string $shippingLabelsPath
    ) {
        $this->flashBag = $flashBag;
        $this->webClient = $webClient;
        $this->filesystem = $filesystem;
        $this->shippingExportManager = $shippingExportManager;
        $this->shippingLabelsPath = $shippingLabelsPath;
    }

    public function exportShipment(ResourceControllerEvent $exportShipmentEvent): void
    {
        /** @var ?ShippingExportInterface $shippingExport */
        $shippingExport = $exportShipmentEvent->getSubject();
        Assert::isInstanceOf($shippingExport, ShippingExportInterface::class);

        /** @var ?ShippingGatewayInterface $shippingGateway */
        $shippingGateway = $shippingExport->getShippingGateway();
        Assert::notNull($shippingGateway);

        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        if ($shippingGateway->getCode() !== self::INPOST_SHIPPING_GATEWAY_CODE) {
            return;
        }

        try {
            $this->webClient->setShippingGateway($shippingGateway);

            $result = $this->webClient->createShipment($shipment);

            $externalId = $result['id'];

            $shippingExport->setExternalId((string) $externalId);

            $data = $this->webClient->getShipmentById($externalId);
            Assert::notNull($data);
            Assert::keyExists($data, 'status');
            Assert::keyExists($data, 'id');

            if (WebClientInterface::CONFIRMED_STATUS === $data['status'] && null !== $this->webClient->getLabels([$data['id']])) {
                $this->saveShippingLabel($shippingExport, $this->webClient->getLabels([$data['id']]), 'pdf');
            }
        } catch (ClientException $exception) {
            Assert::notNull($shipment->getOrder());
            $this->flashBag->add(
                'error',
                sprintf(
                    'InPost Web Service for #%s order: %s',
                    $shipment->getOrder()->getNumber(),
                    $exception->getMessage()
                )
            );

            return;
        }

        $this->flashBag->add('success', 'bitbag.ui.shipment_data_has_been_exported');
        $this->markShipmentAsExported($shippingExport);
    }

    public function saveShippingLabel(
        ShippingExportInterface $shippingExport,
        string $labelContent,
        string $labelExtension
    ): void {
        $labelPath = sprintf(
            '%s/%s.%s',
            $this->shippingLabelsPath,
            $this->getFilename($shippingExport),
            $labelExtension
        );

        $this->filesystem->dumpFile($labelPath, $labelContent);
        $shippingExport->setLabelPath($labelPath);

        $this->shippingExportManager->persist($shippingExport);
        $this->shippingExportManager->flush();
    }

    private function getFilename(ShippingExportInterface $shippingExport): string
    {
        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $order = $shipment->getOrder();
        Assert::notNull($order);

        $orderNumber = $order->getNumber();
        Assert::notNull($orderNumber);

        $shipmentId = $shipment->getId();

        return implode(
            '_',
            [
                $shipmentId,
                preg_replace('~[A-Za-z0-9]~', '', $orderNumber),
            ]
        );
    }

    private function markShipmentAsExported(ShippingExportInterface $shippingExport): void
    {
        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED);
        $shippingExport->setExportedAt(new \DateTime());

        $this->shippingExportManager->persist($shippingExport);
        $this->shippingExportManager->flush();
    }
}
