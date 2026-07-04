<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MobileLoginRequest;
use App\Http\Resources\Api\V1\MobileAuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function login(MobileLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::query()
            ->where('email', $validated['email'])
            ->with(['roles', 'studentProfile.program', 'studentProfile.major', 'studentProfile.classSection', 'teacherProfile.department'])
            ->first();

        if (! $user instanceof User || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $this->abortUnlessSupportedMobileRole($user);

        $token = $user
            ->createToken($validated['device_name'] ?? 'KAI Mobile App', ['mobile'])
            ->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                ...((new MobileAuthUserResource($user))->resolve($request)),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);
        $this->abortUnlessSupportedMobileRole($user);

        return response()->json([
            'data' => (new MobileAuthUserResource($user))->resolve($request),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'data' => [
                'revoked' => true,
                'user' => [
                    'id' => $user->id,
                ],
            ],
        ]);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user->loadMissing([
            'roles',
            'studentProfile.program',
            'studentProfile.major',
            'studentProfile.classSection',
            'teacherProfile.department',
        ]);
    }

    private function abortUnlessSupportedMobileRole(User $user): void
    {
        abort_unless($user->hasAnyRole(['student', 'teacher']), 403, 'KAI mobile auth is only available for student and teacher accounts.');
    }
}
