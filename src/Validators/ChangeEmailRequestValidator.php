<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:28
 */
use Atxy2k\Essence\Infraestructure\Validator;

class ChangeEmailRequestValidator extends Validator
{
    protected $rules =
    [
        'create' => [
            'user_id'                   => 'required|integer',
            'before_email'              => 'required|email',
            'next_email'                => 'required|email',
        ],
        'confirm_process' => [
            'token_confirmation_change' => 'required|size:32',
            'email'                     => 'required|email',
        ],
        'confirm_process_mail' => [
            'token_confirmation_email'  => 'required|size:32',
            'email'                     => 'required|email',
        ],
    ];
}
