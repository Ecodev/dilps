<?php

namespace App\Model;

use App\Utility;
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
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $dateCreated;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $dateModified;

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
    private $modifier;

    /**
     * Get id
     *
     * @return null|int
     */
    public function getId():?int
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
     * Set dateModified
     *
     * @param DateTimeImmutable $dateModified
     */
    private function setDateModified(DateTimeImmutable $dateModified = null): void
    {
        $this->dateModified = $dateModified;
    }

    /**
     * Get dateModified
     *
     * @return null|DateTimeImmutable
     */
    public function getDateModified():?DateTimeImmutable
    {
        return $this->dateModified;
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
    public function getCreator():?User
    {
        return $this->creator;
    }

    /**
     * Set modifier
     *
     * @param null|User $modifier
     */
    private function setModifier(User $modifier = null): void
    {
        $this->modifier = $modifier;
    }

    /**
     * Get modifier
     *
     * @return null|User
     */
    public function getModifier():?User
    {
        return $this->modifier;
    }

    /**
     * Automatically called by Doctrine when the object is saved for the first time
     *
     * @ORM\PrePersist
     */
    public function timestampCreation()
    {
        $this->setDateCreated(Utility::getNow());
        $this->setCreator(User::getCurrentUser());
    }

    /**
     * Automatically called by Doctrine when the object is updated
     *
     * @ORM\PreUpdate
     */
    public function timestampModification()
    {
        $this->setDateModified(Utility::getNow());
        $this->setModifier(User::getCurrentUser());
    }
}
