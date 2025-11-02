<?php

declare(strict_types=1);

namespace MobilityWork\Infrastructure\Controller;

use MobilityWork\Application\UseCase\CreateHotelTicket\CreateHotelTicketCommand;
use MobilityWork\Domain\Model\Ticket\CreateHotelTicketRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tickets/hotel', name: 'app_tickets_hotel', methods: [Request::METHOD_POST])]
final class TicketsHotelController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload]
        CreateHotelTicketRequest $request,
    ): JsonResponse {
        $this->bus->dispatch(new CreateHotelTicketCommand($request));

        return $this->json([
            'message' => 'Hotel ticket creation request received.',
        ], Response::HTTP_ACCEPTED);
    }
}
