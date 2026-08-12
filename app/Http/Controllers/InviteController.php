<?php

namespace App\Http\Controllers;

use App\Models\Invite;

class InviteController extends Controller
{
    public function show(string $token)
    {
        $invite = Invite::where('token', $token)->first();

        if (! $invite || $invite->isExpired() || $invite->isUsed()) {
            return response()->view('invite.invalid', [], 404);
        }

        return view('invite.show', ['token' => $token]);
    }
}
