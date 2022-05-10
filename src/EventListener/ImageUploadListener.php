<?php

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\EventListener;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        if (empty($subject->getImage()) || !$subject->getImage()->hasFile()) {
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
