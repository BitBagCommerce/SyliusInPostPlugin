<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

trait SelectParcelTemplateTrait
{
    public function selectParcelTemplate(Request $request): RedirectResponse
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var ResourceInterface|null $shippingExport */
        $shippingExport = $this->repository->find($request->get('id'));
        Assert::notNull($shippingExport);
        $shippingExport->setParcelTemplate($request->get('template'));

        $this->eventDispatcher->dispatch(
            self::SELECT_PARCEL_TEMPLATE_EVENT,
            $configuration,
            $shippingExport,
        );

        return $this->redirectToReferer($request);
    }
}
