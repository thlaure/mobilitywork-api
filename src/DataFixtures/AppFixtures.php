<?php

declare(strict_types=1);

namespace MobilityWork\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use MobilityWork\Domain\Model\Entity\Currency;
use MobilityWork\Domain\Model\Entity\Customer;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\HotelContact;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Model\Entity\Room;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Languages
        $en = new Language();
        $en->setName('English');
        $manager->persist($en);

        $fr = new Language();
        $fr->setName('French');
        $manager->persist($fr);

        // Currencies
        $usd = new Currency();
        $usd->setCode('USD');
        $manager->persist($usd);

        $eur = new Currency();
        $eur->setCode('EUR');
        $manager->persist($eur);

        // Customer
        $customer = new Customer();
        $customer->setSimplePhoneNumber('0606060606');
        $manager->persist($customer);

        // Hotel Contact
        $mainHotelContact = new HotelContact();
        $mainHotelContact->setEmail('hotel.manager@example.com');
        $manager->persist($mainHotelContact);

        $hotelContact = new HotelContact();
        $hotelContact->setEmail('hotel.contact@example.com');
        $manager->persist($hotelContact);

        // Hotel
        $hotel = new Hotel();
        $hotel->setName('Grand Hyatt');
        $hotel->setAddress('123 Main St, New York, NY 10001');
        $hotel->setCurrency($usd);
        $hotel->setMainContact($mainHotelContact);
        $hotel->addContact($hotelContact);
        $hotel->addContact($mainHotelContact);
        $manager->persist($hotel);

        // Room
        $room = new Room();
        $room->setName('Deluxe King');
        $room->setType('King');
        $manager->persist($room);

        // Reservation
        $reservation = new Reservation();
        $reservation->setReference('RES12345');
        $reservation->setCustomer($customer);
        $reservation->setHotel($hotel);
        $reservation->setRoom($room);
        $reservation->setBookedDate(new \DateTime('2025-10-27'));
        $reservation->setBookedStartTime(new \DateTime('14:00'));
        $reservation->setBookedEndTime(new \DateTime('16:00'));
        $reservation->setRoomPrice(250.00);
        $manager->persist($reservation);

        $manager->flush();
    }
}
