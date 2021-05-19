<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:05
 */
use Atxy2k\Essence\Infrastructure\Validator;

class UsersValidator extends Validator
{
    protected $rules = [
        'register' => [
            'first_name'    => 'required|string|bail',
            'last_name'     => 'required|string|bail',
            'email'         => 'required|string|email|confirmed|unique:users,email|bail',
            'password'      => 'required_if:asign_password,1|confirmed|bail',
            'roles'         => 'required|array|bail'
        ],
        'authenticate' => [
            'email' => 'required',
            'password' => 'required'
        ],
        'reset-password' => [
            'old_password' => 'required',
            'password'     => 'required|confirmed|bail',
        ],
        'change-password' => [
            'password'     => 'required|confirmed|bail',
        ],
        'remove-admin-privileges' => [
            'roles' => 'required|array'
        ],
        'update' => [
            'first_name' => 'required',
            'last_name'  => 'required'
        ],
        'request-password-recovery' => [
            'email' => 'required'
        ],
        'encoded-password-recovery-token' => [
            'email' => 'required',
            'date'  => 'required'
        ]
    ];
}
