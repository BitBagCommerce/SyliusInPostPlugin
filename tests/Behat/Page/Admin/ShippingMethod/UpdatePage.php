<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\ShippingMethod\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function attachFile(string $path): void
    {
        $filesPath = $this->getParameter('files_path');

        Assert::notEmpty($filesPath);

        $this->getDocument()->attachFileToField(
            'sylius_shipping_method_image_file',
            $filesPath . $path,
        );
    }
}
