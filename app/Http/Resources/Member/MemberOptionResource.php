<?php

namespace App\Http\Resources\Member;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberOptionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'member_code' => $this->member_code,
            'name' => $this->name,
            'phone' => $this->phone,
        ];
    }
}
