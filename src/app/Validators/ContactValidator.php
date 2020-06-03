<?php

namespace VCComponent\Laravel\Contact\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;
use VCComponent\Laravel\Vicoders\Core\Validators\ValidatorInterface;

class ContactValidator extends AbstractValidator
{
    protected $rules = [
        ValidatorInterface::RULE_ADMIN_CREATE => [
            'email' => ['required'],
        ],
        ValidatorInterface::RULE_ADMIN_UPDATE => [
            'email' => ['required'],
        ],
        ValidatorInterface::RULE_CREATE => [
            'email' => ['required'],
        ],
    ];
}
