<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionProviderInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Webmozart\Assert\Assert;

final class ShippingExportEventListener
{
    public const INPOST_SHIPPING_GATEWAY_CODE = 'inpost';

    public const INPOST_POINT_SHIPPING_GATEWAY_CODE = 'inpost_point';

    private const ID_KEY = 'id';

    private const STATUS_KEY = 'status';

    private WebClientInterface $webClient;

    private InPostShippingExportActionProviderInterface $shippingExportActionProvider;

    private FlashBagInterface $flashBag;

    private LoggerInterface $logger;

    public function __construct(
        WebClientInterface $webClient,
        InPostShippingExportActionProviderInterface $shippingExportActionProvider,
        FlashBagInterface $flashBag,
        LoggerInterface $logger
    ) {
        $this->webClient = $webClient;
        $this->shippingExportActionProvider = $shippingExportActionProvider;
        $this->flashBag = $flashBag;
        $this->logger = $logger;
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

        $this->webClient->setShippingGateway($shippingGateway);

        if (null === $shippingExport->getExternalId()) {
            try {
                $createShipmentResponse = $this->webClient->createShipment($shipment);
            } catch (ClientException $exception) {
                $this->flashBag->add('error', 'bitbag.ui.shipping_export_error');
                $this->logError($exception, $shipment);
                return;
            }
            $externalId = $createShipmentResponse[self::ID_KEY];
            $shippingExport->setExternalId(strval($externalId));
        }

        try {
            $shipmentData = $this->webClient->getShipmentById(intval($shippingExport->getExternalId()));
        } catch (ClientException $exception) {
            $this->flashBag->add('error', 'bitbag.ui.shipping_export_error');
            $this->logError($exception, $shipment);
            return;
        }
        Assert::notNull($shipmentData);
        Assert::keyExists($shipmentData, self::ID_KEY);
        Assert::keyExists($shipmentData, self::STATUS_KEY);

        $status = $shipmentData[self::STATUS_KEY];
        try {
            $action = $this->shippingExportActionProvider->provide($status);
        } catch (\Exception $exception) {
            $this->flashBag->add('error', 'bitbag.ui.shipping_export_error');
            $this->logError($exception, $shipment);
            return;
        }

        $action->execute($shippingExport);
    }

    private function logError(\Exception $exception, ShipmentInterface $shipment): void
    {
        $order = $shipment->getOrder();
        Assert::notNull($order);

        $message = sprintf(
            '%s %s: %s',
            '[InPostPlugin] Error while exporting shipment for order number',
            $order->getNumber(),
            $exception->getMessage()
        );

        $this->logger->error($message);
    }
}
