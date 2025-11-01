<?php

namespace App\Http\Controllers\V1;

use App\Models\Ticket;
use App\Http\Controllers\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\V1\TicketCommentRequest;
use App\Http\Resources\CommentResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends ApiController
{
    /**
     * Put a comment to the specified ticket from storage.
     */
    public function addComment(TicketCommentRequest $request, Ticket $ticket) : JsonResponse
    {
        $validated = $request->validated();

        $comment = $ticket->comments()->create([ 
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'content' => $validated['content']
        ]);

        return $this->createdResponse(new CommentResource($comment), 'Comment on Ticket Sucessfully');
    }

    /**
     * Get comments to the specified ticket from storage.
     */
    public function getComments(Ticket $ticket) : JsonResponse
    {
        $comments = $ticket->comments()->with(['user'])->latest()->paginate(5);

        return $this->successResponse(CommentResource::collection($comments), 'Get comment on Ticket Sucessfully');
    }
}
