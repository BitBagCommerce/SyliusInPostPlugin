<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface WebClientInterface
{
    public const PRODUCTION_API_ENDPOINT = 'https://api-shipx-pl.easypack24.net';

    public const SANDBOX_API_ENDPOINT = 'https://sandbox-api-shipx-pl.easypack24.net';

    public const SANDBOX_ENVIRONMENT = 'sandbox';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public const API_VERSION = 'v1';

    public const CONFIRMED_STATUS = 'confirmed';

    public const INPOST_LOCKER_STANDARD_SERVICE = 'inpost_locker_standard';

    public const INPOST_LOCKER_PASS_THRU_SERVICE = 'inpost_locker_pass_thru';

    public const INPOST_COURIER_STANDARD_SERVICE = 'inpost_courier_standard';

    public const INPOST_COURIER_EXPRESS_1000_SERVICE = 'inpost_courier_express_1000';

    public const INPOST_COURIER_EXPRESS_1200_SERVICE = 'inpost_courier_express_1200';

    public const INPOST_COURIER_EXPRESS_1700_SERVICE = 'inpost_courier_express_1700';

    public const INPOST_COURIER_LOCAL_STANDARD_SERVICE = 'inpost_courier_local_standard';

    public const INPOST_COURIER_LOCAL_EXPRESS_SERVICE = 'inpost_courier_local_express';

    public const INPOST_COURIER_LOCAL_SUPER_EXPRESS_SERVICE = 'inpost_courier_local_super_express';

    public const SMS_ADDITIONAL_SERVICE = 'sms';

    public const EMAIL_ADDITIONAL_SERVICE = 'email';

    public const SATURDAY_ADDITIONAL_SERVICE = 'saturday';

    public const ROD_ADDITIONAL_SERVICE = 'rod';

    public function setShippingGateway(ShippingGatewayInterface $shippingGateway): self;

    public function getApiEndpoint(): string;

    public function getApiEndpointForShipment(): string;

    public function getApiEndpointForPointByName(string $name): string;

    public function getApiEndpointForShipmentById(int $id): string;

    public function getApiEndpointForOrganizations(): string;

    public function getApiEndpointForLabels(): string;

    public function getPointByName(string $name): ?array;

    public function getShipmentById(int $id): ?array;

    public function getShipments(): ?array;

    public function getLabels(array $shipmentIds): ?string;

    public function getAuthorizedHeaderWithContentType(): array;

    public function createShipment(ShipmentInterface $shipment): array;

    public function request(
        string $method,
        string $url,
        array $data = [],
        bool $returnJson = true,
    ): array|string;
}
