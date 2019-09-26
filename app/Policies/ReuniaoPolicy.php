<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Reuniao;

class ReuniaoPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function cancelaReuniao(User $user, Reuniao $reuniao) {
        return $user->id == $reuniao->id_solicitante;
    }
}
