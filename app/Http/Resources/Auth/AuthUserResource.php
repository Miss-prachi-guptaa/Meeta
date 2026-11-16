<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'first_name' => $this->first_name,

            'last_name' => $this->last_name,

            'display_name' => $this->display_name,

            'email' => $this->email,

            'phone' => $this->phone,

            'status' => $this->status,

            'gender' => $this->gender,

            'date_of_birth' => $this->date_of_birth,

            'profile_image' => $this->profile_image,

            'last_login_at' => $this->last_login_at,

            'role' => $this->getRoleNames()->first(),
        ];
    }
}