<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StudentDataQueryRequest;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\User;
use App\Services\Mobile\VisibleAnnouncementQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function __invoke(StudentDataQueryRequest $request, VisibleAnnouncementQuery $announcements): AnonymousResourceCollection
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return NotificationResource::collection(
            $this->applyDateRange($announcements->forUser($user), $request, 'publish_at')
                ->latest('publish_at')
                ->latest()
                ->paginate($request->perPage()),
        );
    }

    private function applyDateRange(Builder $query, StudentDataQueryRequest $request, string $column): Builder
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->date('to'));
        }

        return $query;
    }
}
