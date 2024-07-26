<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Exception;

class InvalidInPostResponseException extends InPostException
{
    public function getErrorMessage(): string
    {
        return sprintf(
            'Invalid InPost Response in file: %s; errorCode: %s; at line: %s',
            $this->getFile(),
            $this->getCode(),
            $this->getLine(),
        );
    }
}
