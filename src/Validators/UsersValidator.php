<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:05
 */
use Atxy2k\Essence\Infraestructure\Validator;

class UsersValidator extends Validator
{
    protected $rules = [
        'login' => [
            'email'     => 'required|email|bail',
            'password'  => 'required'
        ],
        'changePassword' =>
        [
            'before_password'   => 'required',
            'password'          => 'required|confirmed|bail'
        ],
        'reset_password' =>
        [
            'password'          => 'required|confirmed|bail'
        ],
        'changeEmail' =>
        [
            'email'          => 'required|confirmed|bail'
        ],
        'validate_reminder' =>
        [
            'email'             => 'required|bail',
            'code'              => 'required|bail',
        ],
        'update_password_from_reminder' =>
        [
            'token'             => 'required',
            'password'          => 'required|confirmed',
        ],
        'register' =>
        [
            'first_name'    => 'required|string|bail',
            'last_name'     => 'required|string|bail',
            'email'         => 'required|string|email|confirmed|unique:users,email|bail',
            'password'      => 'required_if:asign_password,1|confirmed|bail',
            'roles'         => 'required|array|bail',
            'asign_password' => 'required_with:password'
        ],
        'activate' => [
            'email'             => 'required',
            'code'              => 'required',
        ],
        'update' => [
            'first_name'    => 'required|string|bail',
            'last_name'     => 'required|string|bail',
        ],
        'change-admin-role' => [
            'roles'		=> 'required|array'
        ],
    ];
}
