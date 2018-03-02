<?php

declare(strict_types=1);

namespace Application\Traits;

use Application\Model\User;
use Application\Utility;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fields to represent data and image validation
 */
trait HasValidation
{
    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $imageValidationDate;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dataValidationDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $imageValidator;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $dataValidator;

    /**
     * Get image validation date
     *
     * @return null|DateTimeImmutable
     */
    public function getImageValidationDate(): ?DateTimeImmutable
    {
        return $this->imageValidationDate;
    }

    /**
     * Get data validation date
     *
     * @return null|DateTimeImmutable
     */
    public function getDataValidationDate(): ?DateTimeImmutable
    {
        return $this->dataValidationDate;
    }

    /**
     * Get the user who validated the image
     *
     * @return null|User
     */
    public function getImageValidator(): ?User
    {
        return $this->imageValidator;
    }

    /**
     * Get the user who validated the data
     *
     * @return null|User
     */
    public function getDataValidator(): ?User
    {
        return $this->dataValidator;
    }

    /**
     * Mark the image as validated now by current user
     */
    public function validateImage(): void
    {
        $this->imageValidationDate = Utility::getNow();
        $this->imageValidator = User::getCurrent();
    }

    /**
     * Mark the data as validated now by current user
     */
    public function validateData(): void
    {
        $this->dataValidationDate = Utility::getNow();
        $this->dataValidator = User::getCurrent();
    }
}
