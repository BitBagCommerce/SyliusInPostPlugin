<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusInPostPlugin\Api;

use BitBag\SyliusInPostPlugin\Api\WebClient;
use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Psr\Http\Client\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class WebClientSpec extends ObjectBehavior
{
    public const ORGANIZATION_ID = '123456';

    public const API_ENDPOINT = 'https://api-shipx-pl.easypack24.net/v1';

    public const POINT_NAME = 'AAA666';

    public const LABEL_TYPE = "normal";

    public const PARCEL_TEMPLATE = "medium";

    public function let(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
    ): void
    {
        $this->beConstructedWith($client, $requestFactory, $streamFactory, self::LABEL_TYPE, self::PARCEL_TEMPLATE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WebClient::class);
        $this->shouldImplement(WebClientInterface::class);
    }

    public function it_creates_api_endpoint_url_for_shipment(ShippingGatewayInterface $shippingGateway): void
    {
        $this->mockSetShippingGatewayMethod($shippingGateway);
        $this->getApiEndpointForShipment()->shouldReturn(self::API_ENDPOINT . '/organizations/' . self::ORGANIZATION_ID . '/shipments');
    }

    public function it_creates_api_endpoint_url_for_point(): void
    {
        $this->getApiEndpointForPointByName(self::POINT_NAME)->shouldReturn(self::API_ENDPOINT . '/points/' . self::POINT_NAME);
    }

    public function it_creates_api_endpoint_url_for_labels(ShippingGatewayInterface $shippingGateway): void
    {
        $this->mockSetShippingGatewayMethod($shippingGateway);
        $this->getApiEndpointForLabels()->shouldReturn(self::API_ENDPOINT . '/organizations/' . self::ORGANIZATION_ID . '/shipments/labels');
    }

    public function it_creates_api_endpoint_url_for_organizations(): void
    {
        $this->getApiEndpointForOrganizations()->shouldReturn(self::API_ENDPOINT . '/organizations');
    }

    public function it_creates_api_endpoint_url_for_shipment_by_shipment_id(): void
    {
        $this->getApiEndpointForShipmentById(1)->shouldReturn(self::API_ENDPOINT . '/shipments/1');
    }

    private function mockSetShippingGatewayMethod(ShippingGatewayInterface $shippingGateway): void
    {
        $shippingGateway->getConfigValue('access_token')->willReturn('1234567890.abcdefghij');
        $shippingGateway->getConfigValue('organization_id')->willReturn(self::ORGANIZATION_ID);
        $shippingGateway->getConfigValue('environment')->willReturn('https://sandbox-api-shipx-pl.easypack24.net');
        $this->setShippingGateway($shippingGateway);
    }
}
