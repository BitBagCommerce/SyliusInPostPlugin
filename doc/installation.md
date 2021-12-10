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

This plugin was made on top of [SyliusShippingExportPlugin](https://github.com/BitBagCommerce/SyliusShippingExportPlugin), so please remember to do the same for it's configuration.

Add trait to your Order entity class:
```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Sylius\Component\Core\Model\Order as BaseOrder;
use BitBag\SyliusInPostPlugin\Model\OrderPointTrait;

class Order extends BaseOrder
{
    use OrderPointTrait;
}
```

Define new Entity mapping inside your src/Resources/config/doctrine directory. (You can do it with annotations if you prefer)

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping
        xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                            http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
>
    <entity name="Tests\BitBag\SyliusInPostPlugin\Application\src\Entity\Order" table="sylius_order">

        <one-to-one field="point" target-entity="BitBag\SyliusInPostPlugin\Entity\Point">
            <cascade>
                <cascade-persist />
                <cascade-remove />
                <cascade-refresh />
            </cascade>
            <join-column name="point_id" referenced-column-name="id" nullable="true" />
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

## Testing & running the plugin
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
$ vendor/bin/phpspec run
```
