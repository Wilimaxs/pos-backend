<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CreateMemberRequest;
use App\Http\Requests\Member\MemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\Member\MemberOptionResource;
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
            filter: $request->validated(),
        );

        $data = MemberResource::collection(
            $members->getCollection()
        )->resolve($request);

        return ApiResponse::success(
            message: 'Daftar member berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $members->currentPage(),
                'per_page' => $members->perPage(),
                'total_page' => $members->lastPage(),
                'total_data' => $members->total(),
            ]
        );
    }

    public function update(
        UpdateMemberRequest $request,
        string              $memberCode
    ): JsonResponse
    {
        $this->memberService->update(
            memberCode: $memberCode,
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Member berhasil diperbarui',
        );
    }

    public function create(CreateMemberRequest $request): JsonResponse
    {
        $this->memberService->create(
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Member berhasil dibuat',
            statusCode: 201,
        );
    }

    public function options(MemberRequest $request): JsonResponse
    {
        $member = $this->memberService->options(
            filter: $request->validated(),
        );

        $data = MemberOptionResource::collection(
            $member->getCollection()
        )->resolve();

        return ApiResponse::success(
            message: 'Pilihan Member berhasil diambil',
            data: $data,
            meta: [
                'current_page' => $member->currentPage(),
                'per_page' => $member->perPage(),
                'total_page' => $member->lastPage(),
                'total_data' => $member->total(),
            ]
        );
    }
}
