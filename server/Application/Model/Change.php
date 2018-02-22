<?php

declare(strict_types=1);

namespace Application\Model;

use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * A change suggested by a user to be accepted or rejected by administrators.
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ChangeRepository")
 * @ORM\Table(name="`change`")
 */
class Change extends AbstractModel
{
    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    /**
     * @var string
     * @ORM\Column(type="ChangeType", options={"default" = Change::TYPE_UPDATE})
     */
    private $type = self::TYPE_UPDATE;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="CASCADE")
     * })
     */
    private $original;

    /**
     * @var null|Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="SET NULL")
     * })
     */
    private $suggestion;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $request = '';

    /**
     * Get the type of change
     *
     * @API\Field(type="Application\Api\Enum\ChangeTypeType")
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the type of change
     *
     * @API\Field(type="Application\Api\Enum\ChangeTypeType")
     *
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Get the original image on which to apply change
     *
     * It will be `null` if the change type is `create`, otherwise
     * it mus be set.
     *
     * @return null|Image
     */
    public function getOriginal(): ?Image
    {
        return $this->original;
    }

    /**
     * Set the original image on which to apply change
     *
     * @param null|Image $original
     */
    public function setOriginal(?Image $original): void
    {
        $this->original = $original;
    }

    /**
     * Get the image containing the suggested changes.
     *
     * It will be `null` if the change type is `delete`, otherwise
     * it mus be set.
     *
     * @return null|Image
     */
    public function getSuggestion(): ?Image
    {
        return $this->suggestion;
    }

    /**
     * @param null|Image $suggestion
     */
    public function setSuggestion(?Image $suggestion): void
    {
        $this->suggestion = $suggestion;
    }

    /**
     * Get the message from the submitter explaining the reason of the change request
     *
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * Set the message from the submitter explaining the reason of the change request
     *
     * @param string $request
     */
    public function setRequest(string $request): void
    {
        $this->request = $request;
    }
}
