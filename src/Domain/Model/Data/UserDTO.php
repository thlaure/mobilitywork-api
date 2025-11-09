<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Data;

final class UserDTO
{
    /**
     * @param array<string,mixed> $userFields
     */
    public function __construct(
        public ?string $email,
        public ?string $name,
        public ?string $phone,
        public string $role = 'end-user',
        public array $userFields = [],
    ) {
    }
}
