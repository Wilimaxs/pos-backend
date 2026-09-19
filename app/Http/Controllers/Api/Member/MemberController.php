<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\MemberRequest;
use App\Http\Resources\Member\MemberResource;
use App\Http\Service\Member\MemberService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService
    )
    {
    }

    public function memberList(MemberRequest $request): JsonResponse
    {
        $members = $this->memberService->paginate(
            $request->validated(),
        );

        $data = MemberResource::collection(
            $members->getCollection()
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar Member Berhasil Diambil',
            data: $data,
            meta: [
                'current_page' => $members->currentPage(),
                'per_page' => $members->perPage(),
                'total_page' => $members->lastPage(),
                'total_data' => $members->total(),
            ]
        );
    }
}
