<?php

namespace VCComponent\Laravel\Contact\Policies;

use VCComponent\Laravel\Contact\Contracts\ContactPolicyInterface;

class ContactPolicy implements ContactPolicyInterface 
{
    public function ableToUse($user)
    {
        return true;
    }
}