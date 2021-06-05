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
     * @ORM\Column(type="datetime")
     */
    private $DateNotif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Action;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Contenu;

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
}
