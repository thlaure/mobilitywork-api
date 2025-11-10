<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Application\Service\Finder;

use MobilityWork\Application\Service\Finder\ReservationFinder;
use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Domain\Port\Out\ReservationRepositoryPort;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ReservationFinderTest extends TestCase
{
    private ReservationRepositoryPort&MockObject $resevationRepository;
    private ReservationFinder $reservationFinder;

    public function setUp(): void
    {
        $this->resevationRepository = $this->createMock(ReservationRepositoryPort::class);
        $this->reservationFinder = new ReservationFinder($this->resevationRepository);
    }

    public function testGetByRefRetrieveReference(): void
    {
        $expectedResult = new Reservation();

        $this->resevationRepository->expects($this->once())
            ->method('getByRef')
            ->with('A1')
            ->willReturn($expectedResult);

        $result = $this->reservationFinder->getByRef('A1');

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetByRefReferenceDoesNotExist(): void
    {
        $expectedResult = null;

        $this->resevationRepository->expects($this->once())
            ->method('getByRef')
            ->with('B1')
            ->willReturn($expectedResult);

        $result = $this->reservationFinder->getByRef('B1');

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetByRefEmptyReference(): void
    {
        $this->resevationRepository->expects($this->never())
            ->method('getByRef');

        $result = $this->reservationFinder->getByRef(null);

        $this->assertNull($result);
    }
}
