<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface InPostPointInterface extends ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getHref(): ?string;

    public function setHref(?string $href): void;
    
    public function getImageUrl(): ?string;

    public function setImageUrl(?string $imageUrl): void;
    
    public function getAddressLine1(): ?string;

    public function setAddressLine1(?string $addressLine1): void;
    
    public function getAddressLine2(): ?string;

    public function setAddressLine2(?string $addressLine2): void;

    public function getLocationDescription(): ?string;

    public function setLocationDescription(?string $locationDescription): void;
}
