<?php

namespace App\Http\Controllers\V1;

use App\Models\Ticket;
use App\Http\Controllers\V1\ApiController;
use App\Http\Requests\V1\TicketLeaseRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class TicketLeaseController extends ApiController
{
    use AuthorizesRequests;

    /**
     * Set a lease to the specified ticket from storage.
     */
    public function lease(TicketLeaseRequest $request, Ticket $ticket) : JsonResponse 
    {
        $this->authorize('lease', $ticket);

        $ticket->update($request->validated());

        $ticket->refresh();

        Cache::flush();

        return $this->successResponse(
            new TicketResource($ticket, 'update'), 
            'Ticket Leased Sucessfully'
        );
    }

    public function unlease(Ticket $ticket) : JsonResponse 
    {
        $this->authorize('unlease', $ticket);

        $ticket->update([
            'leased_to_id' => null
        ]);

        $ticket->refresh();

        Cache::flush();

        return $this->successResponse(
            new TicketResource($ticket, 'update'), 
            'Ticket Unleased Sucessfully'
        );
    }

}
