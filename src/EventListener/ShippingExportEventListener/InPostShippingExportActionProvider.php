<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener\ShippingExportEventListener;

use Webmozart\Assert\Assert;

final class InPostShippingExportActionProvider implements InPostShippingExportActionProviderInterface
{
    private const NO_ACTION_FOUND = 'No action found for status code "%s"';

    /** @var object[] */
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
