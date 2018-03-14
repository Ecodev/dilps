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
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="CASCADE")
     * })
     */
    private $original;

    /**
     * @var null|Card
     *
     * @ORM\OneToOne(targetEntity="Card", inversedBy="change")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="CASCADE")
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
     * Get the original card on which to apply change
     *
     * It will be `null` if the change type is `create`, otherwise
     * it mus be set.
     *
     * @return null|Card
     */
    public function getOriginal(): ?Card
    {
        return $this->original;
    }

    /**
     * Set the original card on which to apply change
     *
     * @param null|Card $original
     */
    public function setOriginal(?Card $original): void
    {
        $this->original = $original;
    }

    /**
     * Get the card containing the suggested changes.
     *
     * It will be `null` if the change type is `delete`, otherwise
     * it mus be set.
     *
     * @return null|Card
     */
    public function getSuggestion(): ?Card
    {
        return $this->suggestion;
    }

    /**
     * @param null|Card $suggestion
     */
    public function setSuggestion(?Card $suggestion): void
    {
        if ($this->suggestion) {
            $this->suggestion->changeAdded(null);
        }

        $this->suggestion = $suggestion;

        if ($this->suggestion) {
            $this->suggestion->changeAdded($this);
        }
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
