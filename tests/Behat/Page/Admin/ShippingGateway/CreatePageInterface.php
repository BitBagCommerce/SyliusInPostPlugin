<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingGateway;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function selectShippingMethod(string $name): void;

    public function selectFieldOption(string $field, string $option): void;

    public function fillField(string $field, string $value): void;

    public function submit(): void;

    public function hasError(string $message, string $errorClass = '.sylius-validation-error'): bool;
}
