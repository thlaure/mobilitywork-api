<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Application\UseCase\CreatePartnersTicket\CreatePartnersTicketCommand;
use MobilityWork\Application\UseCase\CreatePartnersTicket\PartnersTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Model\Ticket\CreatePartnersTicketRequest;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PartnersTicketDataBuilderTest extends TestCase
{
    private LanguageFinder&MockObject $languageFinder;
    private PartnersTicketDataBuilder $partnersTicketDataBuilder;

    public function setUp(): void
    {
        $this->languageFinder = $this->createMock(LanguageFinder::class);
        $this->partnersTicketDataBuilder = new PartnersTicketDataBuilder($this->languageFinder);
    }

    public function testBuildSuccess(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message',
            languageId: 1
        );
        $command = new CreatePartnersTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->partnersTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606'
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'partner',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildThrowsExceptionRetrievingLanguage(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message',
            languageId: 1
        );
        $command = new CreatePartnersTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('DB error');

        $this->partnersTicketDataBuilder->build($command);
    }

    public function testBuildCustomerFieldsWithoutLanguage(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message',
            languageId: null
        );
        $command = new CreatePartnersTicketCommand($request);

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn(null);

        $result = $this->partnersTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606'
        );

        $expectedTicket = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'partner',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }

    public function testBuildCustomerFieldsFull(): void
    {
        $longMessage = 'This is a very long message that should be truncated in the ticket subject line to just 50 characters...';
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: $longMessage,
            languageId: 1
        );
        $command = new CreatePartnersTicketCommand($request);

        $language = $this->createMock(Language::class);
        $language->method('getName')->willReturn('French');

        $this->languageFinder->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($language);

        $result = $this->partnersTicketDataBuilder->build($command);

        $expectedUser = new UserDTO(
            email: 'test@mail.test',
            name: 'John DOE',
            phone: '0606060606'
        );

        $expectedTicket = new TicketDTO(
            subject: 'This is a very long message that should be truncat...',
            comment: [
                'body' => $longMessage,
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'partner',
                ZendeskCustomFields::LANGUAGE_NAME => 'French',
            ]
        );

        $expectedResult = new TicketCreationDataDTO($expectedUser, $expectedTicket);

        $this->assertEquals($expectedResult, $result);
    }
}
