<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use Doctrine\ORM\Mapping as ORM;

/**
 * An institution
 *
 * @ORM\Entity(repositoryClass="Application\Repository\InstitutionRepository")
 */
class Institution extends AbstractModel
{
    use HasName;
}
