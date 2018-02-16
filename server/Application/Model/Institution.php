<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasAddress;
use Application\Traits\HasName;
use Doctrine\ORM\Mapping as ORM;

/**
 * An institution
 *
 * @ORM\Entity(repositoryClass="Application\Repository\InstitutionRepository")
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"})})
 */
class Institution extends AbstractModel
{
    use HasName;
    use HasAddress;
}
