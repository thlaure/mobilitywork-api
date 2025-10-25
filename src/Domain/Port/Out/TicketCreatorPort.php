<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Port\Out;

interface TicketCreatorPort
{
    /**
     * @param array<string, mixed> $userData
     * @return int The ID of the created or updated user.
     */
    public function createOrUpdateUser(array $userData): int;

    /**
     * @param array<string, mixed> $ticketData
     * @return int The ID of the created ticket.
     */
    public function createTicket(array $ticketData): int;
}
