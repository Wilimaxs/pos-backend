<?php

namespace App\Http\Service\Member;

use App\Models\Member;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberService
{

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     *
     * GET ALL MEMBERS
     *
     */
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Member::query()
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('members.member_code', 'like', "%$search%")
                            ->orWhere('members.name', 'like', "%$search%");
                    });
                })
            ->when(
                array_key_exists('is_active', $filter),
                function ($query) use ($filter): void {
                    $query->where('members.is_active', $filter['is_active']);
                }
            )
            ->orderByDesc('members.created_at')
            ->orderBy('members.name')
            ->paginate(20);
    }

    /**
     * @param string $memberCode
     * @param array $data
     * @return void
     *
     * UPDATE MEMBER
     *
     */
    public function update(
        string $memberCode,
        array  $data,
    ): void
    {
        $member = Member::query()
            ->whereKey($memberCode)
            ->firstOrFail();

        $member->update($data);
    }

    /**
     * @param array $data
     * @return Member
     *
     * CREATE MEMBER
     *
     */
    public function create(array $data): Member
    {
        return Member::query()->create($data);
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     *
     * DROPDOWN MEMBER
     *
     */
    public function dropDown(array $filter): LengthAwarePaginator
    {
        return Member::query()
            ->select(
                'members.member_code',
                'members.name',
                'members.phone'
            )
            ->where('members.is_active', true)
            ->when(
                filled($filter['search'] ?? null),
                function ($query) use ($filter): void {
                    $search = trim($filter['search']);
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('members.member_code', 'like', "%$search%")
                            ->orWhere('members.name', 'like', "%$search%")
                            ->orWhere('members.phone', 'like', "%$search%");
                    });
                })
            ->orderBy('members.name')
            ->paginate(20);
    }
}


