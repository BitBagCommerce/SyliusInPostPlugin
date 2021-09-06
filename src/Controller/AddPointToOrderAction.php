<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AddPointToOrderAction
{
    /** @var OrderRepositoryInterface */
    private OrderRepositoryInterface $orderRepository;

    /** @var FactoryInterface */
    private FactoryInterface $inPostPointFactory;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var WebClientInterface */
    private WebClientInterface $client;

    /** @var CartContextInterface */
    private CartContextInterface $cartContext;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $inPostPointFactory,
        EntityManagerInterface $entityManager,
        WebClientInterface $client,
        CartContextInterface $cartContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->inPostPointFactory = $inPostPointFactory;
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->cartContext = $cartContext;
    }

    public function addPointToCartAction(Request $request): Response
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();

        if (null === $cart) {
            throw new NotFoundHttpException();
        }

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            throw new BadRequestHttpException();
        }

        return $this->addPoint($request, $cart);
    }

    public function addPointToOrderAction(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($request->get('orderId'));

        if (null === $order) {
            throw new NotFoundHttpException();
        }

        return $this->addPoint($request, $order);
    }

    private function addPoint(Request $request, OrderInterface $order): Response
    {
        $name = $request->get('name');

        try {
            $pointData = $this->client->getPointByName($name);
        } catch (ClientException $exception) {
            $data = \GuzzleHttp\json_decode($exception->getMessage(), true);

            return new JsonResponse($data['message'], Response::HTTP_BAD_REQUEST);
        }

        $point = $order->getPoint();

        if (null === $point) {
            $point = $this->inPostPointFactory->createNew();
        }

        $point->setName($name);

        $order->setPoint($point);

        $this->entityManager->flush();

        return new JsonResponse($pointData);
    }
}
