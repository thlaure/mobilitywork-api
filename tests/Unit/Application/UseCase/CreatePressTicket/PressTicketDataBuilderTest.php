<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreatePressTicket;

use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Application\UseCase\CreatePressTicket\CreatePressTicketCommand;
use MobilityWork\Application\UseCase\CreatePressTicket\PressTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Ticket\CreatePressTicketRequest;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PressTicketDataBuilderTest extends TestCase
{
    private LanguageFinder&MockObject $languageFinder;
    private PressTicketDataBuilder $pressTicketDataBuilder;

    public function setUp(): void
    {
        $this->languageFinder = $this->createMock(LanguageFinder::class);
        $this->pressTicketDataBuilder = new PressTicketDataBuilder($this->languageFinder);
    }

    public function testBuildSuccess(): void
    {
        $request = new CreatePressTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            media: 'My Media',
            subject: 'My subject',
            message: 'My message',
            reservationNumber: 'RES-123',
            languageId: 1
        );
        $command = new CreatePressTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->pressTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: ['press_media' => 'My Media']
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'press',
                ZendeskCustomFields::CITY => 'Paris',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildThrowsExceptionRetrievingLanguage(): void
    {
        $request = new CreatePressTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            media: 'My Media',
            subject: 'My subject',
            message: 'My message',
            reservationNumber: 'RES-123',
            languageId: 1
        );
        $command = new CreatePressTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willThrowException(new \Exception());

        $this->expectException(\Exception::class);

        $this->pressTicketDataBuilder->build($command);
    }

    public function testBuildCustomerFieldsWithoutLanguage(): void
    {
        $request = new CreatePressTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            media: 'My Media',
            subject: 'My subject',
            message: 'My message',
            reservationNumber: 'RES-123',
            languageId: null
        );
        $command = new CreatePressTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn(null);

        $result = $this->pressTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: ['press_media' => 'My Media']
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'press',
                ZendeskCustomFields::CITY => 'Paris',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildCustomerFieldsFull(): void
    {
        $longMessage = 'This is a very long message that should be truncated in the ticket subject line to just 50 characters...';
        $request = new CreatePressTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            country: 'France',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            city: 'Paris',
            media: 'My Media',
            subject: 'My subject',
            message: $longMessage,
            reservationNumber: 'RES-123',
            languageId: 1
        );
        $command = new CreatePressTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->pressTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606',
            userFields: ['press_media' => 'My Media']
        );

        $expectedTicket = new TicketDTO(
            subject: 'This is a very long message that should be truncat...',
            comment: [
                'body' => $longMessage,
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'press',
                ZendeskCustomFields::CITY => 'Paris',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }
}
