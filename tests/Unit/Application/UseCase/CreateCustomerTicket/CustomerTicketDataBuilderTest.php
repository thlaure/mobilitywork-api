<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Application\Service\Finder\HotelFinder;
use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Application\Service\Finder\ReservationFinder;
use MobilityWork\Application\UseCase\CreateCustomerTicket\CreateCustomerTicketCommand;
use MobilityWork\Application\UseCase\CreateCustomerTicket\CustomerTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Model\Entity\HotelContact;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Model\Entity\Room;
use MobilityWork\Domain\Model\Ticket\CreateCustomerTicketRequest;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CustomerTicketDataBuilderTest extends TestCase
{
    private ReservationFinder&MockObject $reservationFinder;
    private HotelFinder&MockObject $hotelFinder;
    private LanguageFinder&MockObject $languageFinder;
    private CustomerTicketDataBuilder $customerTicketDataBuilder;

    public function setUp(): void
    {
        $this->reservationFinder = $this->createMock(ReservationFinder::class);
        $this->hotelFinder = $this->createMock(HotelFinder::class);
        $this->languageFinder = $this->createMock(LanguageFinder::class);

        $this->customerTicketDataBuilder = new CustomerTicketDataBuilder($this->reservationFinder, $this->hotelFinder, $this->languageFinder);
    }

    public function testBuildWithMinimumInformation(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message');
        $command = new CreateCustomerTicketCommand($request);

        $expectedResult = new TicketCreationDataDTO(
            new UserDTO('test@mail.test', 'John DOE', '0606060606'),
            new TicketDTO(
                subject: 'My message',
                comment: [
                    'body' => 'My message',
                ],
                customFields: [
                    ZendeskCustomFields::TICKET_TYPE => 'customer',
                    ZendeskCustomFields::RESERVATION_NUMBER => null,
                ]
            )
        );

        $this->reservationFinder->expects($this->once())
            ->method('getByRef')
            ->with($command->request->reservationNumber)
            ->willReturn(null);

        $this->hotelFinder->expects($this->once())
            ->method('findById')
            ->with($command->request->hotelId)
            ->willReturn(null);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with($command->request->languageId)
            ->willReturn(null);

        $result = $this->customerTicketDataBuilder->build($command);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildCustomerFieldsWithoutHotel(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message', 'RES001', null, 1);
        $command = new CreateCustomerTicketCommand($request);

        $room = $this->createMock(Room::class);
        $room->method('getName')->willReturn('My Room');
        $room->method('getType')->willReturn('Double');

        $reservation = $this->createMock(Reservation::class);
        $reservation->method('getReference')->willReturn('RES001');
        $reservation->method('getBookedDate')->willReturn(new \DateTime('2025-01-01'));
        $reservation->method('getBookedStartTime')->willReturn(new \DateTime('2024-12-31 10:00:00'));
        $reservation->method('getBookedEndTime')->willReturn(new \DateTime('2024-12-31 11:00:00'));
        $reservation->method('getRoomPrice')->willReturn(150.50);
        $reservation->method('getRoom')->willReturn($room);
        $reservation->method('getHotel')->willReturn(null);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->reservationFinder->expects($this->once())
            ->method('getByRef')
            ->with('RES001')
            ->willReturn($reservation);

        $this->hotelFinder->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn(null);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->customerTicketDataBuilder->build($command);

        $expectedCustomFields = [
            ZendeskCustomFields::TICKET_TYPE => 'customer',
            ZendeskCustomFields::RESERVATION_NUMBER => 'RES001',
            ZendeskCustomFields::ROOM_NAME => 'My Room (Double)',
            ZendeskCustomFields::BOOKED_DATE => '2025-01-01',
            ZendeskCustomFields::ROOM_PRICE => '150.5 ',
            ZendeskCustomFields::BOOKED_TIME => '10:00 - 11:00',
            ZendeskCustomFields::LANGUAGE_NAME => 'French',
        ];

        $this->assertEquals($expectedCustomFields, $result->ticket->customFields);
    }

    public function testBuildCustomerFieldsWithoutReservation(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message', null, 1, 1);
        $command = new CreateCustomerTicketCommand($request);

        $hotelContact = $this->createMock(HotelContact::class);
        $hotelContact->method('getEmail')->willReturn('main@hotel.com');

        $hotel = $this->createMock(Hotel::class);
        $hotel->method('getMainContact')->willReturn($hotelContact);
        $hotel->method('getName')->willReturn('My Hotel');
        $hotel->method('getAddress')->willReturn('My Address');

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->reservationFinder->expects($this->once())
            ->method('getByRef')
            ->with(null)
            ->willReturn(null);

        $this->hotelFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($hotel);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->customerTicketDataBuilder->build($command);

        $expectedCustomFields = [
            ZendeskCustomFields::TICKET_TYPE => 'customer',
            ZendeskCustomFields::RESERVATION_NUMBER => null,
            ZendeskCustomFields::HOTEL_CONTACT_EMAIL => 'main@hotel.com',
            ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
            ZendeskCustomFields::HOTEL_ADDRESS => 'My Address',
            ZendeskCustomFields::LANGUAGE_NAME => 'French',
        ];

        $this->assertEquals($expectedCustomFields, $result->ticket->customFields);
    }

    public function testBuildCustomerFieldsWithoutLanguage(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message', 'RES001', 1, null);
        $command = new CreateCustomerTicketCommand($request);

        $hotelContact = $this->createMock(HotelContact::class);
        $hotelContact->method('getEmail')->willReturn('main@hotel.com');

        $hotel = $this->createMock(Hotel::class);
        $hotel->method('getMainContact')->willReturn($hotelContact);
        $hotel->method('getName')->willReturn('My Hotel');
        $hotel->method('getAddress')->willReturn('My Address');

        $room = $this->createMock(Room::class);
        $room->method('getName')->willReturn('My Room');
        $room->method('getType')->willReturn('Double');

        $reservation = $this->createMock(Reservation::class);
        $reservation->method('getReference')->willReturn('RES001');
        $reservation->method('getBookedDate')->willReturn(new \DateTime('2025-01-01'));
        $reservation->method('getBookedStartTime')->willReturn(new \DateTime('2024-12-31 10:00:00'));
        $reservation->method('getBookedEndTime')->willReturn(new \DateTime('2024-12-31 11:00:00'));
        $reservation->method('getRoomPrice')->willReturn(150.50);
        $reservation->method('getRoom')->willReturn($room);
        $reservation->method('getHotel')->willReturn($hotel);

        $this->reservationFinder->expects($this->once())
            ->method('getByRef')
            ->with('RES001')
            ->willReturn($reservation);

        $this->hotelFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($hotel);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn(null);

        $result = $this->customerTicketDataBuilder->build($command);

        $expectedCustomFields = [
            ZendeskCustomFields::TICKET_TYPE => 'customer',
            ZendeskCustomFields::RESERVATION_NUMBER => null,
            ZendeskCustomFields::HOTEL_CONTACT_EMAIL => 'main@hotel.com',
            ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
            ZendeskCustomFields::HOTEL_ADDRESS => 'My Address',
            ZendeskCustomFields::RESERVATION_NUMBER => 'RES001',
            ZendeskCustomFields::ROOM_NAME => 'My Room (Double)',
            ZendeskCustomFields::BOOKED_DATE => '2025-01-01',
            ZendeskCustomFields::ROOM_PRICE => '150.5 ',
            ZendeskCustomFields::BOOKED_TIME => '10:00 - 11:00',
        ];

        $this->assertEquals($expectedCustomFields, $result->ticket->customFields);
    }

    public function testBuildCustomerFieldsFull(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message', 'RES001', 1, 1);
        $command = new CreateCustomerTicketCommand($request);

        $hotelContact = $this->createMock(HotelContact::class);
        $hotelContact->method('getEmail')->willReturn('main@hotel.com');

        $hotel = $this->createMock(Hotel::class);
        $hotel->method('getMainContact')->willReturn($hotelContact);
        $hotel->method('getName')->willReturn('My Hotel');
        $hotel->method('getAddress')->willReturn('My Address');

        $room = $this->createMock(Room::class);
        $room->method('getName')->willReturn('My Room');
        $room->method('getType')->willReturn('Double');

        $reservation = $this->createMock(Reservation::class);
        $reservation->method('getReference')->willReturn('RES001');
        $reservation->method('getBookedDate')->willReturn(new \DateTime('2025-01-01'));
        $reservation->method('getBookedStartTime')->willReturn(new \DateTime('2024-12-31 10:00:00'));
        $reservation->method('getBookedEndTime')->willReturn(new \DateTime('2024-12-31 11:00:00'));
        $reservation->method('getRoomPrice')->willReturn(150.50);
        $reservation->method('getRoom')->willReturn($room);
        $reservation->method('getHotel')->willReturn($hotel);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->reservationFinder->expects($this->once())
            ->method('getByRef')
            ->with('RES001')
            ->willReturn($reservation);

        $this->hotelFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($hotel);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->customerTicketDataBuilder->build($command);

        $expectedCustomFields = [
            ZendeskCustomFields::TICKET_TYPE => 'customer',
            ZendeskCustomFields::RESERVATION_NUMBER => null,
            ZendeskCustomFields::HOTEL_CONTACT_EMAIL => 'main@hotel.com',
            ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
            ZendeskCustomFields::HOTEL_ADDRESS => 'My Address',
            ZendeskCustomFields::RESERVATION_NUMBER => 'RES001',
            ZendeskCustomFields::ROOM_NAME => 'My Room (Double)',
            ZendeskCustomFields::BOOKED_DATE => '2025-01-01',
            ZendeskCustomFields::ROOM_PRICE => '150.5 ',
            ZendeskCustomFields::BOOKED_TIME => '10:00 - 11:00',
            ZendeskCustomFields::LANGUAGE_NAME => 'French',
        ];

        $this->assertEquals($expectedCustomFields, $result->ticket->customFields);
    }
}
