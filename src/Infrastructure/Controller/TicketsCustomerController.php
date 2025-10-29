<?php

declare(strict_types=1);

namespace MobilityWork\Infrastructure\Controller;

use MobilityWork\Application\UseCase\CreateCustomerTicket\CreateCustomerTicketCommand;
use MobilityWork\Domain\Model\Ticket\CreateCustomerTicketRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tickets/customer', name: 'app_tickets_customer', methods: [Request::METHOD_POST])]
final class TicketsCustomerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload]
        CreateCustomerTicketRequest $request,
    ): JsonResponse {
        $this->bus->dispatch(new CreateCustomerTicketCommand($request));

        return $this->json([
            'message' => 'Customer ticket creation request received.',
        ], Response::HTTP_ACCEPTED);
    }
}
