<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusInPostPlugin\Behat\Mocker\InPostApiMocker;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Page\Admin\ShippingExport\IndexPageInterface;

final class ShippingExportContext implements Context
{
    private IndexPageInterface $indexPage;

    private InPostApiMocker $inPostApiMocker;

    public function __construct(
        IndexPageInterface $indexPage,
        InPostApiMocker $inPostApiMocker
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
}
