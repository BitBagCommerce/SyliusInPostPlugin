# Controllers

Add `ShippingExportController` with following trait:

`src/Controller/ShippingExportController.php`

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use BitBag\SyliusInPostPlugin\Controller\SelectParcelTemplateTrait;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Webmozart\Assert\Assert;

final class ShippingExportController extends ResourceController
{
    public const SELECT_PARCEL_TEMPLATE_EVENT = 'select_parcel_template';

    use SelectParcelTemplateTrait;

    public function exportAllNewShipmentsAction(Request $request): RedirectResponse
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        Assert::implementsInterface($this->repository, ShippingExportRepositoryInterface::class);
        $shippingExports = $this->repository->findAllWithNewOrPendingState();

        if (0 === count($shippingExports)) {
            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('error', 'bitbag.ui.no_new_shipments_to_export');

            return $this->redirectToReferer($request);
        }

        foreach ($shippingExports as $shippingExport) {
            $this->eventDispatcher->dispatch(
                ExportShipmentEvent::SHORT_NAME,
                $configuration,
                $shippingExport,
            );
        }

        return $this->redirectToReferer($request);
    }

    public function exportSingleShipmentAction(Request $request): RedirectResponse
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var ResourceInterface|null $shippingExport */
        $shippingExport = $this->repository->find($request->get('id'));
        Assert::notNull($shippingExport);

        $this->eventDispatcher->dispatch(
            ExportShipmentEvent::SHORT_NAME,
            $configuration,
            $shippingExport,
        );

        return $this->redirectToReferer($request);
    }

    private function redirectToReferer(Request $request): RedirectResponse
    {
        $referer = $request->headers->get('referer');
        if (null !== $referer) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute($request->attributes->get('_route'));
    }
}
```

Add controller in `config/packages/bitbag_shipping_export_plugin.yaml` configuration:
```yaml
# config/packages/bitbag_shipping_export_plugin.yaml

imports:
    ...
sylius_resource:
    resources:
        bitbag.shipping_export:
            classes:
                ...
                controller: App\Controller\ShippingExportController
```
