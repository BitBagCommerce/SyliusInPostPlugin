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
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\ShipmentInterface;

final class WebClientSpec extends ObjectBehavior
{
    const ORGANIZATION_ID = '123456';

    const API_ENDPOINT = 'https://api-shipx-pl.easypack24.net/v1';

    const POINT_NAME = 'AAA666';

    function let(Client $client): void
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(WebClient::class);
        $this->shouldImplement(WebClientInterface::class);
    }

    function it_creates_api_endpoint_for_shipment(
        ShippingGatewayInterface $shippingGateway,
        OrderInterface $order,
        ShipmentInterface $shipment,
        PaymentInterface $payment,
        PaymentMethod $paymentMethod,
        AddressInterface $shippingAddress,
        CustomerInterface $customer
    ): void {
        $shippingGateway->getConfigValue('service')->willReturn('inpost_locker_standard');
        $shippingGateway->getConfigValue('is_return')->willReturn(false);
        $shippingGateway->getConfigValue('insurance_amount')->willReturn(null);
        $shippingGateway->getConfigValue('additional_services')->willReturn([]);
        $shippingGateway->getConfigValue('cod_payment_method_code')->willReturn(null);
        $shippingGateway->getConfigValue('environment')->willReturn('sandbox');
        $shippingGateway->getConfigValue('access_token')->willReturn('1234567890.abcdefghij');
        $shippingGateway->getConfigValue('organization_id')->willReturn(self::ORGANIZATION_ID);
        $shippingGateway->getConfigValue('environment')->willReturn('https://sandbox-api-shipx-pl.easypack24.net');

        $customer->getId()->willReturn(1);
        $customer->getEmail()->willReturn('email@email.com');

        $shipment->getOrder()->willReturn($order);
        $shipment->getShippingWeight()->willReturn(1.0);
        $shipment->getId()->willReturn(1);

        $order->getCustomer()->willReturn($customer);
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getNumber()->willReturn(12);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTotal()->willReturn(100);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $order->getNotes()->willReturn('Notes');

        $payment->getMethod()->willReturn($paymentMethod);

        $shippingAddress->getCompany()->willReturn('Company name');
        $shippingAddress->getFirstName()->willReturn('First name');
        $shippingAddress->getLastName()->willReturn('Last name');
        $shippingAddress->getPhoneNumber()->willReturn('666666666');
        $shippingAddress->getStreet()->willReturn('Street 666');
        $shippingAddress->getCity()->willReturn('City');
        $shippingAddress->getPostcode()->willReturn('66-666');
        $shippingAddress->getCountryCode()->willReturn('PL');

        $this->setShippingGateway($shippingGateway);
    }

    public function it_creates_api_endpoint_url_for_shipment(ShippingGatewayInterface $shippingGateway) {
        $this->mockSetShippingGatewayMethod($shippingGateway);
        $this->getApiEndpointForShipment()->shouldReturn(self::API_ENDPOINT . '/organizations/' . self::ORGANIZATION_ID . '/shipments');
    }

    public function it_creates_api_endpoint_url_for_point() {
        $this->getApiEndpointForPointByName(self::POINT_NAME)->shouldReturn(self::API_ENDPOINT . '/points/' . self::POINT_NAME);
    }

    public function it_creates_api_endpoint_url_for_labels(ShippingGatewayInterface $shippingGateway) {
        $this->mockSetShippingGatewayMethod($shippingGateway);
        $this->getApiEndpointForLabels()->shouldReturn(self::API_ENDPOINT . '/organizations/' . self::ORGANIZATION_ID . '/shipments/labels');
    }

    public function it_creates_api_endpoint_url_for_organizations() {
        $this->getApiEndpointForOrganizations()->shouldReturn(self::API_ENDPOINT . '/organizations');
    }

    public function it_creates_api_endpoint_url_for_shipment_by_shipment_id() {
        $this->getApiEndpointForShipmentById(1)->shouldReturn(self::API_ENDPOINT . '/shipments/1');
    }

    private function mockSetShippingGatewayMethod(ShippingGatewayInterface $shippingGateway) {
        $shippingGateway->getConfigValue('access_token')->willReturn('1234567890.abcdefghij');
        $shippingGateway->getConfigValue('organization_id')->willReturn(self::ORGANIZATION_ID);
        $shippingGateway->getConfigValue('environment')->willReturn('https://sandbox-api-shipx-pl.easypack24.net');
        $this->setShippingGateway($shippingGateway);
    }
}
