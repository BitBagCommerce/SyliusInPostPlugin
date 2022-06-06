<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;

class ShippingMethodContext implements Context
{

    private UpdatePageInterface $updatePage;

    public function __construct(
        UpdatePageInterface $updatePage
    ) {
        $this->updatePage = $updatePage;
    }

    /** @When I upload the :path image as shipping method logo */
    public function iUploadImageAsShippingMethodLogo(string $path)
    {
        $this->updatePage->attachFile($path);
    }
}
