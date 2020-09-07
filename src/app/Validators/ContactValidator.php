<?php

namespace VCComponent\Laravel\Contact\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;
use VCComponent\Laravel\Vicoders\Core\Validators\ValidatorInterface;

class ContactValidator extends AbstractValidator
{
    protected $rules = [
        ValidatorInterface::RULE_ADMIN_CREATE => [
            'email' => ['email'],
        ],
        ValidatorInterface::RULE_ADMIN_UPDATE => [
            'email' => ['email'],
        ],
        ValidatorInterface::RULE_CREATE => [
            'email' => ['email'],
        ],
        'RULE_EXPORT'                          => [
            'label'     => ['required'],
            'extension' => ['required', 'regex:/(^xlsx$)|(^csv$)/'],
        ],
    ];
}
