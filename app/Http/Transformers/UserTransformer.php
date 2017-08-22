<?php

namespace App\Http\Transformers;

use App\Entity\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'               => $user->id,
            'first_name'       => $user->first_name,
            'last_name'        => $user->last_name,
            'email'            => $user->email,
            'activated'        => $user->activated,
            'first_time_login' => $user->first_time_login,
            'created_at'       => $user->created_at->toDateTimeString(),
            'updated_at'       => $user->updated_at->toDateTimeString(),
            'avatar'           => $user->avatar,
            'mobile'           => $user->mobile,
            'company'          => $user->company,
            'role'             => $user->roles->pluck('name'),
        ];
    }
}
