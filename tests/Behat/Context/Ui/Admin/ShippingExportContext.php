<?php

/*
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\SyliusInPostPlugin\Behat\Mocker\InpostApiMocker;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Page\Admin\ShippingExport\IndexPageInterface;

final class ShippingExportContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var InpostApiMocker */
    private $DHLApiMocker;

    public function __construct(
        IndexPageInterface $indexPage,
        InpostApiMocker $DHLApiMocker
    ) {
        $this->DHLApiMocker = $DHLApiMocker;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I export all new shipments to dhl api
     */
    public function iExportAllNewShipments(): void
    {
        $this->DHLApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportAllShipments();
        });
    }

    /**
     * @When I export first shipment to dhl api
     */
    public function iExportFirsShipments(): void
    {
        $this->DHLApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportFirsShipment();
        });
    }
}
