<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Kai\KaiContextResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KaiContextController extends Controller
{
    public function __invoke(Request $request, KaiContextResolver $contextResolver): JsonResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return response()->json([
            'data' => $contextResolver->buildFor($user),
        ]);
    }
}
