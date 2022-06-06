<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener;

use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

final class ImageUploadListener
{
    private ImageUploaderInterface $uploader;

    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadImages(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        Assert::isInstanceOf($subject, ImageAwareInterface::class);

        if (null !== $subject->getImage()) {
            return;
        }

        /** @var ImageInterface $image */
        $image = $subject->getImage();

        /** @var UploadedFile $file */
        $file = $image->getFile();

        $this->uploader->upload($image);

        $image->setType($file->getMimeType());
        $image->setOwner($subject);
    }
}
