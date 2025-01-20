<?php
namespace App\Http\Middleware;

use App\Helpers\AuthHelper;
use App\Jobs\ProcessLimit;
use App\Models\Asset;
use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psy\Util\Str;
use Symfony\Component\HttpFoundation\Response;

class IDPSubscriptionValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type): Response
    {
        $company_id = Auth::user()->selectedCompany->company_id;

        $subscription = Cache::remember(
            "company_subscription_{$company_id}",
            now()->addMinutes(1),
            fn() => AuthHelper::getCompanySubscription($company_id)
        );

        if (empty($subscription['data'])) {
            return response()->json(['error' => true, 'message' => 'No subscription found.'], 403);
        }

        $limitCheck = $this->checkLimit($type, $company_id, $subscription['data']);
        if (!$limitCheck['withinLimit']) {
            $limitType = $type === 'employee' ? 'Employee' : 'Asset';
            return response()->json([
                'error' => true,
                'message' => "{$limitType} limit exceeded. Please contact your administrator."
            ], 400);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 201 && $limitCheck['remaining'] <= 5) {
            $this->dispatchNotificationJob($type, $company_id, $limitCheck['remaining']);
        }

        return $response;
    }

    /**
     * Check if the type is within the subscription limit
     */
    private function checkLimit(string $type, int $company_id, array $subscriptionData): array
    {
        $count = match ($type) {
            'employee' => Employee::where('company_id', $company_id)->count(),
            'asset' => Asset::whereHas('assetCategory', fn($query) => $query->where('company_id', $company_id))->count(),
            default => 0,
        };

        $limitKey = $type === "employee" ? "pims_employee_limit" : "asset_limit";
        $remaining = $subscriptionData[$limitKey] - $count;

        return [
            'withinLimit' => $remaining > 0,
            'remaining' => $remaining
        ];
    }

    /**
     * Dispatch a notification job when the remaining limit is low
     */
    private function dispatchNotificationJob(string $type, int $company_id, int $remaining): void
    {
        ProcessLimit::dispatch($remaining-1, $type, Auth::user());
    }
}