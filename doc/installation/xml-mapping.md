# XML-mapping

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
                        type: xml
                        dir: '%kernel.project_dir%/src/Resources/config/doctrine'
```

Extend entities with parameters and methods using attributes and traits:
- `Order` entity

`src/Entity/Order.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use Sylius\Component\Core\Model\Order as BaseOrder;
use BitBag\SyliusInPostPlugin\Model\OrderPointTrait;

class Order extends BaseOrder implements InPostPointsAwareInterface
{
    use OrderPointTrait;
}
```

- `ShippingMethod` entity

`src/Entity/ShippingMethod.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use BitBag\SyliusInPostPlugin\Model\ShippingMethodImageTrait;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

class ShippingMethod extends BaseShippingMethod implements ImageAwareInterface
{
    use ShippingMethodImageTrait;
}
```

- `ShippingExport` entity

`src/Entity/ShippingExport.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusInPostPlugin\Model\ParcelTemplateTrait;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExport as BaseShippingExport;

class ShippingExport extends BaseShippingExport implements ShippingExportInterface
{
    use ParcelTemplateTrait;
}
```

Define new Entity mapping inside `src/Resources/config/doctrine` directory.

- **Order entity:**

`src/Resources/config/doctrine/Order.orm.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
>
    <entity name="App\Entity\Order" table="sylius_order">

        <one-to-one field="point" target-entity="BitBag\SyliusInPostPlugin\Entity\InPostPoint">
            <cascade>
                <cascade-persist />
                <cascade-remove />
                <cascade-refresh />
            </cascade>
            <join-column name="point_id" referenced-column-name="id" nullable="true"/>
        </one-to-one>

    </entity>
</doctrine-mapping>
```

- **Shipping method entity:**

`src/Resources/config/doctrine/ShippingMethod.orm.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="App\Entity\ShippingMethod" table="sylius_shipping_method">
        <one-to-one field="image" target-entity="BitBag\SyliusInPostPlugin\Entity\ShippingMethodImage" mapped-by="owner">
            <cascade>
                <cascade-all />
            </cascade>
            <join-column name="image_id" referenced-column-name="id" nullable="false" on-delete="CASCADE"/>
        </one-to-one>
    </entity>
</doctrine-mapping>
```

- **Shipping export entity:**

`src/Resources/config/doctrine/ShippingExport.orm.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="App\Entity\ShippingExport" table="bitbag_shipping_export">
        <field name="parcel_template" nullable="true" />
    </entity>
</doctrine-mapping>
```

Override `config/packages/_sylius.yaml` configuration:
```yaml
# config/_sylius.yaml

sylius_order:
    resources:
        order:
            classes:
                model: App\Entity\Order

sylius_shipping:
    resources:
        shipping_method:
            classes:
                model: App\Entity\ShippingMethod
```

Add to `config/packages/bitbag_shipping_export_plugin.yaml` file:
```yaml
# config/packages/bitbag_shipping_export_plugin.yaml

imports:
    # ...
sylius_resource:
    resources:
        bitbag.shipping_export:
            classes:
                model: App\Entity\ShippingExport
```
