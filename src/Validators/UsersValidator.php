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
            'email'     => 'required|email',
            'password'  => 'required'
        ],
        'changePassword' =>
        [
            'before_password'   => 'required',
            'password'          => 'required|confirmed'
        ],
        'reset_password' =>
        [
            'password'          => 'required|confirmed'
        ],
        'changeEmail' =>
        [
            'email'          => 'required|confirmed'
        ],
        'validate_reminder' =>
        [
            'email'             => 'required',
            'code'              => 'required',
        ],
        'update_password_from_reminder' =>
        [
            'token'             => 'required',
            'password'          => 'required|confirmed',
        ],
        'register' =>
        [
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'email'         => 'required|string|email|confirmed|unique:users,email',
            'password'      => 'required_if:asign_password,1|confirmed',
            'role_id'       => 'required|integer|exists:roles,id'
        ],
        'activate' => [
            'email'             => 'required',
            'code'              => 'required',
        ],
        'update' => [
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
        ],
    ];
}
