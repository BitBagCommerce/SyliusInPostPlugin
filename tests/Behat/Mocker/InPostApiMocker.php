<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Mocker;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class InPostApiMocker
{
    private MockerInterface $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function performActionInApiSuccessfulScope(callable $action): void
    {
        $this->mockApiSuccessfulInPostResponse();
        $action();
        $this->mocker->unmockAll();
    }

    private function mockApiSuccessfulInPostResponse(): void
    {
        $createShipmentResult = (object) [
            'createShipmentResult' => (object) [
                'label' => (object) [
                    'labelContent' => 'test',
                    'labelType' => 't',
                ],
            ],
        ];

        $this
            ->mocker
            ->mockService(
                'bitbag.sylius_inpost_plugin.api.web_client',
                WebClientInterface::class,
            )
            ->shouldReceive('createShipment')
            ->andReturn($createShipmentResult)
        ;
    }
}
