<?php

namespace App\Entity;

use App\Repository\MercureNotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MercureNotificationsRepository::class)
 */
class MercureNotifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbCommits;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbReleases;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbMerges;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbIssues;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPipes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbJobs;

    /**
     * @ORM\Column(type="integer")
     */
    private $idProject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNbCommits(): ?int
    {
        return $this->nbCommits;
    }

    public function setNbCommits(int $nbCommits): self
    {
        $this->nbCommits = $nbCommits;

        return $this;
    }

    public function getNbReleases(): ?int
    {
        return $this->nbReleases;
    }

    public function setNbReleases(int $nbReleases): self
    {
        $this->nbReleases = $nbReleases;

        return $this;
    }

    public function getNbMerges(): ?int
    {
        return $this->nbMerges;
    }

    public function setNbMerges(int $nbMerges): self
    {
        $this->nbMerges = $nbMerges;

        return $this;
    }

    public function getNbIssues(): ?int
    {
        return $this->nbIssues;
    }

    public function setNbIssues(int $nbIssues): self
    {
        $this->nbIssues = $nbIssues;

        return $this;
    }

    public function getNbPipes(): ?int
    {
        return $this->nbPipes;
    }

    public function setNbPipes(?int $nbPipes): self
    {
        $this->nbPipes = $nbPipes;

        return $this;
    }

    public function getNbJobs(): ?int
    {
        return $this->nbJobs;
    }

    public function setNbJobs(?int $nbJobs): self
    {
        $this->nbJobs = $nbJobs;

        return $this;
    }

    public function getIdProject(): ?int
    {
        return $this->idProject;
    }

    public function setIdProject(int $idProject): self
    {
        $this->idProject = $idProject;

        return $this;
    }
}
