<?php

declare(strict_types=1);

namespace Application\Traits;

use Application\Model\Institution;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait for all objects belonging to an institution
 */
trait HasInstitution
{
    /**
     * @var null|Institution
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="SET NULL")
     * })
     */
    private $institution;

    /**
     * Get the institution this object belongs to
     *
     * @return null|Institution
     */
    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    /**
     * Set the institution this object belongs to
     *
     * @param null|Institution $institution
     */
    public function setInstitution(?Institution $institution): void
    {
        $this->institution = $institution;
    }
}
