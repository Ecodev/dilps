<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use Doctrine\ORM\Mapping as ORM;

/**
 * A tag
 *
 * @ORM\Entity(repositoryClass="Application\Repository\TagRepository")
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"})})
 */
class Tag extends AbstractModel
{
    use HasName;
}
