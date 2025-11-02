<?php

namespace App\Http\Controllers\V1;

use App\Models\Ticket;
use App\Http\Controllers\V1\ApiController;
use App\Http\Requests\V1\TicketStoreRequest;
use App\Http\Requests\V1\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class TicketController extends ApiController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the ticket.
     */
    public function index(Request $request) : JsonResponse
    {
        $id = $request->user()->id;
        $pages = (int) $request->get('page', 1);

        $cacheKey = sprintf('user:%d:tickets:page:%d', $id, $pages);

        $tickets = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($id) {
            return Ticket::with(['creator', 'lessee'])
                        ->where(fn ($att) => 
                            $att->where('creator_id', $id)
                                ->orWhere('leased_to_id', $id))
                        ->latest()
                        ->paginate(10);
        });

        return $this->successResponse(
            TicketResource::collection($tickets, 'index'), 
            'All Related Tickets Retrieved Successfully'
        );
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(TicketStoreRequest $request) : JsonResponse
    {
        $ticket = $request->user()->createdTickets()->create($request->validated())->refresh();

        Cache::flush();

        return $this->createdResponse(
            new TicketResource($ticket, 'store'), 
            'Ticket Created Sucessfully'
        );
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket) : JsonResponse
    {
        $this->authorize('view', $ticket);

        return $this->successResponse(
            new TicketResource($ticket, 'show'),
            'Ticket Retrieved Sucessfully'
        );
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket) : JsonResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());
        $ticket->refresh();

        Cache::flush();

        return $this->successResponse(
            new TicketResource($ticket, 'update'), 
            'Ticket Updated Sucessfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket) : JsonResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        Cache::flush();

        return $this->successResponse(
            null, 
            'Ticket Deleted Sucessfully'
        );
    }
}
