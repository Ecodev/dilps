<?php

declare(strict_types=1);

namespace Application\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait for all objects with an organization
 */
trait HasOrganization
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, options={"default" = ""}))
     */
    private $organization = '';

    /**
     * Set organization
     *
     * @param string $organization
     */
    public function setOrganization(string $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }
}
