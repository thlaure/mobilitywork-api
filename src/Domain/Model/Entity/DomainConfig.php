<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DomainConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    public function getId(): ?int
    {
        return $this->id;
    }
}
