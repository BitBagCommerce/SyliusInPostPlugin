<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Controller;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class AddPointToOrderAction
{
    private OrderRepositoryInterface $orderRepository;

    private FactoryInterface $inPostPointFactory;

    private EntityManagerInterface $entityManager;

    private WebClientInterface $client;

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
        /** @var InPostPointsAwareInterface $cart */
        $cart = $this->cartContext->getCart();
        Assert::isInstanceOf($cart, OrderInterface::class);

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            throw new BadRequestHttpException();
        }

        return $this->addPoint($request, $cart);
    }

    public function addPointToOrderAction(Request $request): Response
    {
        /** @var ?InPostPointsAwareInterface $order */
        $order = $this->orderRepository->findOneBy(['id' => $request->get('orderId')]);

        if (null === $order) {
            throw new NotFoundHttpException();
        }

        return $this->addPoint($request, $order);
    }

    private function addPoint(Request $request, InPostPointsAwareInterface $order): Response
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
            /** @var InPostPointInterface $point */
            $point = $this->inPostPointFactory->createNew();
        }

        $point->setName($name);

        $order->setPoint($point);

        $this->entityManager->flush();

        return new JsonResponse($pointData);
    }
}
