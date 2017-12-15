<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use Doctrine\ORM\Mapping as ORM;

/**
 * An artist
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ArtistRepository")
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"})})
 */
class Artist extends AbstractModel
{
    use HasName;
}
