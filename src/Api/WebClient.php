<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Api;

use BitBag\SyliusInPostPlugin\Entity\InPostPoint;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Webmozart\Assert\Assert;

final class WebClient implements WebClientInterface
{
    private Client $apiClient;

    private ?string $organizationId = null;

    private ?string $accessToken = null;

    private ?string $environment = null;

    private ShippingGatewayInterface $shippingGateway;

    public function __construct(Client $client)
    {
        $this->apiClient = $client;
    }

    public function setShippingGateway(ShippingGatewayInterface $shippingGateway): WebClientInterface
    {
        $this->shippingGateway = $shippingGateway;

        $this->accessToken = $shippingGateway->getConfigValue('access_token');
        $this->organizationId = $shippingGateway->getConfigValue('organization_id');
        $this->environment = $shippingGateway->getConfigValue('environment');

        return $this;
    }

    public function getPointByName(string $name, int $attempts = 0): ?array
    {
        $url = $this->getApiEndpointForPointByName($name);

        try {
            return $this->request('GET', $url);
        } catch (\Exception $exception) {
            if ($attempts < 3) {
                sleep(1);

                return $this->getPointByName($name, ($attempts + 1));
            }
        }

        return null;
    }

    public function getApiEndpoint(): string
    {
        $apiEndpoint = self::SANDBOX_ENVIRONMENT === $this->environment ? self::SANDBOX_API_ENDPOINT : self::PRODUCTION_API_ENDPOINT;

        return sprintf('%s/%s', $apiEndpoint, self::API_VERSION);
    }

    public function getApiEndpointForShipment(): string
    {
        return sprintf('%s/organizations/%s/shipments', $this->getApiEndpoint(), $this->organizationId);
    }

    public function getApiEndpointForPointByName(string $name): string
    {
        return sprintf('%s/points/%s', $this->getApiEndpoint(), $name);
    }

    public function getApiEndpointForOrganizations(): string
    {
        return sprintf('%s/organizations', $this->getApiEndpoint());
    }

    public function getOrganizations(): array
    {
        $url = $this->getApiEndpointForOrganizations();

        return $this->request('GET', $url);
    }

    public function getApiEndpointForLabels(): string
    {
        return sprintf('%s/organizations/%s/shipments/labels', $this->getApiEndpoint(), $this->organizationId);
    }

    public function getApiEndpointForShipmentById(int $id): string
    {
        return sprintf('%s/shipments/%s', $this->getApiEndpoint(), $id);
    }

    public function getShipmentById(int $id): ?array
    {
        $url = $this->getApiEndpointForShipmentById($id);

        return $this->request('GET', $url);
    }

    public function getLabels(array $shipmentIds): ?string
    {
        $url = $this->getApiEndpointForLabels();

        $data = [
            'format' => 'pdf',
            'shipment_ids' => $shipmentIds,
        ];

        return $this->request('POST', $url, $data, false);
    }

    public function getShipments(): ?array
    {
        $url = $this->getApiEndpointForShipment();

        return $this->request('GET', $url);
    }

    public function createShipment(ShipmentInterface $shipment): array
    {
        /** @var OrderInterface $order */
        $order = $shipment->getOrder();

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        $data = [
            'external_customer_id' => $customer->getId(),
            'receiver' => $this->createReceiverDetails($order),
            'custom_attributes' => $this->createCustomAttributes($shipment),
            'parcels' => [$this->createParcel($shipment)],
            'service' => $this->getShippingGatewayConfig('service'),
            'additional_services' => $this->getAdditionalServices(),
            'reference' => 'Order: ' . $order->getNumber(),
            'comments' => $this->resolveComment($order),
            'is_return' => $this->shippingGateway->getConfigValue('is_return'),
        ];

        if (null !== $this->getShippingGatewayConfig('insurance_amount')) {
            $data['insurance'] = [
                'amount' => $this->getShippingGatewayConfig('insurance_amount'),
                'currency' => $order->getCurrencyCode(),
            ];
        }

        if (true === $this->isCashOnDelivery($order)) {
            $value = $order->getTotal();

            $data['cod'] = [
                'amount' => $value / 100,
                'currency' => $order->getCurrencyCode(),
            ];
        }

        $url = $this->getApiEndpointForShipment();

        return $this->request('POST', $url, $data);
    }

    public function getAuthorizedHeaderWithContentType(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->accessToken),
        ];
    }

    /**
     * @return mixed|string
     *
     * @throws GuzzleException
     */
    public function request(string $method, string $url, array $data = [], bool $returnJson = true)
    {
        $options = [
            'json' => $data,
            'headers' => $this->getAuthorizedHeaderWithContentType(),
        ];

        try {
            $result = $this->apiClient->request($method, $url, $options);
        } catch (ClientException $exception) {
            /** @var ?ResponseInterface $result */
            $result = $exception->getResponse();

            throw new ClientException(null !== $result ? (string) $result->getBody() : 'Request failed for url' . $url, $exception->getRequest());
        }

        if (false === $returnJson) {
            return (string) $result->getBody();
        }

        return \GuzzleHttp\json_decode((string) $result->getBody(), true);
    }

    private function getAdditionalServices(): array
    {
        $additionalServices = $this->getShippingGatewayConfig('additional_services');

        if (self::INPOST_LOCKER_STANDARD_SERVICE === $this->getShippingGatewayConfig('service')) {
            foreach ($additionalServices as $key => $service) {
                if (self::SMS_ADDITIONAL_SERVICE === $service || self::EMAIL_ADDITIONAL_SERVICE === $service) {
                    unset($additionalServices[$key]);
                }
            }
        }

        return $additionalServices;
    }

    private function createCustomAttributes(ShipmentInterface $shipment): array
    {
        /** @var ?InPostPointsAwareInterface $order */
        $order = $shipment->getOrder();
        Assert::notNull($order);

        /** @var ?InPostPoint $point */
        $point = $order->getPoint();

        if (null === $point) {
            return [];
        }

        return [
            'target_point' => $point->getName(),
        ];
    }

    private function createReceiverDetails(OrderInterface $order): array
    {
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        return [
            'company_name' => $shippingAddress->getCompany(),
            'first_name' => $shippingAddress->getFirstName(),
            'last_name' => $shippingAddress->getLastName(),
            'email' => $customer->getEmail(),
            'phone' => $shippingAddress->getPhoneNumber(),
            'address' => [
                'street' => $shippingAddress->getStreet(),
                'building_number' => $this->resolveHouseNumber($shippingAddress),
                'city' => $shippingAddress->getCity(),
                'post_code' => $shippingAddress->getPostcode(),
                'country_code' => $shippingAddress->getCountryCode(),
            ],
        ];
    }

    private function isCashOnDelivery(OrderInterface $order): bool
    {
        $codPaymentMethodCode = $this->getShippingGatewayConfig('cod_payment_method_code');

        $payments = $order->getPayments();

        foreach ($payments as $payment) {
            Assert::notNull($payment->getMethod());

            return $codPaymentMethodCode === $payment->getMethod()->getCode();
        }

        return false;
    }

    private function createParcel(ShipmentInterface $shipment): array
    {
        $weight = $shipment->getShippingWeight();

        return [
            'id' => $shipment->getId(),
            'weight' => [
                'amount' => $weight,
                'unit' => 'kg',
            ],
            'dimensions' => [],
            'template' => 'large',
            'tracking_number' => null,
            'is_non_standard' => false,
        ];
    }

    /** @return mixed */
    private function getShippingGatewayConfig(string $config)
    {
        return $this->shippingGateway->getConfigValue($config);
    }

    private function resolveHouseNumber(AddressInterface $address): string
    {
        $street = $address->getStreet();
        Assert::notNull($street);

        $streetParts = explode(' ', $street);

        Assert::greaterThan(count($streetParts), 0, sprintf(
            'Street "%s" is invalid. The street format must be something like %s, where %d is the house number.',
            $street,
            '"Opolska 45"',
            45
        ));

        return end($streetParts);
    }

    private function resolveComment(OrderInterface $order): string
    {
        $comments = $order->getNotes();

        if (null === $comments) {
            $comments = '';
        }

        if (strlen($comments) >= 100) {
            $comments = substr($comments, 0, 97) . '...';
        }

        return $comments;
    }
}
