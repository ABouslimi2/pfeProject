<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateNotif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Action;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Contenu;

      /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ownerProj;

      /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameProj;
    

     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idProj;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idTeam;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNotif(): ?\DateTimeInterface
    {
        return $this->DateNotif;
    }

    public function setDateNotif(\DateTimeInterface $DateNotif): self
    {
        $this->DateNotif = $DateNotif;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->Action;
    }

    public function setAction(string $Action): self
    {
        $this->Action = $Action;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(string $Contenu): self
    {
        $this->Contenu = $Contenu;

        return $this;
    }

    /**
     * Get the value of idProj
     */ 
    public function getIdProj()
    {
        return $this->idProj;
    }

    /**
     * Set the value of idProj
     *
     * @return  self
     */ 
    public function setIdProj($idProj)
    {
        $this->idProj = $idProj;

        return $this;
    }

    /**
     * Get the value of idTeam
     */ 
    public function getIdTeam()
    {
        return $this->idTeam;
    }

    /**
     * Set the value of idTeam
     *
     * @return  self
     */ 
    public function setIdTeam($idTeam)
    {
        $this->idTeam = $idTeam;

        return $this;
    }

    /**
     * Get the value of ownerProj
     */ 
    public function getOwnerProj()
    {
        return $this->ownerProj;
    }

    /**
     * Set the value of ownerProj
     *
     * @return  self
     */ 
    public function setOwnerProj($ownerProj)
    {
        $this->ownerProj = $ownerProj;

        return $this;
    }

    /**
     * Get the value of nameProj
     */ 
    public function getNameProj()
    {
        return $this->nameProj;
    }

    /**
     * Set the value of nameProj
     *
     * @return  self
     */ 
    public function setNameProj($nameProj)
    {
        $this->nameProj = $nameProj;

        return $this;
    }
}
