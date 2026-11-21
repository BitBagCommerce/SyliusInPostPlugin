<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Mocker;

/**
 * Sylius 2 no longer ships the Behat service mocker. No scenario exports shipments to the InPost API,
 * so the action runs as is; add a test double for bitbag.sylius_inpost_plugin.api.web_client when one does.
 */
final class InPostApiMocker
{
    public function performActionInApiSuccessfulScope(callable $action): void
    {
        $action();
    }
}
