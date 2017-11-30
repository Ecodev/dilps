<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * An image
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Application\Repository\ImageRepository")
 */
class Image extends AbstractModel
{
    use HasName;

    private const IMAGE_PATH = 'data/images/';

    /**
     * @var string
     * @ORM\Column(type="string", length=2000)
     */
    private $filename = '';

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" = false}))
     */
    private $isPublic = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default" = ""}))
     */
    private $dating = '';
    /**
     * @var null|DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $datingFrom;
    /**
     * @var null|DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $datingTo;

    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="images")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $collection;

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
     * Get absolute path to small image on disk
     *
     * @API\Exclude
     *
     * @return string
     */
    public function getSmallPath(): string
    {
        return realpath('.') . '/' . self::IMAGE_PATH . 'small/' . $this->getFilename();
    }

    /**
     * Set collection
     *
     * @param Collection $collection
     */
    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;
        $this->collection->imageAdded($this);
    }

    /**
     * Get collection
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
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

        $smallPath = $this->getSmallPath();
        if (file_exists($smallPath)) {
            unlink($smallPath);
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
     * values would typically:
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
     * values would typically:
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
     * Return the automatically computed beginning of dating period
     *
     * @return null|DateTimeImmutable
     */
    public function getDatingFrom(): ?DateTimeImmutable
    {
        return $this->datingFrom;
    }

    /**
     * @API\Exclude
     *
     * @param null|DateTimeImmutable $datingFrom
     */
    public function setDatingFrom(?DateTimeImmutable $datingFrom): void
    {
        $this->datingFrom = $datingFrom;
    }

    /**
     * Return the automatically computed end of dating period
     *
     * @return null|DateTimeImmutable
     */
    public function getDatingTo(): ?DateTimeImmutable
    {
        return $this->datingTo;
    }

    /**
     * @API\Exclude
     *
     * @param null|DateTimeImmutable $datingTo
     */
    public function setDatingTo(?DateTimeImmutable $datingTo): void
    {
        $this->datingTo = $datingTo;
    }
}
