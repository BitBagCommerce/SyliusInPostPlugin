<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use Webmozart\Assert\Assert;

final class InPostShippingExportActionProvider implements InPostShippingExportActionProviderInterface
{
    private const NO_ACTION_FOUND = 'No action found for status code "%s"';

    /**
     * @var object[]
     */
    public array $actions;

    public function __construct(iterable $actions)
    {
        $this->actions = $actions instanceof \Traversable ? iterator_to_array($actions) : $actions;
    }

    public function provide(string $statusCode): InPostShippingExportActionInterface
    {
        foreach ($this->actions as $action) {
            Assert::isInstanceOf($action, InPostShippingExportActionInterface::class);

            if (!$action->supports($statusCode)) {
                continue;
            }

            return $action;
        }

        throw new \InvalidArgumentException(sprintf(self::NO_ACTION_FOUND, $statusCode));
    }
}
