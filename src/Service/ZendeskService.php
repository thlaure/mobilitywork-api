<?php

namespace MobilityWork\Service;

use Psr\Log\LoggerInterface;
use Zendesk\API\HttpClient as ZendeskAPI;

class ZendeskService extends AbstractService
{
    public function __construct(
        private readonly ZendeskAPI $client,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function createOrUpdateUser(array $userData): int
    {
        try {
            $response = $this->client->users()->createOrUpdate($userData);
        } catch (\Exception $exception) {
            $this->logger->error('Error creating or updating Zendesk user: '.$exception->getMessage(), ['userData' => $userData]);
            throw new \RuntimeException('Failed to create or update Zendesk user.', $exception->getCode(), $exception);
        }

        if (empty($response->user->id)) {
            throw new \RuntimeException('Failed to create or update Zendesk user.');
        }

        return $response->user->id;
    }

    public function createTicket(array $ticketData): int
    {
        try {
            $response = $this->client->tickets()->create($ticketData);
        } catch (\Exception $exception) {
            $this->logger->error('Error creating Zendesk ticket: '.$exception->getMessage(), ['ticketData' => $ticketData]);
            throw new \RuntimeException('Failed to create Zendesk ticket.', $exception->getCode(), $exception);
        }

        if (empty($response->ticket->id)) {
            throw new \RuntimeException('Failed to create Zendesk ticket.');
        }

        return $response->ticket->id;
    }
}
