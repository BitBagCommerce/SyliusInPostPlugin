<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

final class ShippingGatewayContext implements Context
{
    private ProductVariantResolverInterface $defaultVariantResolver;

    private RepositoryInterface $orderRepository;

    private RepositoryInterface $shipmentRepository;

    private EntityManagerInterface $entityManager;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ProductVariantResolverInterface $productVariantResolver,
        RepositoryInterface $orderRepository,
        RepositoryInterface $shipmentRepository,
        EntityManagerInterface $entityManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->defaultVariantResolver = $productVariantResolver;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^the customer set the shipping address ("[^"]+" addressed it to "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+")) to orders$/
     */
    public function theCustomerSetTheAddressAddressedItToInTheToOrders(AddressInterface $address): void
    {
        $orders = $this->orderRepository->findAll();

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            $order->setShippingAddress(clone $address);
        }
    }

    /**
     * @Given set product weight to :weight
     */
    public function setProductWeightTo(float $weight): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);

        $productVariant->setWeight($weight);

        $this->entityManager->flush();
    }

    /**
     * @Given set units to the shipment
     */
    public function setUnitsToTheShipment(): void
    {
        $shipments = $this->shipmentRepository->findAll();

        /** @var ShipmentInterface $shipment */
        foreach ($shipments as $shipment) {
            if (null === $shipment->getOrder()) {
                continue;
            }
            /** @var OrderItemInterface $orderItem */
            foreach ($shipment->getOrder()->getItems() as $orderItem) {
                /** @var ShipmentUnitInterface $itemUnit */
                foreach ($orderItem->getUnits() as $itemUnit) {
                    $shipment->addUnit($itemUnit);
                }
            }
        }

        $this->entityManager->flush();
    }
}
