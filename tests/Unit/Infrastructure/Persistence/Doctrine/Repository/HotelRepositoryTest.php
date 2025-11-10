<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository;

use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\HotelRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HotelRepositoryTest extends TestCase
{
    private HotelRepository&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(HotelRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
    }

    public function testFindOneByIdSuccess(): void
    {
        $expectedHotel = new Hotel();
        $hotelId = 1;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($hotelId)
            ->willReturn($expectedHotel);

        $result = $this->repository->findOneById($hotelId);

        $this->assertSame($expectedHotel, $result);
    }

    public function testFindOneByIdReturnsNullWhenNotFound(): void
    {
        $hotelId = 999;

        $this->repository->expects($this->once())
            ->method('find')
            ->with($hotelId)
            ->willReturn(null);

        $result = $this->repository->findOneById($hotelId);

        $this->assertNull($result);
    }

    public function testFindOneByIdThrowsException(): void
    {
        $hotelId = 123;
        $exception = new \Exception('Something went wrong');

        $this->repository->expects($this->once())
            ->method('find')
            ->with($hotelId)
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->repository->findOneById($hotelId);
    }
}
