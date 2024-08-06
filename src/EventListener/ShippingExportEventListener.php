<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener\InPostShippingExportActionProviderInterface;
use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\Exception\InPostException;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class ShippingExportEventListener
{
    public const INPOST_SHIPPING_GATEWAY_CODE = 'inpost';

    public const INPOST_POINT_SHIPPING_GATEWAY_CODE = 'inpost_point';

    private const ID_KEY = 'id';

    private const STATUS_KEY = 'status';

    private WebClientInterface $webClient;

    private InPostShippingExportActionProviderInterface $shippingExportActionProvider;

    private RequestStack $requestStack;

    private LoggerInterface $logger;

    public function __construct(
        WebClientInterface $webClient,
        InPostShippingExportActionProviderInterface $shippingExportActionProvider,
        RequestStack $requestStack,
        LoggerInterface $logger,
    ) {
        $this->webClient = $webClient;
        $this->shippingExportActionProvider = $shippingExportActionProvider;
        $this->requestStack = $requestStack;
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

        if (self::INPOST_SHIPPING_GATEWAY_CODE !== $shippingGateway->getCode()) {
            return;
        }

        $this->webClient->setShippingGateway($shippingGateway);

        if (null === $shippingExport->getExternalId()) {
            try {
                $createShipmentResponse = $this->webClient->createShipment($shipment);
            } catch (InPostException $exception) {
                $this->requestStack->getSession()->getBag('flashes')->add('error', 'bitbag.ui.shipping_export_error');
                $this->logError($exception, $shipment);

                return;
            }
            $externalId = $createShipmentResponse[self::ID_KEY];
            $shippingExport->setExternalId((string) $externalId);
        }

        try {
            $shipmentData = $this->webClient->getShipmentById((int) ($shippingExport->getExternalId()));
        } catch (InPostException $exception) {
            $this->requestStack->getSession()->getBag('flashes')->add('error', 'bitbag.ui.shipping_export_error');
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
            $this->requestStack->getSession()->getBag('flashes')->add('error', 'bitbag.ui.shipping_export_error');
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
            $exception->getMessage(),
        );

        $this->logger->error($message);
    }
}
