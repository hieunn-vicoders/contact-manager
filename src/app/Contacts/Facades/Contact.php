<?php

namespace VCComponent\Laravel\Contact\Contacts\Facades;

use Illuminate\Support\Facades\Facade;

class Contact extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moduleContact.contact';
    }
}
