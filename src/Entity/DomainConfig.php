<?php

namespace MobilityWork\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobilityWork\Repository\DomainConfigRepository;

#[ORM\Entity(repositoryClass: DomainConfigRepository::class)]
class DomainConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
