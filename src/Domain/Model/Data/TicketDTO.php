<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Model\Data;

final class TicketDTO
{
    /**
     * @param array<string,mixed>     $comment
     * @param array<string|int,mixed> $customFields
     */
    public function __construct(
        public string $subject,
        public array $comment = [],
        public string $priority = 'normal',
        public string $type = 'question',
        public string $status = 'new',
        public array $customFields = [],
    ) {
    }

    public function withCustomField(string $key, mixed $value): self
    {
        $newCustomFields = $this->customFields;
        $newCustomFields[$key] = $value;

        return new self(
            subject: $this->subject,
            comment: $this->comment,
            priority: $this->priority,
            type: $this->type,
            status: $this->status,
            customFields: $newCustomFields
        );
    }
}
