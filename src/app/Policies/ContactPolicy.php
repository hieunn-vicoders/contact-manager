<?php

namespace VCComponent\Laravel\Contact\Policies;

use VCComponent\Laravel\Contact\Contracts\ContactPolicyInterface;

class ContactPolicy implements ContactPolicyInterface 
{
    public function before($user, $ability)
    {
        if ($user->isAdministrator()) {
            return true;
        }
    }

    public function manage($user)
    {
        return $user->hasPermission('manage-contact');
    }
}