<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use Application\Traits\HasOrganization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * A collection of images
 *
 * @ORM\Entity(repositoryClass="Application\Repository\CollectionRepository")
 */
class Collection extends AbstractModel
{
    use HasName;
    use HasOrganization;

    /**
     * @var string
     *
     * @ORM\Column(type="text"))
     */
    private $description = '';

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" = false}))
     */
    private $isSource = false;

    /**
     * @var Collection
     *
     * @ORM\ManyToOne(targetEntity="Application\Model\Collection", inversedBy="children")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @var DocwwtrineCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Model\Collection", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC", "id" = "ASC"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Application\Model\Image", mappedBy="collection")
     */
    private $images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns whether the collection is a source ("main" collection)
     */
    public function isSource(): bool
    {
        return $this->isSource;
    }

    /**
     * Set whether the collection is a source ("main" collection)
     *
     * @param bool $isSource
     */
    public function setIsSource(bool $isSource): void
    {
        $this->isSource = $isSource;
    }

    /**
     * Get the parent collection containing this collection.
     *
     * @return null|Collection
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * Set the parent collection containing this collection.
     *
     * @param null|Collection $parent
     */
    public function setParent(?self $parent): void
    {
        // Remove from previous parent
        if ($this->parent) {
            $this->parent->children->removeElement($this);
        }

        // Add to new parent
        if ($parent) {
            $this->assertNotCyclic($parent);
            $parent->children->add($this);
        }

        $this->parent = $parent;
    }

    private function assertNotCyclic(self $parentCandidate): void
    {
        $allChildren = $this->getAllChildren();

        while ($parentCandidate) {
            if (in_array($parentCandidate, $allChildren, true)) {
                throw new InvalidArgumentException('Parent collection is invalid because it would create a cyclic hierarchy');
            }

            $parentCandidate = $parentCandidate->getParent();
        }
    }

    /**
     * Get children collections
     *
     * @return Collection[]
     */
    public function getChildren(): DoctrineCollection
    {
        return $this->children;
    }

    /**
     * Get recursively all children and grand-children
     *
     * @return Collection[]
     */
    private function getAllChildren(): array
    {
        $allChildren = [];
        foreach ($this->getChildren() as $child) {
            $allChildren[] = $child;
            $allChildren = array_merge($allChildren, $child->getAllChildren());
        }

        return $allChildren;
    }

    /**
     * Get images
     *
     * @return Image[]
     */
    public function getImages(): DoctrineCollection
    {
        return $this->images;
    }

    /**
     * Notify the collection that an Image was added.
     * This should only be called by Image::setCollection()
     *
     * @param \Application\Model\Image $image
     */
    public function imageAdded(Image $image): void
    {
        $this->images->add($image);
    }
}
