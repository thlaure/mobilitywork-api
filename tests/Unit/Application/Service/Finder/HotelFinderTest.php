<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\Service\Finder;

use MobilityWork\Application\Service\Finder\HotelFinder;
use MobilityWork\Domain\Model\Entity\Hotel;
use MobilityWork\Domain\Port\Out\HotelRepositoryPort;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class HotelFinderTest extends TestCase
{
    private HotelRepositoryPort&MockObject $hotelRepository;
    private HotelFinder $hotelFinder;

    public function setUp(): void
    {
        $this->hotelRepository = $this->createMock(HotelRepositoryPort::class);
        $this->hotelFinder = new HotelFinder($this->hotelRepository);
    }

    public function testFindByIdRetrieveHotel(): void
    {
        $expectedResult = new Hotel();

        $this->hotelRepository->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn($expectedResult);

        $result = $this->hotelFinder->findById(1);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByIdHotelDoesNotExist(): void
    {
        $expectedResult = null;

        $this->hotelRepository->expects($this->once())
            ->method('findOneById')
            ->with(2)
            ->willReturn($expectedResult);

        $result = $this->hotelFinder->findById(2);

        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByIdNullIndentifier(): void
    {
        $this->hotelRepository->expects($this->never())
            ->method('findOneById');

        $result = $this->hotelFinder->findById(null);

        $this->assertNull($result);
    }
}
