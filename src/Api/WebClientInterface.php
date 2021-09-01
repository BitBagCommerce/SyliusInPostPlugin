<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface WebClientInterface
{
    const PRODUCTION_API_ENDPOINT = 'https://api-shipx-pl.easypack24.net';
    const SANDBOX_API_ENDPOINT = 'https://sandbox-api-shipx-pl.easypack24.net';
    const SANDBOX_ENVIRONMENT = 'sandbox';
    const PRODUCTION_ENVIRONMENT = 'production';
    const API_VERSION = 'v1';
    const CONFIRMED_STATUS = 'confirmed';
    const INPOST_LOCKER_STANDARD_SERVICE = 'inpost_locker_standard';
    const INPOST_LOCKER_PASS_THRU_SERVICE = 'inpost_locker_pass_thru';
    const INPOST_COURIER_STANDARD_SERVICE = 'inpost_courier_standard';
    const INPOST_COURIER_EXPRESS_1000_SERVICE = 'inpost_courier_express_1000';
    const INPOST_COURIER_EXPRESS_1200_SERVICE = 'inpost_courier_express_1200';
    const INPOST_COURIER_EXPRESS_1700_SERVICE = 'inpost_courier_express_1700';
    const INPOST_COURIER_LOCAL_STANDARD_SERVICE = 'inpost_courier_local_standard';
    const INPOST_COURIER_LOCAL_EXPRESS_SERVICE = 'inpost_courier_local_express';
    const INPOST_COURIER_LOCAL_SUPER_EXPRESS_SERVICE = 'inpost_courier_local_super_express';
    const SMS_ADDITIONAL_SERVICE = 'sms';
    const EMAIL_ADDITIONAL_SERVICE = 'email';
    const SATURDAY_ADDITIONAL_SERVICE = 'saturday';
    const ROD_ADDITIONAL_SERVICE = 'rod';

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

    public function request(string $method, string $url, array $data = [], bool $returnJson = true);
}
