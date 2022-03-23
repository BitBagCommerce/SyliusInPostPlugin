<?php

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Controller;

use BitBag\SyliusInPostPlugin\Resolver\OrganizationIdResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class QuickReturnController extends AbstractController
{
    private OrganizationIdResolverInterface $organizationIdResolver;

    public function __construct(OrganizationIdResolverInterface $organizationIdResolver)
    {
        $this->organizationIdResolver = $organizationIdResolver;
    }

    public function redirectAction(Request $request): RedirectResponse
    {
       $orderId = $request->query->get('id');
       $organizationId = $this->organizationIdResolver->getOrganizationId((int)$orderId);

       if ('' === $organizationId) {
           throw new \Exception('Can not found config data');
       }

        return new RedirectResponse('https://www.szybkiezwroty.pl/'. $organizationId);
    }
}
