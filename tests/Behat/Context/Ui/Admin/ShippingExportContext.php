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
use Tests\BitBag\SyliusInPostPlugin\Behat\Mocker\InPostApiMocker;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingExport\IndexPageInterface;

final class ShippingExportContext implements Context
{
    private IndexPageInterface $indexPage;

    private InPostApiMocker $inPostApiMocker;

    public function __construct(
        IndexPageInterface $indexPage,
        InPostApiMocker $inPostApiMocker,
    ) {
        $this->inPostApiMocker = $inPostApiMocker;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I export all new shipments to inpost api
     */
    public function iExportAllNewShipments(): void
    {
        $this->inPostApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportAllShipments();
        });
    }

    /**
     * @When I export first shipment to inpost api
     */
    public function iExportFirsShipments(): void
    {
        $this->inPostApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportFirsShipment();
        });
    }

    /**
     * @When I select parcel template
     */
    public function iSelectParcelTemplate(): void
    {
        $this->indexPage->selectParcelTemplate();
    }

    /**
     * @Then I should see that shipping export parcel template is set
     */
    public function iCheckParcelTemplate(): void
    {
        $this->indexPage->checkParcelTemplate();
    }
}
