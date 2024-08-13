## Installation

```bash
$ composer require bitbag/inpost-plugin
```

Add plugin dependencies to your `config/bundles.php` file:

```php
return [
    ...
    
    BitBag\SyliusInPostPlugin\BitBagSyliusInPostPlugin::class  => ['all' => true]
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
  ...

  - { resource: "@BitBagSyliusInPostPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

bitbag_sylius_inpost_plugin:
  resource: "@BitBagSyliusInPostPlugin/Resources/config/routes.yml"
```

This plugin was made on top
of [SyliusShippingExportPlugin](https://github.com/BitBagCommerce/SyliusShippingExportPlugin).
Typically, Symfony Flex, if you agree, automatically adds the configuration files:
bitbag_shipping_export_plugin.yaml to the config/packages and config/routes directories.
It also adding the appropriate entry to config/bundles.php.
If it doesn't, so please remember to do the same as above for SyliusShippingExportPlugin configuration.

### Create a new controller:
```php
// src/Controller/ShippingExportController

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
    public const SELECT_PARCEL_TEMPLATE_EVENT = 'export_shipping_select_parcel_template';

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

Complete the **config/packages/bitbag_shipping_export_plugin.yaml** file with the following data:

```yaml
# config/packages/bitbag_shipping_export_plugin.yaml

imports:
    - { resource: "@BitBagSyliusShippingExportPlugin/Resources/config/config.yml" }

sylius_resource:
    resources:
        bitbag.shipping_export:
            classes:
                model: App\Entity\Shipping\ShippingExport
                controller: App\Controller\ShippingExportController

```
Remember that in case of different mapping, the model path may be different.
Default:
```yaml
                model: App\Entity\ShippingExport
```

### Extend entities with parameters

You can implement this using xml-mapping or attributes. Instructions for both settings are described below.

#### For doctrine XML-MAPPING:
If you are using doctrine xml-mapping, you have probably already removed the entries
for the entity in config/_sylius.yaml and all entity extensions are in src/Entity.
Add the following entries to config/_sylius.yaml:
```
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

Add trait and interface to your Order and ShippingMethod entity classes:

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
Remember to mark it appropriately in the config/doctrine.yaml configuration file.
```
doctrine:
    ...
    orm:
        ...
        mappings:
            App:
                ...
                type: xml
                dir: '%kernel.project_dir%/src/Resources/config/doctrine'
```
Define new Entity mapping inside your src/Resources/config/doctrine directory.

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
#### You can do it with attributes if you prefer. Remember to mark it appropriately in the config/doctrine.yaml configuration file.
```
doctrine:
    ...
    orm:
        ...
        mappings:
            App:
                ...
                type: attribute

```
```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

use BitBag\SyliusInPostPlugin\Entity\InPostPoint;
use BitBag\SyliusInPostPlugin\Entity\InPostPointInterface;
use BitBag\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
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
    #[ORM\OneToOne(targetEntity: InPostPoint::class, cascade: ['persist', 'remove', 'refresh'])]
    #[ORM\JoinColumn(name: 'point_id', referencedColumnName: 'id', nullable: true)]
    protected ?InPostPointInterface $point = null;

    public function getPoint(): ?InPostPointInterface
    {
        return $this->point;
    }

    public function setPoint(?InPostPointInterface $point): void
    {
        $this->point = $point;
    }
}

```

```php
<?php

declare(strict_types=1);

namespace App\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingMethodImage;
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
    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: ShippingMethodImage::class, cascade: ['all'])]
    protected ?ImageInterface $image = null;

    public function getImage(): ?ImageInterface
    {
        return $this->image;
    }

    public function setImage(?ImageInterface $image): void
    {
        $this->image = $image;
    }
    
    // other methods
}

```
```php
<?php

declare(strict_types=1);

namespace App\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Entity\ShippingExportInterface;
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
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $parcel_template = null;

    public function getParcelTemplate(): ?string
    {
        return $this->parcel_template;
    }

    public function setParcelTemplate(?string $parcel_template): void
    {
        $this->parcel_template = $parcel_template;
    }
}
```

Finish the installation by updating the database schema (check in advance: [Known Issues](known_issues.md)):

```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
$ bin/console assets:install --symlink
$ bin/console sylius:theme:assets:install --symlink
```
### TWIG configuration
#### Adding files to the template
Copy the files below to the appropriate directories in your template.

```
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusAdminBundle/Order/Show/_addresses.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusAdminBundle/ShippingMethod/_form.html.twig
```
```
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Checkout/SelectShipping/_choice.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Common/Order/_addresses.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Grid/Action/quickReturn.html.twig
```

### Webpack configuration
#### Installing Webpack package

1. Before Webpack installation, please create the `config/packages/webpack_encore.yaml` file with a content of:

    ```yaml
    webpack_encore:
        output_path: '%kernel.project_dir%/public/build/default'
        builds:
            admin: '%kernel.project_dir%/public/build/admin'
            shop: '%kernel.project_dir%/public/build/shop'
            app.admin: '%kernel.project_dir%/public/build/app/admin'
            app.shop: '%kernel.project_dir%/public/build/app/shop'
            inpost_admin: '%kernel.project_dir%/public/build/bitbag/inpost/admin'
            inpost_shop: '%kernel.project_dir%/public/build/bitbag/inpost/shop'
    ```

2. To install Webpack in your application, please run the command below:

    ```bash
    $ composer require "symfony/webpack-encore-bundle"
    ```

3. After installation, please add the line below into `config/bundles.php` file:

    ```php
    return [
        ...
        Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    ];
    ```
#### Configuring Webpack

By a standard, the `webpack.config.js` file should be available in your repository. If not, please use [the Sylius-Standard one](https://github.com/Sylius/Sylius-Standard/blob/1.12/webpack.config.js).

1. Please setup your `webpack.config.js` file to require the plugin's webpack configuration. To do so, please put the line below somewhere on top of your `webpack.config.js` file:

    ```javascript
    const [bitbagInPostShop, bitbagInPostAdmin] = require('./vendor/bitbag/inpost-plugin/webpack.config.js');
    ```

2. As next step, please add the imported consts into final module exports:

    ```javascripts
    module.exports = [shopConfig, adminConfig, appShopConfig, appAdminConfig, bitbagInPostShop, bitbagInPostAdmin];
    ```

3. Next thing is to add the asset configuration into `config/packages/assets.yaml`:

    ```yaml
    framework:
        assets:
            packages:
                admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/admin/manifest.json'
                shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/shop/manifest.json'
                app.admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/app/admin/manifest.json'
                app.shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/app/shop/manifest.json'
                inpost_shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/bitbag/inpost/shop/manifest.json'
                inpost_admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/bitbag/inpost/admin/manifest.json'
    ```
4. Additionally, please add the `"@symfony/webpack-encore": "^1.5.0",` dependency into your `package.json` file.

5. Now you can run the commands:

    ```bash
    $ yarn install
    $ yarn encore dev # or prod, depends on your environment
    ```


## Default parameters configuration
In the .env file, the default parcel size and label type can be specified by adding:

````
BITBAG_INPOST_DEFAULT_PARCEL_TEMPLATE='medium'
BITBAG_INPOST_DEFAULT_LABEL_TYPE='normal'
````

Three types of parcel templates are allowed:
- 'small'
- 'medium'
- 'large'

Two types of labels are allowed:
- 'normal'
- 'A6'


## Testing & running the plugin

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn encore dev
$ bin/console assets:install public -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
$ vendor/bin/phpspec run
```
