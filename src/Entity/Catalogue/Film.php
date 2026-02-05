<?php

namespace App\Entity\Catalogue;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Film extends Article
{
    #[ORM\Column(length: 255,name: 'titre_original')]
    private ?string $titre_vo = null;

    #[ORM\Column(length: 255, name: 'id_tmdb')]
    private ?int $id_tmdb = null;

    #[ORM\Column(name: 'durée')]
    private ?int $duree = null;

    #[ORM\Column(length: 255, name: 'réalisateur')]
    private ?string $realisateur = null;

    #[ORM\Column(length: 255, name: 'date_de_publication')]
    private ?string $dateDePublication = null;

    public function getTitreVO(): ?string
    {
        return $this->titre_vo;
    }

    public function setTitreVO(?string $titre_vo): static
    {
        $this->titre_vo = $titre_vo;

        return $this;
    }

    public function getIdTMDB(): ?string
    {
        return $this->id_tmdb;
    }

    public function setIdTMDB(?string $id_tmdb): static
    {
        $this->ISid_tmdbBN = $id_tmdb;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateDePublication(): ?string
    {
        return $this->dateDePublication;
    }

    public function setDateDePublication(?string $dateDePublication): static
    {
        $this->dateDePublication = $dateDePublication;

        return $this;
    }

    public function getRealisateur(): ?string
    {
        return $this->realisateur;
    }

    public function setRealisateur(?string $realisateur): static
    {
        $this->realisateur = $realisateur;

        return $this;
    }
}

