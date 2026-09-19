<?php

namespace App\Http\Service\Member;

use App\Models\Member;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberService
{
    public function paginate(array $filter): LengthAwarePaginator
    {
        return Member::query()
            ->when(
                isset($filter['search']),
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

    public function create(array $data): Member
    {
        return Member::query()->create($data);
    }
}


