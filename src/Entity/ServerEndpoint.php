<?php

namespace App\Entity;

use App\Repository\ServerEndpointRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServerEndpointRepository::class)
 */
class ServerEndpoint
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
    private $team;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gitlabURL;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gitType;

   
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MapLDS", inversedBy="serverEndpoints")
     * @ORM\JoinColumn(name="map_id",referencedColumnName="id", onDelete="CASCADE")
     */
    private $map;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(string $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getGitlabURL(): ?string
    {
        return $this->gitlabURL;
    }

    public function setGitlabURL(string $gitlabURL): self
    {
        $this->gitlabURL = $gitlabURL;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getGitType(): ?string
    {
        return $this->gitType;
    }

    public function setGitType(string $gitType): self
    {
        $this->gitType = $gitType;

        return $this;
    }
 
    public function getMap()
    {
        return $this->map;
    }

    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }
}
