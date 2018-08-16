<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Traits\HasName;
use Doctrine\ORM\Mapping as ORM;

/**
 * An artist
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ArtistRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_name", columns={"name"})
 * })
 */
class Artist extends AbstractModel
{
    use HasName;
}
