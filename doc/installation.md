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
of [SyliusShippingExportPlugin](https://github.com/BitBagCommerce/SyliusShippingExportPlugin), so please remember to do
the same for it's configuration.

Add trait and interface to your Order and ShippingMethod entity classes:

```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

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

namespace App\Entity\Shipping;

use BitBag\SyliusInPostPlugin\Model\ShippingMethodImageTrait;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

class ShippingMethod extends BaseShippingMethod implements ImageAwareInterface
{
    use ShippingMethodImageTrait;
}
```

Define new Entity mapping inside your src/Resources/config/doctrine directory. (You can do it with annotations if you
prefer)

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping
        xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                            http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
>
    <entity name="Tests\BitBag\SyliusInPostPlugin\Application\src\Entity\Order" table="sylius_order">

        <one-to-one field="point" target-entity="BitBag\SyliusInPostPlugin\Entity\InPostPoint">
            <cascade>
                <cascade-persist/>
                <cascade-remove/>
                <cascade-refresh/>
            </cascade>
            <join-column name="point_id" referenced-column-name="id" nullable="true"/>
        </one-to-one>

    </entity>
</doctrine-mapping>
```
```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping
        xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                            http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
>
    <entity name="Tests\BitBag\SyliusInPostPlugin\Application\src\Entity\ShippingMethodImage" table="bitbag_inpost_shipping_method_image">
        <one-to-one field="owner" target-entity="Sylius\Component\Shipping\Model\ShippingMethodInterface" inversed-by="image">
            <join-column name="owner_id" referenced-column-name="id" nullable="false" on-delete="CASCADE"/>
        </one-to-one>
    </entity>
    
</doctrine-mapping>
```
Finish the installation by updating the database schema:

```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
$ bin/console assets:install --symlink
$ bin/console sylius:theme:assets:install --symlink
```

### Webpack configuration
#### Installing Webpack package

1. Before Webpack installation, please create the `config/packages/webpack_encore.yaml` file with a content of:

    ```yaml
    webpack_encore:
        output_path: '%kernel.project_dir%/public/build/default'
        builds:
            shop: '%kernel.project_dir%/public/build/shop'
            admin: '%kernel.project_dir%/public/build/admin'
            app.admin: '%kernel.project_dir%/public/build/app/admin'
            app.shop: '%kernel.project_dir%/public/build/app/shop'
            inpost_shop: '%kernel.project_dir%/public/build/bitbag/inpost/shop'
            inpost_admin: '%kernel.project_dir%/public/build/bitbag/inpost/admin'
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

3. Next thing is to add the asset configuration into `config/packages/framework.yaml`:

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
