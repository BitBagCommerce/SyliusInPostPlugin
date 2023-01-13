<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\CompletePage as BaseCompletePage;

class CompletePage extends BaseCompletePage implements CompletePageInterface
{
    public function hasPointName(string $pointName): bool
    {
        return $this->hasElement('point_name', ['%pointName%' => $pointName]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'point_name' => '[data-test-point-name="%pointName%"]',
        ]);
    }
}
