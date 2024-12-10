<?php

namespace App\Http\Controllers\Api;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\ReligionEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function gender(): JsonResponse
    {
        $gender = collect(GenderEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray();
        return response()->json(['success' => true, 'data' => $gender], 200);
    }

    public function maritalStatus(): JsonResponse
    {
        $gender = collect(MaritalStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray();
        return response()->json(['success' => true, 'data' => $gender], 200);
    }

    public function bloodGroup(): JsonResponse
    {
        $paymentApproveStatus = collect(BloodGroupEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->customTitle()])->toArray();
        return response()->json(['success' => true, 'data' => $paymentApproveStatus], 200);
    }

    public function religion(): JsonResponse
    {
        $paymentApproveStatus = collect(ReligionEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->customTitle()])->toArray();
        return response()->json(['success' => true, 'data' => $paymentApproveStatus], 200);
    }
}
