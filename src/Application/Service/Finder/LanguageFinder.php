<?php

declare(strict_types=1);

namespace MobilityWork\Application\Service\Finder;

use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;

class LanguageFinder
{
    public function __construct(
        private readonly LanguageRepositoryPort $languageRepository,
    ) {
    }

    public function findById(?int $languageId): ?Language
    {
        if (null === $languageId) {
            return null;
        }

        return $this->languageRepository->findOneById($languageId);
    }
}
