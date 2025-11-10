<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreatePartnersTicket;

use MobilityWork\Application\UseCase\CreatePartnersTicket\CreatePartnersTicketCommand;
use MobilityWork\Application\UseCase\CreatePartnersTicket\CreatePartnersTicketHandler;
use MobilityWork\Application\UseCase\CreatePartnersTicket\PartnersTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Ticket\CreatePartnersTicketRequest;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreatePartnersTicketHandlerTest extends TestCase
{
    private TicketCreatorPort&MockObject $ticketCreator;
    private PartnersTicketDataBuilder&MockObject $partnersTicketDataBuilder;
    private CreatePartnersTicketHandler $handler;

    public function setUp(): void
    {
        $this->ticketCreator = $this->createMock(TicketCreatorPort::class);
        $this->partnersTicketDataBuilder = $this->createMock(PartnersTicketDataBuilder::class);

        $this->handler = new CreatePartnersTicketHandler($this->ticketCreator, $this->partnersTicketDataBuilder);
    }

    public function testInvokeSuccess(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message'
        );
        $command = new CreatePartnersTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606');
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'partner',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->partnersTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willReturn($ticketCreationDataDTO);

        $this->ticketCreator->expects($this->once())
            ->method('createOrUpdateUser')
            ->with($userDTO)
            ->willReturn(123);

        $updatedTicketDTO = $ticketDTO->withCustomField('requester_id', 123);

        $this->ticketCreator->expects($this->once())
            ->method('createTicket')
            ->with($updatedTicketDTO);

        ($this->handler)($command);
    }

    public function testInvokeBuildThrowsException(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message'
        );
        $command = new CreatePartnersTicketCommand($request);

        $this->partnersTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willThrowException(new \Exception('Build error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Build error');

        $this->ticketCreator->expects($this->never())->method('createOrUpdateUser');
        $this->ticketCreator->expects($this->never())->method('createTicket');

        ($this->handler)($command);
    }

    public function testInvokeCreateUserThrowsException(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message'
        );
        $command = new CreatePartnersTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606');
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'partner',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->partnersTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willReturn($ticketCreationDataDTO);

        $this->ticketCreator->expects($this->once())
            ->method('createOrUpdateUser')
            ->with($userDTO)
            ->willThrowException(new \Exception('Create user error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Create user error');

        $this->ticketCreator->expects($this->never())->method('createTicket');

        ($this->handler)($command);
    }

    public function testInvokeCreateTicketThrowsException(): void
    {
        $request = new CreatePartnersTicketRequest(
            firstName: 'John',
            lastName: 'Doe',
            phoneNumber: '0606060606',
            email: 'test@mail.test',
            message: 'My message'
        );
        $command = new CreatePartnersTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606');
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'partner',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->partnersTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willReturn($ticketCreationDataDTO);

        $this->ticketCreator->expects($this->once())
            ->method('createOrUpdateUser')
            ->with($userDTO)
            ->willReturn(123);

        $updatedTicketDTO = $ticketDTO->withCustomField('requester_id', 123);

        $this->ticketCreator->expects($this->once())
            ->method('createTicket')
            ->with($updatedTicketDTO)
            ->willThrowException(new \Exception('Create ticket error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Create ticket error');

        ($this->handler)($command);
    }
}
