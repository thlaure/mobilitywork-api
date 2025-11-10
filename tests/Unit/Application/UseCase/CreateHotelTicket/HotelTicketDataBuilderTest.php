<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreateHotelTicket;

use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Application\UseCase\CreateHotelTicket\CreateHotelTicketCommand;
use MobilityWork\Application\UseCase\CreateHotelTicket\HotelTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HotelTicketDataBuilderTest extends TestCase
{
    private LanguageFinder&MockObject $languageFinder;
    private HotelTicketDataBuilder $hotelTicketDataBuilder;

    public function setUp(): void
    {
        $this->languageFinder = $this->createMock(LanguageFinder::class);
        $this->hotelTicketDataBuilder = new HotelTicketDataBuilder($this->languageFinder);
    }

    public function testBuildSuccess(): void
    {
        $request = new CreateHotelTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            website: 'http://my.website.test',
            hotelName: 'My Hotel',
            subject: 'My subject',
            message: 'My message',
            languageId: 1
        );
        $command = new CreateHotelTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->hotelTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: [
                'website' => 'http://my.website.test',
            ]
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'hotel',
                ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
                ZendeskCustomFields::CITY => 'Paris',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildThrowsExceptionRetrievingLanguage(): void
    {
        $request = new CreateHotelTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            website: 'http://my.website.test',
            hotelName: 'My Hotel',
            subject: 'My subject',
            message: 'My message',
            languageId: 1
        );
        $command = new CreateHotelTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willThrowException(new \Exception());

        $this->expectException(\Exception::class);

        $this->hotelTicketDataBuilder->build($command);
    }

    public function testBuildCustomerFieldsWithoutLanguage(): void
    {
        $request = new CreateHotelTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            website: 'http://my.website.test',
            hotelName: 'My Hotel',
            subject: 'My subject',
            message: 'My message',
            languageId: null
        );
        $command = new CreateHotelTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn(null);

        $result = $this->hotelTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: [
                'website' => 'http://my.website.test',
            ]
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'hotel',
                ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
                ZendeskCustomFields::CITY => 'Paris',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildCustomerFieldsFull(): void
    {
        $longMessage = 'This is a very long message that should be truncated in the ticket subject line to just 50 characters...';
        $request = new CreateHotelTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            website: 'http://my.website.test',
            hotelName: 'My Hotel',
            subject: 'My subject',
            message: $longMessage,
            languageId: 1
        );
        $command = new CreateHotelTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->hotelTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: [
                'website' => 'http://my.website.test',
            ]
        );

        $expectedTicket = new TicketDTO(
            subject: 'This is a very long message that should be truncat...',
            comment: [
                'body' => $longMessage,
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'hotel',
                ZendeskCustomFields::HOTEL_NAME => 'My Hotel',
                ZendeskCustomFields::CITY => 'Paris',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }
}
