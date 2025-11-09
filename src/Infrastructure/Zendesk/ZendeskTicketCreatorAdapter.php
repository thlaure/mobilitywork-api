<?php

declare(strict_types=1);

namespace MobilityWork\Infrastructure\Zendesk;

use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;
use MobilityWork\Domain\Port\Out\TicketCreatorPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zendesk\API\HttpClient as ZendeskAPI;

class ZendeskTicketCreatorAdapter implements TicketCreatorPort
{
    public function __construct(
        private readonly ZendeskAPI $client,
        private readonly LoggerInterface $logger,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function createOrUpdateUser(UserDTO $userData): int
    {
        /** @var array<string,mixed> $zendeskUserData */
        $zendeskUserData = $this->normalizer->normalize($userData);

        try {
            $response = $this->client->users()->createOrUpdate($zendeskUserData);
        } catch (\Exception $exception) {
            $this->logger->error('Error creating or updating Zendesk user: '.$exception->getMessage(), ['userData' => $zendeskUserData]);
            throw new \RuntimeException('Failed to create or update Zendesk user.', $exception->getCode(), $exception);
        }

        if (empty($response->user->id)) {
            throw new \RuntimeException('Failed to create or update Zendesk user.');
        }

        return $response->user->id;
    }

    public function createTicket(TicketDTO $ticketData): int
    {
        /** @var array<string,mixed> $zendeskTicketData */
        $zendeskTicketData = $this->normalizer->normalize($ticketData);

        try {
            $response = $this->client->tickets()->create($zendeskTicketData);
        } catch (\Exception $exception) {
            $this->logger->error('Error creating Zendesk ticket: '.$exception->getMessage(), ['ticketData' => $zendeskTicketData]);
            throw new \RuntimeException('Failed to create Zendesk ticket.', $exception->getCode(), $exception);
        }

        if (empty($response->ticket->id)) {
            throw new \RuntimeException('Failed to create Zendesk ticket.');
        }

        return $response->ticket->id;
    }
}
