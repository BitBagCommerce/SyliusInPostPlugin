<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
            $filesPath . $path
        );
    }
}