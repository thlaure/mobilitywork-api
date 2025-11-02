<?php

declare(strict_types=1);

namespace MobilityWork\Domain\Port\Out;

use MobilityWork\Domain\Model\Entity\Language;

interface LanguageRepositoryPort
{
    public function findOneById(int $id): ?Language;
}
