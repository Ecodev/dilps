<?php

declare(strict_types=1);

namespace Application\Model;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * An exact dating period expressed in Julian Days and automatically computed
 * from an approximate, lose string value.
 *
 * Julian days are used instead of standard date format because MariaDB does not
 * support older year than 1000 and we often much older than that (before Christ)
 *
 * @ORM\Entity(repositoryClass="Application\Repository\DatingRepository")
 */
class Dating extends AbstractModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="`from`", type="integer")
     */
    private $from;

    /**
     * @var int
     *
     * @ORM\Column(name="`to`", type="integer")
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
        return $this->julianToDate($this->from);
    }

    /**
     * @API\Exclude
     *
     * @param DateTimeImmutable $from
     */
    public function setFrom(DateTimeImmutable $from): void
    {
        $this->from = $this->dateToJulian($from);
    }

    /**
     * Return the automatically computed end of dating period
     *
     * @return DateTimeImmutable
     */
    public function getTo(): DateTimeImmutable
    {
        return $this->julianToDate($this->to);
    }

    /**
     * @API\Exclude
     *
     * @param DateTimeImmutable $to
     */
    public function setTo(DateTimeImmutable $to): void
    {
        $this->to = $this->dateToJulian($to);
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

    private function dateToJulian(DateTimeImmutable $date): int
    {
        return gregoriantojd((int) $date->format('m'), (int) $date->format('d'), (int) $date->format('Y'));
    }

    private function julianToDate(int $date): DateTimeImmutable
    {
        $parts = explode('/', jdtogregorian($date));

        $result = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return $result->setDate((int) $parts[2], (int) $parts[0], (int) $parts[1])->setTime(0, 0, 0, 0);
    }
}
