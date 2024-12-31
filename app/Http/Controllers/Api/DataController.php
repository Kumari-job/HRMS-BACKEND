<?php

namespace App\Http\Controllers\Api;

use App\Enums\AssetStatusEnum;
use App\Enums\BloodGroupEnum;
use App\Enums\ContractTypeEnum;
use App\Enums\EmploymentTypeEnum;
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

    public function assetStatus(): JsonResponse
    {
        $assetStatus = collect(AssetStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray();
        return response()->json(['success' => true, 'data' => $assetStatus], 200);
    }
    public function employmentType(): JsonResponse
    {
        $employmentType = collect(EmploymentTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray();
        return response()->json(['success' => true, 'data' => $employmentType], 200);
    }

    public function maritalStatus(): JsonResponse
    {
        $maritalStatus = collect(MaritalStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray();
        return response()->json(['success' => true, 'data' => $maritalStatus], 200);
    }

    public function bloodGroup(): JsonResponse
    {
        $bloodGroup = collect(BloodGroupEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->customTitle()])->toArray();
        return response()->json(['success' => true, 'data' => $bloodGroup], 200);
    }

    public function religion(): JsonResponse
    {
        $religion = collect(ReligionEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->customTitle()])->toArray();
        return response()->json(['success' => true, 'data' => $religion], 200);
    }
    public function contractType(): JsonResponse
    {
        $contractType = collect(ContractTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->customTitle()])->toArray();
        return response()->json(['success' => true, 'data' => $contractType], 200);
    }
}
