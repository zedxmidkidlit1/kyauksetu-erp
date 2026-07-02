<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\KaiChatRequest;
use App\Models\User;
use App\Services\Kai\KaiResponder;
use Illuminate\Http\JsonResponse;

class KaiChatController extends Controller
{
    public function __invoke(KaiChatRequest $request, KaiResponder $responder): JsonResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $validated = $request->validated();

        return response()->json([
            'data' => $responder->respondFor(
                $user,
                $validated['message'],
                $request->header('X-Request-Id'),
            ),
        ]);
    }
}
