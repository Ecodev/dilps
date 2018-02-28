<?php

declare(strict_types=1);

namespace Application\Traits;

use Application\Api\Exception;
use Application\Model\Card;
use Application\Model\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Common fields to represent an address.
 */
trait HasVisibility
{
    /**
     * @var string
     * @ORM\Column(type="Visibility", options={"default" = Card::VISIBILITY_PRIVATE})
     */
    private $visibility = Card::VISIBILITY_PRIVATE;

    /**
     * Return whether this is publicly available to everybody, or only member, or only owner
     *
     * @API\Field(type="Application\Api\Enum\VisibilityType")
     *
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * Set whether this is publicly available to everybody, or only member, or only owner
     *
     * @param string $visibility
     */
    public function setVisibility(string $visibility): void
    {
        $user = User::getCurrent();
        if ($visibility === Card::VISIBILITY_PUBLIC && $this->visibility !== Card::VISIBILITY_PUBLIC && $user->getRole() !== User::ROLE_ADMINISTRATOR) {
            throw new Exception('Only administrator can make it public');
        }

        $this->visibility = $visibility;
    }
}
