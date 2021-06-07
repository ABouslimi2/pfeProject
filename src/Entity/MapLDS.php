<?php

namespace App\Entity;

use App\Repository\MapLDSRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapLDSRepository::class)
 */
class MapLDS
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\OneToMany(targetEntity=ServerEndpoint::class, mappedBy="map")
     */
    public $serverEndpoints;
    public $commits;
    public $releases;
    public $merges;
    public $teamName;
    public $urlServer;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $address;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    public $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    public $lattitude;


    public function getId(): ?int
    {
        return $this->id;
    }

   
    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLattitude(): ?float
    {
        return $this->lattitude;
    }

    public function setLattitude(?float $lattitude): self
    {
        $this->lattitude = $lattitude;

        return $this;
    }
   

    /**
     * Get the value of serverEndpoints
     */ 
    public function getServerEndpoints()
    {
        return $this->serverEndpoints;
    }

    /**
     * Set the value of serverEndpoints
     *
     * @return  self
     */ 
    public function setServerEndpoints($serverEndpoints)
    {
        $this->serverEndpoints = $serverEndpoints;

        return $this;
    }
}
