<?php

namespace App\Entity;

use App\Repository\CoordsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoordsRepository::class)
 */
class Coords
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $_long;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $_lat;

    /**
     * @ORM\ManyToOne(targetEntity=Annonce::class, inversedBy="coords")
     */
    private $annonce;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLong(): ?string
    {
        return $this->_long;
    }

    public function setLong(string $_long): self
    {
        $this->_long = $_long;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->_lat;
    }

    public function setLat(string $_lat): self
    {
        $this->_lat = $_lat;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }
}
