<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?float $roomPrice = null;

    #[ORM\ManyToOne]
    private ?Room $room = null;

    #[ORM\ManyToOne]
    private ?Hotel $hotel = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $bookedDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $bookedStartTime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $bookedEndTime = null;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRoomPrice(): ?float
    {
        return $this->roomPrice;
    }

    public function setRoomPrice(float $roomPrice): static
    {
        $this->roomPrice = $roomPrice;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): static
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getBookedDate(): ?\DateTime
    {
        return $this->bookedDate;
    }

    public function setBookedDate(?\DateTime $bookedDate): static
    {
        $this->bookedDate = $bookedDate;

        return $this;
    }

    public function getBookedStartTime(): ?\DateTime
    {
        return $this->bookedStartTime;
    }

    public function setBookedStartTime(?\DateTime $bookedStartTime): static
    {
        $this->bookedStartTime = $bookedStartTime;

        return $this;
    }

    public function getBookedEndTime(): ?\DateTime
    {
        return $this->bookedEndTime;
    }

    public function setBookedEndTime(?\DateTime $bookedEndTime): static
    {
        $this->bookedEndTime = $bookedEndTime;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
