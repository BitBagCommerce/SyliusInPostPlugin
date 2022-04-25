<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\TwigExtension;

use BitBag\SyliusInPostPlugin\Resolver\IsQuickReturnResolverInterface;
use BitBag\SyliusInPostPlugin\Resolver\OrganizationIdResolverInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ShippingInpostCodeExtension extends AbstractExtension
{
    public const ULR_TO_INPOST_QUICK_RETURN = "https://www.szybkiezwroty.pl/";

    public IsQuickReturnResolverInterface $isQuickReturnResolver;

    private OrganizationIdResolverInterface $organizationIdResolver;


    public function __construct(
        IsQuickReturnResolverInterface $isQuickReturnResolver,
        OrganizationIdResolverInterface $organizationIdResolver
    ) {
        $this->isQuickReturnResolver = $isQuickReturnResolver;
        $this->organizationIdResolver = $organizationIdResolver;

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('quick_return', [$this, 'isInpostShippingCode']),
        ];
    }

    public function isInpostShippingCode(): string
    {
        $isQuickReturn = $this->isQuickReturnResolver->getIsQuickReturn();
        $organizationId = $this->organizationIdResolver->getOrganizationId();

        if ($isQuickReturn && $organizationId !== "") {
                return sprintf("%s%s",self::ULR_TO_INPOST_QUICK_RETURN, $organizationId);
        }

        return "";
    }
}
