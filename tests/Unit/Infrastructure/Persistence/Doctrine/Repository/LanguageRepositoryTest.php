<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository;

use MobilityWork\Domain\Model\Entity\Language;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\LanguageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LanguageRepositoryTest extends TestCase
{
    private LanguageRepository&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(LanguageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
    }

    public function testFindOneByIdSuccess(): void
    {
        $expectedLanguage = new Language();
        $languageId = 1;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($languageId)
            ->willReturn($expectedLanguage);

        $result = $this->repository->findOneById($languageId);

        $this->assertSame($expectedLanguage, $result);
    }

    public function testFindOneByIdReturnsNullWhenNotFound(): void
    {
        $languageId = 999;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($languageId)
            ->willReturn(null);

        $result = $this->repository->findOneById($languageId);

        $this->assertNull($result);
    }

    public function testFindOneByIdThrowsException(): void
    {
        $languageId = 123;
        $exception = new \Exception('Something went wrong');

        $this->repository->expects($this->once())
            ->method('find')
            ->with($languageId)
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->repository->findOneById($languageId);
    }
}
