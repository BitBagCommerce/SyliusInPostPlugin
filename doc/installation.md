# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
--- 
BACKEND
- [Entities](#entities)
    - [Attribute mapping](#attribute-mapping)
    - [XML mapping](#xml-mapping)
- [Controllers](#controllers)
---
FRONTEND
- [Templates](#templates)
- [Webpack](#webpack)
---
ADDITIONAL
- [Additional configuration](#additional-configuration)
- [Tests](#tests)
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.0          |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 18.x        |

---
## Composer:
```bash
composer require bitbag/inpost-plugin
```

---
## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\SyliusInPostPlugin\BitBagSyliusInPostPlugin::class  => ['all' => true],
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

bitbag_sylius_inpost_plugin:
    resource: "@BitBagSyliusInPostPlugin/Resources/config/routes.yml"
```

This plugin was made on top of [SyliusShippingExportPlugin](https://github.com/BitBagCommerce/SyliusShippingExportPlugin).
Usually, `Symfony Flex`, if you agree, automatically adds the configuration files:

- `bitbag_shipping_export_plugin.yaml` to the `config/packages` directory
- `bitbag_shipping_export_plugin.yaml` to the `config/routes` directory

It also adding the appropriate entry to `config/bundles.php`.

If it doesn't, so please remember to do the same as above for `SyliusShippingExportPlugin` configuration.

---
## Entities
You can implement entity configuration by using both xml-mapping and attribute-mapping. Depending on your preference, choose either one or the other:
### Attribute mapping
- [Attribute mapping configuration](installation/attribute-mapping.md)
### XML mapping
- [XML mapping configuration](installation/xml-mapping.md)

### Update your database
First, please run legacy-versioned migrations by using command:
```bash
bin/console doctrine:migrations:migrate
```

After migration, please create a new diff migration and update database:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

---
## Controllers
Add the controller from the following instruction:

- [Adding controllers](installation/controllers.md)

### Clear application cache by using command:
```bash
bin/console cache:clear
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

---
## Templates
Copy required templates into correct directories in your project.

**AdminBundle** (`templates/bundles/SyliusAdminBundle`):
```
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusAdminBundle/Order/Show/_addresses.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusAdminBundle/ShippingMethod/_form.html.twig
```

**ShopBundle** (`templates/bundles/SyliusShopBundle`):
```
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Checkout/SelectShipping/_choice.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Common/Order/_addresses.html.twig
vendor/bitbag/inpost-plugin/tests/Application/templates/bundles/SyliusShopBundle/Grid/Action/quickReturn.html.twig
```

---
## Webpack
### Webpack.config.js

Please setup your `webpack.config.js` file to require the plugin's webpack configuration. To do so, please put the line below somewhere on top of your webpack.config.js file:
```js
const [bitbagInPostShop, bitbagInPostAdmin] = require('./vendor/bitbag/inpost-plugin/webpack.config.js');
```
As next step, please add the imported consts into final module exports:
```js
module.exports = [..., bitbagInPostShop, bitbagInPostAdmin];
```

### Assets
Add the asset configuration into `config/packages/assets.yaml`:
```yaml
framework:
    assets:
        packages:
            ...
            inpost_shop:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/inpost/shop/manifest.json'
            inpost_admin:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/inpost/admin/manifest.json'
```

### Webpack Encore
Add the webpack configuration into `config/packages/webpack_encore.yaml`:

```yaml
webpack_encore:
    output_path: '%kernel.project_dir%/public/build/default'
    builds:
        ...
        inpost_admin: '%kernel.project_dir%/public/build/bitbag/inpost/admin'
        inpost_shop: '%kernel.project_dir%/public/build/bitbag/inpost/shop'
```

### Run commands
#### Install assets by using the following commands:
```bash
bin/console assets:install
bin/console sylius:theme:assets:install
```
or (may cause errors):
```bash
bin/console assets:install --symlink
bin/console sylius:theme:assets:install --symlink
```

#### Run webpack by using the following commands:
```bash
yarn install
yarn encore dev # or prod, depends on your environment
```



---
## Additional configuration
### Default parameters configuration
In the `.env` file, the default `parcel size` and `label type` can be specified by adding:

```dotenv
BITBAG_INPOST_DEFAULT_PARCEL_TEMPLATE='medium'
BITBAG_INPOST_DEFAULT_LABEL_TYPE='normal'
```

#### Three types of parcel templates are allowed:

- 'small'
- 'medium'
- 'large'

#### Two types of labels are allowed:

- 'normal'
- 'A6'

---
## Tests
### Testing & running the plugin

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

---
## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

### Other known issues
- [check the other known issues](known_issues.md)
