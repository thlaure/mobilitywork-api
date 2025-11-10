<?php

declare(strict_types=1);

namespace MobilityWork\Tests\Unit\Infrastructure\Persistence\Doctrine\Repository;

use MobilityWork\Domain\Model\Entity\Reservation;
use MobilityWork\Infrastructure\Persistence\Doctrine\Repository\ReservationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ReservationRepositoryTest extends TestCase
{
    private ReservationRepository&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(ReservationRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();
    }

    public function testGetByRefSuccess(): void
    {
        $expectedReservation = new Reservation();
        $reference = 'REF123';

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['reference' => $reference])
            ->willReturn($expectedReservation);

        $result = $this->repository->getByRef($reference);

        $this->assertSame($expectedReservation, $result);
    }

    public function testGetByRefReturnsNullWhenNotFound(): void
    {
        $reference = 'NONEXISTENT';

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['reference' => $reference])
            ->willReturn(null);

        $result = $this->repository->getByRef($reference);

        $this->assertNull($result);
    }

    public function testGetByRefThrowsException(): void
    {
        $reference = 'ERRORREF';
        $exception = new \Exception('Database error');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['reference' => $reference])
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->repository->getByRef($reference);
    }
}
