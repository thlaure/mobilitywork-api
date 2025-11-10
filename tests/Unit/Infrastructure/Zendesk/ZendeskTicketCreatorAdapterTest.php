<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Infrastructure\Zendesk;

use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Infrastructure\Zendesk\ZendeskTicketCreatorAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zendesk\API\HttpClient as ZendeskAPI;
use Zendesk\API\Resources\Core\Tickets;
use Zendesk\API\Resources\Core\Users;

final class ZendeskTicketCreatorAdapterTest extends TestCase
{
    private ZendeskTicketCreatorAdapter $zendeskAdapter;
    private ZendeskAPI&MockObject $client;
    private LoggerInterface&MockObject $logger;
    private NormalizerInterface&MockObject $normalizer;

    public function setUp(): void
    {
        $this->client = $this->createMock(ZendeskAPI::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);

        $this->zendeskAdapter = new ZendeskTicketCreatorAdapter($this->client, $this->logger, $this->normalizer);
    }

    public function testCreateOrUpdateUserSuccess(): void
    {
        $userData = new UserDTO('john@example.com', 'John Doe', '0606060606');
        $zendeskUserData = [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '0606060606',
        ];

        $this->normalizer
            ->method('normalize')
            ->with($userData)
            ->willReturn($zendeskUserData);

        $response = (object) ['user' => (object) ['id' => 1]];
        $zendeskUserApi = $this->createMock(Users::class);
        $zendeskUserApi
            ->method('createOrUpdate')
            ->with($zendeskUserData)
            ->willReturn($response);

        $this->client->expects($this->once())
            ->method('__call')
            ->with('users')
            ->willReturn($zendeskUserApi);

        $this->logger->expects($this->never())->method('error');

        $result = $this->zendeskAdapter->createOrUpdateUser($userData);

        $expectedId = 1;
        $this->assertSame($expectedId, $result);
    }

    public function testCreateOrUpdateUserThrowsException(): void
    {
        $userData = new UserDTO('john@example.com', 'John Doe', '0606060606');
        $zendeskUserData = [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '0606060606',
        ];

        $this->normalizer
            ->method('normalize')
            ->with($userData)
            ->willReturn($zendeskUserData);

        $zendeskUserApi = $this->createMock(Users::class);
        $zendeskUserApi
            ->method('createOrUpdate')
            ->with($zendeskUserData)
            ->willThrowException(new \Exception());

        $this->client->expects($this->once())
            ->method('__call')
            ->with('users')
            ->willReturn($zendeskUserApi);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Error creating or updating Zendesk user: ', ['userData' => $zendeskUserData]);

        $this->expectException(\RuntimeException::class);

        $this->zendeskAdapter->createOrUpdateUser($userData);
    }

    public function testCreateOrUpdateUserThrowsExceptionOnEmptyId(): void
    {
        $userData = new UserDTO('john@example.com', 'John Doe', '0606060606');
        $zendeskUserData = [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '0606060606',
        ];

        $this->normalizer
            ->method('normalize')
            ->with($userData)
            ->willReturn($zendeskUserData);

        $response = (object) ['user' => (object) ['id' => null]];
        $zendeskUserApi = $this->createMock(Users::class);
        $zendeskUserApi
            ->method('createOrUpdate')
            ->with($zendeskUserData)
            ->willReturn($response);

        $this->client->expects($this->once())
            ->method('__call')
            ->with('users')
            ->willReturn($zendeskUserApi);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Created or updated Zendesk user has no id.', ['userData' => $zendeskUserData]);

        $this->expectException(\RuntimeException::class);

        $this->zendeskAdapter->createOrUpdateUser($userData);
    }

    public function testCreateTicketSuccess(): void
    {
        $ticketData = new TicketDTO(
            subject: 'Subject',
            comment: ['body' => 'Comment']
        );

        $normalizedData = [
            'subject' => 'Subject',
            'comment' => ['body' => 'Comment'],
        ];

        $this->normalizer
            ->method('normalize')
            ->with($ticketData)
            ->willReturn($normalizedData);

        $response = (object) ['ticket' => (object) ['id' => 1]];

        $zendeskTicketApi = $this->createMock(Tickets::class);
        $zendeskTicketApi
            ->method('create')
            ->with($normalizedData)
            ->willReturn($response);

        $this->client->expects($this->once())
            ->method('__call')
            ->with('tickets')
            ->willReturn($zendeskTicketApi);

        $this->logger->expects($this->never())->method('error');

        $result = $this->zendeskAdapter->createTicket($ticketData);

        $expectedId = 1;
        $this->assertSame($expectedId, $result);
    }

    public function testCreateTicketThrowsException(): void
    {
        $ticketData = new TicketDTO(
            subject: 'Subject',
            comment: ['body' => 'Comment']
        );

        $normalizedData = [
            'subject' => 'Subject',
            'comment' => ['body' => 'Comment'],
        ];

        $this->normalizer
            ->method('normalize')
            ->with($ticketData)
            ->willReturn($normalizedData);

        $zendeskTicketApi = $this->createMock(Tickets::class);
        $zendeskTicketApi
            ->method('create')
            ->with($normalizedData)
            ->willThrowException(new \Exception());

        $this->client->expects($this->once())
            ->method('__call')
            ->with('tickets')
            ->willReturn($zendeskTicketApi);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Error creating Zendesk ticket: ', ['ticketData' => $normalizedData]);

        $this->expectException(\RuntimeException::class);

        $this->zendeskAdapter->createTicket($ticketData);
    }

    public function testCreateTicketThrowsExceptionOnEmptyId(): void
    {
        $ticketData = new TicketDTO(
            subject: 'Subject',
            comment: ['body' => 'Comment']
        );

        $normalizedData = [
            'subject' => 'Subject',
            'comment' => ['body' => 'Comment'],
        ];

        $this->normalizer
            ->method('normalize')
            ->with($ticketData)
            ->willReturn($normalizedData);

        $response = (object) ['ticket' => (object) ['id' => null]];

        $zendeskTicketApi = $this->createMock(Tickets::class);
        $zendeskTicketApi
            ->method('create')
            ->with($normalizedData)
            ->willReturn($response);

        $this->client->expects($this->once())
            ->method('__call')
            ->with('tickets')
            ->willReturn($zendeskTicketApi);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Created Zendesk ticket has no id.', ['ticketData' => $normalizedData]);

        $this->expectException(\RuntimeException::class);

        $this->zendeskAdapter->createTicket($ticketData);
    }
}
