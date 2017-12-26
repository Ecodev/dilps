<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Model\AbstractModel;
use Application\Model\Image;
use Application\Service\ImagineFactory;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GraphQL\Doctrine\Definition\EntityID;
use Imagine\Image\ImagineInterface;
use Psr\Http\Message\UploadedFileInterface;

abstract class Helper
{
    public static function paginate(array $pagination, QueryBuilder $query): array
    {
        $offset = $pagination['offset'] ?? 0;
        $pageIndex = $pagination['pageIndex'] ?? 0;
        $pageSize = $pagination['pageSize'];

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($offset ? $offset : $pageSize * $pageIndex)
            ->setMaxResults($pageSize);

        $pagination['length'] = $paginator->count();
        $pagination['items'] = $paginator->getIterator();

        return $pagination;
    }

    public static function hydrate(AbstractModel $object, array $input): void
    {
        foreach ($input as $name => $value) {
            if ($value instanceof EntityID) {
                $value = $value->getEntity();
            }

            $setter = 'set' . ucfirst($name);
            $object->$setter($value);
        }
    }

    public static function hydrateImage(Image $image, UploadedFileInterface $file): void
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = uniqid() . ($extension ? '.' . $extension : '');
        $image->setFilename($filename);

        $path = $image->getPath();
        if (file_exists($path)) {
            throw new \Exception('A file already exist with the same name: ' . $image->getFilename());
        }
        $file->moveTo($path);

        $mime = mime_content_type($path);

        // Also add default validator for image mimetype
        $acceptedMimeTypes = [
            'image/bmp',
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.adobe.photoshop',
            'image/webp',
        ];

        if (!in_array($mime, $acceptedMimeTypes, true)) {
            unlink($path);

            throw new \Exception('Invalid file type of: ' . $mime);
        }

        $imagine = self::getImagine();
        $size = $imagine->open($path)->getSize();

        $image->setWidth($size->getWidth());
        $image->setHeight($size->getHeight());
        $image->setFileSize(filesize($path));
    }

    /**
     * Return the preferred driver available on this system
     *
     * @return ImagineInterface
     */
    private static function getImagine(): ImagineInterface
    {
        $factory = new ImagineFactory();

        return $factory();
    }
}
