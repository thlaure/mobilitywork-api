<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreateHotelTicket;

use MobilityWork\Application\UseCase\CreateHotelTicket\CreateHotelTicketCommand;
use MobilityWork\Application\UseCase\CreateHotelTicket\CreateHotelTicketHandler;
use MobilityWork\Application\UseCase\CreateHotelTicket\HotelTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateHotelTicketHandlerTest extends TestCase
{
    private TicketCreatorPort&MockObject $ticketCreator;
    private HotelTicketDataBuilder&MockObject $hotelTicketDataBuilder;
    private CreateHotelTicketHandler $handler;

    public function setUp(): void
    {
        $this->ticketCreator = $this->createMock(TicketCreatorPort::class);
        $this->hotelTicketDataBuilder = $this->createMock(HotelTicketDataBuilder::class);

        $this->handler = new CreateHotelTicketHandler($this->ticketCreator, $this->hotelTicketDataBuilder);
    }

    public function testInvokeSuccess(): void
    {
        $request = new CreateHotelTicketRequest('John', 'Doe', 'France', '0606060606', 'test@mail.test', 'Paris', 'https://my-hotel.com', 'My Hotel', 'My message', 'My message', 1);
        $command = new CreateHotelTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606');
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                ZendeskCustomFields::TICKET_TYPE => 'customer',
                ZendeskCustomFields::RESERVATION_NUMBER => 'RES001',
                'requester_id' => 1,
            ]
        );
        $expectedResult = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->hotelTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willReturn($expectedResult);

        $this->ticketCreator->expects($this->once())
            ->method('createOrUpdateUser')
            ->with($userDTO)
            ->willReturn(1);

        $this->ticketCreator->expects($this->once())
            ->method('createTicket')
            ->with($ticketDTO)
            ->willReturn(1);

        $this->handler->__invoke($command);
    }
}
