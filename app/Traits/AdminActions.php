<?php

namespace App\Traits;

trait AdminActions
{
    public function before($user, $ability)
    {
        if ($user->admin === true || $user->admin === "1") {
            return true;
        }
    }
}
