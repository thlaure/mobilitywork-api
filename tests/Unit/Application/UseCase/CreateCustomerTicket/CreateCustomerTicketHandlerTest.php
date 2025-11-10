<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreateCustomerTicket;

use MobilityWork\Application\UseCase\CreateCustomerTicket\CreateCustomerTicketCommand;
use MobilityWork\Application\UseCase\CreateCustomerTicket\CreateCustomerTicketHandler;
use MobilityWork\Application\UseCase\CreateCustomerTicket\CustomerTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Ticket\CreateCustomerTicketRequest;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use MobilityWork\Infrastructure\Zendesk\Constants\ZendeskCustomFields;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateCustomerTicketHandlerTest extends TestCase
{
    private TicketCreatorPort&MockObject $ticketCreator;
    private CustomerTicketDataBuilder&MockObject $customerTicketDataBuilder;
    private CreateCustomerTicketHandler $handler;

    public function setUp(): void
    {
        $this->ticketCreator = $this->createMock(TicketCreatorPort::class);
        $this->customerTicketDataBuilder = $this->createMock(CustomerTicketDataBuilder::class);

        $this->handler = new CreateCustomerTicketHandler($this->ticketCreator, $this->customerTicketDataBuilder);
    }

    public function testInvokeSuccess(): void
    {
        $request = new CreateCustomerTicketRequest('John', 'Doe', '0606060606', 'test@mail.test', 'My message', 'RES001', 1, 1);
        $command = new CreateCustomerTicketCommand($request);

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

        $this->customerTicketDataBuilder->expects($this->once())
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

        ($this->handler)($command);
    }
}
