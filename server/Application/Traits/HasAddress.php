<?php

declare(strict_types=1);

namespace Application\Traits;

use Application\Model\Country;
use Doctrine\ORM\Mapping as ORM;

/**
 * Common fields to represent an address.
 */
trait HasAddress
{
    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $street = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $postcode = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $locality = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $area = '';

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $country;

    /**
     * Get latitude
     *
     * @return null|float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Set latitude
     *
     * @param null|float $latitude
     */
    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * Get longitude
     *
     * @return null|float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Set longitude
     *
     * @param null|float $longitude
     */
    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getLocality(): string
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality(string $locality): void
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea(string $area): void
    {
        $this->area = $area;
    }

    /**
     * @return Country
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country = null): void
    {
        $this->country = $country;
    }
}
