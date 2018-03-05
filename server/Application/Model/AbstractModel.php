<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Acl\Acl;
use Application\Utility;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

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
    private $creationDate;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updateDate;

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
     * Set creation date
     *
     * @param DateTimeImmutable $creationDate
     */
    private function setCreationDate(DateTimeImmutable $creationDate = null): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Get creation date
     *
     * @return null|DateTimeImmutable
     */
    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->creationDate;
    }

    /**
     * Set update date
     *
     * @param DateTimeImmutable $updateDate
     */
    private function setUpdateDate(DateTimeImmutable $updateDate = null): void
    {
        $this->updateDate = $updateDate;
    }

    /**
     * Get update date
     *
     * @return null|DateTimeImmutable
     */
    public function getUpdateDate(): ?DateTimeImmutable
    {
        return $this->updateDate;
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
        $this->setCreationDate(Utility::getNow());
        $this->setCreator(User::getCurrent());
    }

    /**
     * Automatically called by Doctrine when the object is updated
     *
     * @ORM\PreUpdate
     */
    public function timestampUpdate(): void
    {
        $this->setUpdateDate(Utility::getNow());
        $this->setUpdater(User::getCurrent());
    }

    /**
     * @API\Field(type="Permissions")
     *
     * @return array
     */
    public function getPermissions(): array
    {
        $acl = new Acl();

        return [
            'create' => $acl->isCurrentUserAllowed($this, 'create'),
            'read' => $acl->isCurrentUserAllowed($this, 'read'),
            'update' => $acl->isCurrentUserAllowed($this, 'update'),
            'delete' => $acl->isCurrentUserAllowed($this, 'delete'),
        ];
    }
}
