<?php

namespace App\Http\Controllers\V1;

use App\Models\Ticket;
use App\Http\Controllers\V1\ApiController;
use App\Http\Requests\V1\TicketCommentRequest;
use App\Http\Requests\V1\TicketLeaseRequest;
use App\Http\Requests\V1\TicketStoreRequest;
use App\Http\Requests\V1\TicketUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends ApiController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the ticket.
     */
    public function index(Request $request) : JsonResponse
    {
        $tickets = Ticket::with(['creator', 'lessee'])
            ->where('creator_id', $request->user()->id)
            ->orWhere('leased_to_id', $request->user()->id)
            ->latest()
            ->paginate(2);

        return $this->successResponse(TicketResource::collection($tickets, 'index'), 'All Related Tickets Retrieved Successfully');
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(TicketStoreRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $ticket = $request->user()->createdTickets()->create($validated);

        $ticket = $ticket->refresh();

        return $this->createdResponse(new TicketResource($ticket, 'store'), 'Ticket Created Sucessfully');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket) : JsonResponse
    {
        return $this->successResponse(new TicketResource($ticket, 'show'), 'Ticket Retrieved Sucessfully');
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket) : JsonResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validated();

        $ticket->update($validated);

        $ticket->refresh();

        return $this->successResponse(new TicketResource($ticket, 'update'), 'Ticket Updated Sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket) : JsonResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return $this->successResponse(null, 'Ticket Deleted Sucessfully');
    }
}
