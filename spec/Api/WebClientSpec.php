<?php


namespace spec\BitBag\SyliusInPostPlugin\Api;


use BitBag\SyliusInPostPlugin\Api\WebClient;
use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use GuzzleHttp\Client;

final class WebClientSpec extends ObjectBehavior
{
    function let(Client $client): void
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(WebClient::class);
        $this->shouldHaveType(WebClientInterface::class);
    }

    function it_creates_request_data_shipment(
        ShippingGatewayInterface $shippingGateway,
        OrderInterface $order,
        ShipmentInterface $shipment,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        PaymentInterface $payment,
        PaymentMethod $paymentMethod,
        AddressInterface $address,
        TaxonInterface $taxon
    ): void {
        $shippingGateway->getConfigValue('access_token')->willReturn('https://sandbox.dhl24.com.pl/webapi2');
        $shippingGateway->getConfigValue('organization_id')->willReturn('https://sandbox.dhl24.com.pl/webapi2');
        $shippingGateway->getConfigValue('environment')->willReturn('https://sandbox.dhl24.com.pl/webapi2');

        $taxon->getName()->willReturn('test');

        $product->getMainTaxon()->willReturn($taxon);

        $orderItem->getProduct()->willReturn($product);

        $paymentMethod->getCode()->willReturn('stripe_checkout');
        $payment->getMethod()->willReturn($paymentMethod);

        $address->getCountryCode()->willReturn('PL');
        $address->getFullName()->willReturn('Janek');
        $address->getPostcode()->willReturn(22222);
        $address->getStreet()->willReturn('Leśna 9');
        $address->getCity()->willReturn('Wawa');
        $address->getPhoneNumber()->willReturn(123456789);

        $order->getShippingAddress()->willReturn($address);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));
        $order->getTotal()->willReturn(77000);

        $shipment->getOrder()->willReturn($order);
        $shipment->getShippingWeight()->willReturn(20);

        $this->setShippingGateway($shippingGateway);
//        $this->setShipment($shipment);

//        $this->getRequestData();
    }
}