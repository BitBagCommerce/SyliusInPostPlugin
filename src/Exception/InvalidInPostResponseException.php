<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
