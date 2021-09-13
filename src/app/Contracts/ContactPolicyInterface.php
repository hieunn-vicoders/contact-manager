<?php

namespace VCComponent\Laravel\Contact\Contracts;

interface ContactPolicyInterface 
{
    public function ableToUse($user);
}