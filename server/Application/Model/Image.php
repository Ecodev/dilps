<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasAddress;
use Application\Traits\HasInstitution;
use Application\Traits\HasName;
use Application\Traits\ImageSimpleProperties;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;
use Imagine\Image\ImagineInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * An image
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Application\Repository\ImageRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"name"}),
 *     @ORM\Index(columns={"locality"}),
 *     @ORM\Index(columns={"area"}),
 *     @ORM\Index(columns={"latitude"}),
 *     @ORM\Index(columns={"longitude"}),
 * })
 */
class Image extends AbstractModel
{
    use HasName;
    use HasInstitution;
    use HasAddress;
    use ImageSimpleProperties;

    private const IMAGE_PATH = 'data/images/';
    const TYPE_DEFAULT = 'default';
    const TYPE_IMAGE = 'image';
    const TYPE_ARCHITECTURE = 'architecture';
    const STATUS_NEW = 'new';
    const STATUS_EDITED = 'edited';
    const STATUS_REVIEWED = 'reviewed';

    /**
     * @var string
     * @ORM\Column(type="string", length=2000)
     */
    private $filename = '';

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $fileSize = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $width = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $height = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" = false}))
     */
    private $isPublic = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $dating = '';

    /**
     * @var DoctrineCollection
     *
     * @ORM\ManyToMany(targetEntity="Collection", mappedBy="images")
     */
    private $collections;

    /**
     * @var DoctrineCollection
     *
     * @ORM\ManyToMany(targetEntity="Artist")
     */
    private $artists;

    /**
     * @var DoctrineCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     */
    private $tags;

    /**
     * @var DoctrineCollection
     *
     * @ORM\OneToMany(targetEntity="Dating", mappedBy="image")
     */
    private $datings;

    /**
     * @var null|Image
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="SET NULL")
     * })
     */
    private $original;

    /**
     * @var string
     * @ORM\Column(type="ImageType", options={"default" = Image::TYPE_DEFAULT})
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * @var string
     * @ORM\Column(type="ImageStatus", options={"default" = Image::STATUS_NEW})
     */
    private $status = self::STATUS_NEW;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->setName($name);

        $this->collections = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->datings = new ArrayCollection();
    }

    /**
     * Set the image file
     *
     * @API\Input(type="?GraphQL\Upload\UploadType")
     *
     * @param UploadedFileInterface $file
     *
     * @throws \Exception
     */
    public function setFile(UploadedFileInterface $file): void
    {
        $this->generateUniqueFilename($file);

        $path = $this->getPath();
        if (file_exists($path)) {
            throw new \Exception('A file already exist with the same name: ' . $this->getFilename());
        }
        $file->moveTo($path);

        $this->validateMimeType();
        $this->readFileInfo();
    }

    /**
     * Set filename (without path)
     *
     * @API\Exclude
     *
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * Get filename (without path)
     *
     * @API\Exclude
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Get absolute path to image on disk
     *
     * @API\Exclude
     *
     * @return string
     */
    public function getPath(): string
    {
        return realpath('.') . '/' . self::IMAGE_PATH . $this->getFilename();
    }

    /**
     * Get collections this image belongs to
     *
     * @API\Field(type="Collection[]")
     *
     * @return DoctrineCollection
     */
    public function getCollections(): DoctrineCollection
    {
        return $this->collections;
    }

    /**
     * Automatically called by Doctrine when the object is deleted
     * Is called after database update because we can have issues on remove operation (like integrity test)
     * and it's preferable to keep a related file on drive before removing it definitely.
     *
     * @ORM\PostRemove
     */
    public function deleteFile(): void
    {
        $path = $this->getPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Return whether this image is publicly available to everybody
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * Set whether this image is publicly available to everybody
     *
     * @param bool $isPublic
     */
    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    /**
     * Get the image dating.
     *
     * This is a free form string that will be parsed to **try** and extract
     * some actual date range of dates. Any string is valid, but some parseable
     * values would typically be:
     *
     * - (1620-1652)
     * - 01.05.1917
     * - XIIIe siècle
     * - 1927
     * - c. 1100
     * - Fin du XIIe siècle
     *
     * @return string
     */
    public function getDating(): string
    {
        return $this->dating;
    }

    /**
     * Set the image dating.
     *
     * This is a free form string that will be parsed to **try** and extract
     * some actual date range of dates. Any string is valid, but some parseable
     * values would typically be:
     *
     * - (1620-1652)
     * - 01.05.1917
     * - XIIIe siècle
     * - 1927
     * - c. 1100
     * - Fin du XIIe siècle
     *
     * @param string $dating
     */
    public function setDating(string $dating): void
    {
        $this->dating = $dating;
    }

    /**
     * Return the automatically computed dating periods
     *
     * @API\Field(type="Dating[]")
     *
     * @return DoctrineCollection
     */
    public function getDatings(): DoctrineCollection
    {
        return $this->datings;
    }

    /**
     * Set all artists at once by their names.
     *
     * Non-existing artists will be created automatically.
     *
     * @param string[] $artistNames
     */
    public function setArtists(array $artistNames): void
    {
        $this->artists->clear();

        $artistNames = _em()->getRepository(Artist::class)->getOrCreateByNames($artistNames);
        foreach ($artistNames as $a) {
            $this->artists->add($a);
        }
    }

    /**
     * Get artists
     *
     * @API\Field(type="Artist[]")
     *
     * @return DoctrineCollection
     */
    public function getArtists(): DoctrineCollection
    {
        return $this->artists;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @API\Field(type="Tag[]")
     *
     * @return DoctrineCollection
     */
    public function getTags(): DoctrineCollection
    {
        return $this->tags;
    }

    /**
     * Set image type
     *
     * @API\Input(type="Application\Api\Enum\ImageTypeType")
     *
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get image type
     *
     * @API\Field(type="Application\Api\Enum\ImageTypeType")
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set image status
     *
     * @API\Input(type="Application\Api\Enum\ImageStatusType")
     *
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get image status
     *
     * @API\Field(type="Application\Api\Enum\ImageStatusType")
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * The original image if this is a suggestion
     *
     * @return null|Image
     */
    public function getOriginal(): ?self
    {
        return $this->original;
    }

    /**
     * Defines this image as suggestion for the $original
     *
     * @param null|Image $original
     */
    public function setOriginal(?self $original): void
    {
        $this->original = $original;
    }

    /**
     * Notify the Image that it was added to a Collection.
     * This should only be called by Collection::addImage()
     *
     * @param Collection $collection
     */
    public function collectionAdded(Collection $collection): void
    {
        $this->collections->add($collection);
    }

    /**
     * Notify the Image that it was removed from a Collection.
     * This should only be called by Collection::removeImage()
     *
     * @param Collection $collection
     */
    public function collectionRemoved(Collection $collection): void
    {
        $this->collections->removeElement($collection);
    }

    /**
     * Notify the Image that a Dating was added.
     * This should only be called by Dating::setImage()
     *
     * @param Dating $dating
     */
    public function datingAdded(Dating $dating): void
    {
        $this->datings->add($dating);
    }

    /**
     * Notify the Image that a Dating was removed.
     * This should only be called by Dating::setImage()
     *
     * @param Dating $dating
     */
    public function datingRemoved(Dating $dating): void
    {
        $this->datings->removeElement($dating);
    }

    /**
     * Get file size in bytes
     *
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * Set file size in bytes
     *
     * @API\Exclude
     *
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * Get image width
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Set image width
     *
     * @API\Exclude
     *
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Set image height
     *
     * @API\Exclude
     *
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * Generate unique filename based while trying to preserver original extension
     *
     * @param UploadedFileInterface $file
     */
    private function generateUniqueFilename(UploadedFileInterface $file): void
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = uniqid() . ($extension ? '.' . $extension : '');
        $this->setFilename($filename);
    }

    /**
     * Delete file and throw exception if MIME type is invalid
     *
     * @throws \Exception
     */
    private function validateMimeType(): void
    {
        $path = $this->getPath();
        $mime = mime_content_type($path);

        // Validate image mimetype
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
    }

    /**
     * Read dimension and size from file on disk
     */
    private function readFileInfo(): void
    {
        global $container;
        $path = $this->getPath();

        /** @var ImagineInterface $imagine */
        $imagine = $container->get(ImagineInterface::class);
        $size = $imagine->open($path)->getSize();

        $this->setWidth($size->getWidth());
        $this->setHeight($size->getHeight());
        $this->setFileSize(filesize($path));
    }
}
