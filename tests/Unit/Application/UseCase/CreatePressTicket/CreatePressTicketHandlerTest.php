<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\UseCase\CreatePressTicket;

use MobilityWork\Application\UseCase\CreatePressTicket\CreatePressTicketCommand;
use MobilityWork\Application\UseCase\CreatePressTicket\CreatePressTicketHandler;
use MobilityWork\Application\UseCase\CreatePressTicket\PressTicketDataBuilder;
use MobilityWork\Domain\Model\Data\TicketCreationDataDTO;
use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Model\Ticket\CreatePressTicketRequest;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreatePressTicketHandlerTest extends TestCase
{
    private TicketCreatorPort&MockObject $ticketCreator;
    private PressTicketDataBuilder&MockObject $pressTicketDataBuilder;
    private CreatePressTicketHandler $handler;

    public function setUp(): void
    {
        $this->ticketCreator = $this->createMock(TicketCreatorPort::class);
        $this->pressTicketDataBuilder = $this->createMock(PressTicketDataBuilder::class);

        $this->handler = new CreatePressTicketHandler($this->ticketCreator, $this->pressTicketDataBuilder);
    }

    public function testInvokeSuccess(): void
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
            reservationNumber: 'RES-123'
        );
        $command = new CreatePressTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606', 'end-user', ['press_media' => 'My Media']);
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'press',
                'city' => 'Paris',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->pressTicketDataBuilder->expects($this->once())
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
            reservationNumber: 'RES-123'
        );
        $command = new CreatePressTicketCommand($request);

        $this->pressTicketDataBuilder->expects($this->once())
            ->method('build')
            ->with($command)
            ->willThrowException(new \Exception());

        $this->expectException(\Exception::class);

        $this->ticketCreator->expects($this->never())->method('createOrUpdateUser');
        $this->ticketCreator->expects($this->never())->method('createTicket');

        ($this->handler)($command);
    }

    public function testInvokeCreateUserThrowsException(): void
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
            reservationNumber: 'RES-123'
        );
        $command = new CreatePressTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606', 'end-user', ['press_media' => 'My Media']);
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'press',
                'city' => 'Paris',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->pressTicketDataBuilder->expects($this->once())
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
            reservationNumber: 'RES-123'
        );
        $command = new CreatePressTicketCommand($request);

        $userDTO = new UserDTO('test@mail.test', 'John DOE', '0606060606', 'end-user', ['press_media' => 'My Media']);
        $ticketDTO = new TicketDTO(
            subject: 'My message',
            comment: [
                'body' => 'My message',
            ],
            customFields: [
                'ticket_type' => 'press',
                'city' => 'Paris',
            ]
        );
        $ticketCreationDataDTO = new TicketCreationDataDTO($userDTO, $ticketDTO);

        $this->pressTicketDataBuilder->expects($this->once())
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
