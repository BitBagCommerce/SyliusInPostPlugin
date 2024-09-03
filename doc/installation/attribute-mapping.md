# Attribute-mapping

Check the mapping settings in `config/packages/doctrine.yaml` and, if necessary, change them accordingly.
```yaml
doctrine:
    # ...
    orm:
        entity_managers:
            default:
                # ...
                mappings:
                    App:
                        # ...
                        type: attribute
```

Extend entities with parameters and methods using attributes and traits:

- `Order` entity

`src/Entity/Order/Order.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

use BitBag\SyliusInPostPlugin\Entity\InPostPoint;
use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use BitBag\SyliusInPostPlugin\Model\OrderPointTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements InPostPointsAwareInterface
{
    use OrderPointTrait;

    #[ORM\OneToOne(targetEntity: InPostPoint::class, cascade: ['persist', 'remove', 'refresh'])]
    #[ORM\JoinColumn(name: 'point_id', referencedColumnName: 'id', nullable: true)]
    protected ?InPostPointInterface $point = null;
}

```

- `ShippingMethod` entity

`src/Entity/Shipping/ShippingMethod.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingMethodImage;
use BitBag\SyliusInPostPlugin\Model\ShippingMethodImageTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipping_method")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
class ShippingMethod extends BaseShippingMethod implements ImageAwareInterface
{
    use ShippingMethodImageTrait;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: ShippingMethodImage::class, cascade: ['all'])]
    protected ?ImageInterface $image = null;

    // other methods
}
```

- `ShippingExport` entity

`src/Entity/Shipping/ShippingExport.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\Model\ParcelTemplateTrait;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bitbag_shipping_export")
 */
#[ORM\Entity]
#[ORM\Table(name: 'bitbag_shipping_export')]
class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
    use ParcelTemplateTrait;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $parcel_template = null;
}

```

Add to `config/packages/bitbag_shipping_export_plugin.yaml` below the imports.
```yaml
# config/packages/bitbag_shipping_export_plugin.yaml

imports:
    ...
sylius_resource:
    resources:
        bitbag.shipping_export:
            classes:
                model: App\Entity\Shipping\ShippingExport
```
