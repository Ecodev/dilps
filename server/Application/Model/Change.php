<?php

declare(strict_types=1);

namespace Application\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * A change suggested by a user to be accepted or rejected by administrators.
 *
 * @ORM\Entity(repositoryClass="Application\Repository\ChangeRepository")
 */
class Change extends AbstractModel
{
    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * @var string
     * @ORM\Column(type="ChangeType", options={"default" = Change::TYPE_UPDATE})
     */
    private $type = self::TYPE_UPDATE;

    /**
     * @var string
     * @ORM\Column(type="ChangeStatus", options={"default" = Change::STATUS_NEW})
     */
    private $status = self::STATUS_NEW;

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
     * @var null|DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $responseDate;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $request = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $response = '';

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
     * Get the status of change
     *
     * @API\Field(type="Application\Api\Enum\ChangeStatusType")
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the status of change
     *
     * @API\Field(type="Application\Api\Enum\ChangeStatusType")
     * @API\Exclude
     *
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
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
     * Get the date when the change suggestion was accepted or rejected.
     *
     * @return null|DateTimeImmutable
     */
    public function getResponseDate(): ?DateTimeImmutable
    {
        return $this->responseDate;
    }

    /**
     * Get the date when the change suggestion was accepted or rejected.
     *
     * @API\Exclude
     *
     * @param null|DateTimeImmutable $responseDate
     */
    public function setResponseDate(?DateTimeImmutable $responseDate): void
    {
        $this->responseDate = $responseDate;
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

    /**
     * Get the message from the administrator explaining the reason of the acceptation/rejection
     *
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * Get the message from the administrator explaining the reason of the acceptation/rejection
     *
     * @API\Exclude
     *
     * @param string $response
     */
    public function setResponse(string $response): void
    {
        $this->response = $response;
    }
}
