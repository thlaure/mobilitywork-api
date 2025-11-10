<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\Service\Finder;

use MobilityWork\Application\Service\Finder\LanguageFinder;
use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Domain\Port\Out\LanguageRepositoryPort;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LanguageFinderTest extends TestCase
{
    private LanguageRepositoryPort&MockObject $languageRepository;
    private LanguageFinder $languageFinder;

    public function setUp(): void
    {
        $this->languageRepository = $this->createMock(LanguageRepositoryPort::class);
        $this->languageFinder = new LanguageFinder($this->languageRepository);
    }

    public function testFindByIdRetrieveLanguage(): void
    {
        $expectedResult = new Language();

        $this->languageRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn($expectedResult);

        $result = $this->languageFinder->findById(1);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByIdLanguageDoesNotExist(): void
    {
        $expectedResult = null;

        $this->languageRepository->expects($this->once())
            ->method('findOneById')
            ->with(2)
            ->willReturn($expectedResult);

        $result = $this->languageFinder->findById(2);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByIdNullIndentifier(): void
    {
        $this->languageRepository->expects($this->never())
            ->method('findOneById');

        $result = $this->languageFinder->findById(null);

        $this->assertNull($result);
    }
}
