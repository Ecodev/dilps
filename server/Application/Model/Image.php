<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
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
     * @var null|Institution
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="SET NULL")
     * })
     */
    private $institution;

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
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $addition = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $expandedName = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $material = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $technique = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $techniqueAuthor = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $format = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $literature = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=10, options={"default" = ""})
     */
    private $page = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=10, options={"default" = ""})
     */
    private $figure = '';

    /**
     * @var string
     * @ORM\Column(name="`table`", type="string", length=10, options={"default" = ""})
     */
    private $table = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=20, options={"default" = ""})
     */
    private $isbn = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $comment = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $rights = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $muserisUrl = '';

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" = ""})
     */
    private $muserisCote = '';

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

    /**
     * Add artist
     *
     * @param Artist $artist
     */
    public function addArtist(Artist $artist): void
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }
    }

    /**
     * Remove artist
     *
     * @param Artist $artist
     */
    public function removeArtist(Artist $artist): void
    {
        $this->artists->removeElement($artist);
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
     * Get the institution where the image is located
     *
     * @return null|Institution
     */
    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    /**
     * Set the institution where the image is located
     *
     * @param null|Institution $institution
     */
    public function setInstitution(?Institution $institution): void
    {
        $this->institution = $institution;
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
     * @return string
     */
    public function getAddition(): string
    {
        return $this->addition;
    }

    /**
     * @param string $addition
     */
    public function setAddition(string $addition): void
    {
        $this->addition = $addition;
    }

    /**
     * @return string
     */
    public function getExpandedName(): string
    {
        return $this->expandedName;
    }

    /**
     * @param string $expandedName
     */
    public function setExpandedName(string $expandedName): void
    {
        $this->expandedName = $expandedName;
    }

    /**
     * @return string
     */
    public function getMaterial(): string
    {
        return $this->material;
    }

    /**
     * @param string $material
     */
    public function setMaterial(string $material): void
    {
        $this->material = $material;
    }

    /**
     * @return string
     */
    public function getTechnique(): string
    {
        return $this->technique;
    }

    /**
     * @param string $technique
     */
    public function setTechnique(string $technique): void
    {
        $this->technique = $technique;
    }

    /**
     * @return string
     */
    public function getTechniqueAuthor(): string
    {
        return $this->techniqueAuthor;
    }

    /**
     * @param string $techniqueAuthor
     */
    public function setTechniqueAuthor(string $techniqueAuthor): void
    {
        $this->techniqueAuthor = $techniqueAuthor;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getLiterature(): string
    {
        return $this->literature;
    }

    /**
     * @param string $literature
     */
    public function setLiterature(string $literature): void
    {
        $this->literature = $literature;
    }

    /**
     * @return string
     */
    public function getPage(): string
    {
        return $this->page;
    }

    /**
     * @param string $page
     */
    public function setPage(string $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getFigure(): string
    {
        return $this->figure;
    }

    /**
     * @param string $figure
     */
    public function setFigure(string $figure): void
    {
        $this->figure = $figure;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getRights(): string
    {
        return $this->rights;
    }

    /**
     * @param string $rights
     */
    public function setRights(string $rights): void
    {
        $this->rights = $rights;
    }

    /**
     * @return string
     */
    public function getMuserisUrl(): string
    {
        return $this->muserisUrl;
    }

    /**
     * @param string $muserisUrl
     */
    public function setMuserisUrl(string $muserisUrl): void
    {
        $this->muserisUrl = $muserisUrl;
    }

    /**
     * @return string
     */
    public function getMuserisCote(): string
    {
        return $this->muserisCote;
    }

    /**
     * @param string $muserisCote
     */
    public function setMuserisCote(string $muserisCote): void
    {
        $this->muserisCote = $muserisCote;
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
}