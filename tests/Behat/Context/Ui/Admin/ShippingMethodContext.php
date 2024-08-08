<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingMethod\UpdatePageInterface;

class ShippingMethodContext implements Context
{
    private UpdatePageInterface $updatePage;

    public function __construct(
        UpdatePageInterface $updatePage,
    ) {
        $this->updatePage = $updatePage;
    }

    /** @When I upload the :path image as shipping method logo */
    public function iUploadImageAsShippingMethodLogo(string $path)
    {
        $this->updatePage->attachFile($path);
    }
}
