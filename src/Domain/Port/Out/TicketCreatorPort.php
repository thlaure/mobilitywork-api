<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Port\Out;

use MobilityWork\Domain\Model\Data\TicketDTO;
use MobilityWork\Domain\Model\Data\UserDTO;

interface TicketCreatorPort
{
    /**
     * @return int the ID of the created or updated user
     */
    public function createOrUpdateUser(UserDTO $userData): int;

    /**
     * @return int the ID of the created ticket
     */
    public function createTicket(TicketDTO $ticketData): int;
}
