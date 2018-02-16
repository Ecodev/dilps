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
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"name"}),
 *     @ORM\Index(columns={"locality"}),
 *     @ORM\Index(columns={"area"}),
 *     @ORM\Index(columns={"latitude"}),
 *     @ORM\Index(columns={"longitude"}),
 * })
 */
class Institution extends AbstractModel
{
    use HasName;
    use HasAddress;
}
