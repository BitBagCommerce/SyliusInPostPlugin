<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface as BaseCompletePageInterface;

interface CompletePageInterface extends BaseCompletePageInterface
{
    public function hasPointName(string $pointName): bool;
}
