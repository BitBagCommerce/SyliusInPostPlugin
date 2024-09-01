<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\TwigExtension;

use BitBag\SyliusInPostPlugin\Resolver\IsQuickReturnResolverInterface;
use BitBag\SyliusInPostPlugin\Resolver\OrganizationIdResolverInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ShippingInpostCodeExtension extends AbstractExtension
{
    public const ULR_TO_INPOST_QUICK_RETURN = 'https://www.szybkiezwroty.pl/';

    public IsQuickReturnResolverInterface $isQuickReturnResolver;

    private OrganizationIdResolverInterface $organizationIdResolver;

    public function __construct(
        IsQuickReturnResolverInterface $isQuickReturnResolver,
        OrganizationIdResolverInterface $organizationIdResolver,
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

        if ($isQuickReturn && '' !== $organizationId) {
            return sprintf('%s%s', self::ULR_TO_INPOST_QUICK_RETURN, $organizationId);
        }

        return '';
    }
}
