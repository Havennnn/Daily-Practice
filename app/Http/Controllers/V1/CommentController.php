<?php

namespace App\Http\Controllers\V1;

use App\Models\Ticket;
use App\Http\Controllers\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\V1\TicketCommentRequest;
use App\Http\Resources\CommentResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CommentController extends ApiController
{
    /**
     * Get comments to the specified ticket from storage.
     */
    public function index(Request $request, Ticket $ticket) : JsonResponse
    {
        $id = $request->user()->id;
        $ticketId = $ticket->id;

        $cacheKey = sprintf('user:%d:tickets:%d:comments', $id, $ticketId);

        $comments = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($ticket) {
            return $ticket->comments()->with(['user'])->latest()->paginate(10);
        });

        return $this->successResponse(
            CommentResource::collection($comments), 
            'Get comment on Ticket Sucessfully'
        );
    }

    /**
     * Put a comment to the specified ticket from storage.
     */
    public function store(TicketCommentRequest $request, Ticket $ticket) : JsonResponse
    {
        $validated = $request->validated();

        $comment = $ticket->comments()->create([ 
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'content' => $validated['content']
        ])->refresh();

        Cache::flush();

        return $this->createdResponse(
            new CommentResource($comment), 
            'Comment on Ticket Sucessfully'
        );
    }
}
