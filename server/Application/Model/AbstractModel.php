<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Utility;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Base class for all objects stored in database.
 *
 * It includes an automatic mechanism to timestamp objects with date and user.
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractModel
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateCreated;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dateUpdated;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $creator;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $updater;

    /**
     * Get id
     *
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set dateCreated
     *
     * @param DateTimeImmutable $dateCreated
     */
    private function setDateCreated(DateTimeImmutable $dateCreated = null): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get dateCreated
     *
     * @return null|DateTimeImmutable
     */
    public function getDateCreated(): ?DateTimeImmutable
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param DateTimeImmutable $dateUpdated
     */
    private function setDateUpdated(DateTimeImmutable $dateUpdated = null): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * Get dateUpdated
     *
     * @return null|DateTimeImmutable
     */
    public function getDateUpdated(): ?DateTimeImmutable
    {
        return $this->dateUpdated;
    }

    /**
     * Set creator
     *
     * @param User $creator
     */
    private function setCreator(User $creator = null): void
    {
        $this->creator = $creator;
    }

    /**
     * Get creator
     *
     * @return null|User
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * Set updater
     *
     * @param null|User $updater
     */
    private function setUpdater(User $updater = null): void
    {
        $this->updater = $updater;
    }

    /**
     * Get updater
     *
     * @return null|User
     */
    public function getUpdater(): ?User
    {
        return $this->updater;
    }

    /**
     * Automatically called by Doctrine when the object is saved for the first time
     *
     * @ORM\PrePersist
     */
    public function timestampCreation(): void
    {
        $this->setDateCreated(Utility::getNow());
        $this->setCreator(User::getCurrentUser());
    }

    /**
     * Automatically called by Doctrine when the object is updated
     *
     * @ORM\PreUpdate
     */
    public function timestampUpdate(): void
    {
        $this->setDateUpdated(Utility::getNow());
        $this->setUpdater(User::getCurrentUser());
    }
}
