<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\HotelRepository;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    #[ORM\ManyToOne]
    private ?HotelContact $mainContact = null;

    /**
     * @var Collection<int, HotelContact>
     */
    #[ORM\OneToMany(targetEntity: HotelContact::class, mappedBy: 'hotel')]
    private Collection $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getMainContact(): ?HotelContact
    {
        return $this->mainContact;
    }

    public function setMainContact(?HotelContact $mainContact): static
    {
        $this->mainContact = $mainContact;

        return $this;
    }

    /**
     * @return Collection<int, HotelContact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(HotelContact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setHotel($this);
        }

        return $this;
    }

    public function removeContact(HotelContact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getHotel() === $this) {
                $contact->setHotel(null);
            }
        }

        return $this;
    }
}
