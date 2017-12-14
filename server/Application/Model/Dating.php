<?php

declare(strict_types=1);

namespace Application\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * An exact dating period expressed in Julian Days and automatically computed
 * from an approximate, lose string value.
 *
 * @ORM\Entity(repositoryClass="Application\Repository\DatingRepository")
 */
class Dating extends AbstractModel
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $from;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $to;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="datings")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(onDelete="CASCADE")
     * })
     */
    private $image;

    /**
     * Return the automatically computed beginning of dating period
     *
     * @return DateTimeImmutable
     */
    public function getFrom(): DateTimeImmutable
    {
        $date = new DateTimeImmutable();

        return $date->setTimestamp(jdtounix($this->from));
    }

    /**
     * @API\Exclude
     *
     * @param DateTimeImmutable $from
     */
    public function setFrom(DateTimeImmutable $from): void
    {
        $this->from = unixtojd($from->getTimestamp());
    }

    /**
     * Return the automatically computed end of dating period
     *
     * @return DateTimeImmutable
     */
    public function getTo(): DateTimeImmutable
    {
        $date = new DateTimeImmutable();

        return $date->setTimestamp(jdtounix($this->to));
    }

    /**
     * @API\Exclude
     *
     * @param DateTimeImmutable $to
     */
    public function setTo(DateTimeImmutable $to): void
    {
        $this->to = unixtojd($to->getTimestamp());
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image): void
    {
        if ($this->image) {
            $this->image->datingRemoved($this);
        }

        $this->image = $image;
        $this->image->datingAdded($this);
    }
}
