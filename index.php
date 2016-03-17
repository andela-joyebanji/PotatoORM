<?php

require 'vendor/autoload.php';

use Pyjac\ORM\Model;

class User extends Model
{
    //protected $table = 'users';
}

$user = User::find(2);
$user->name = 'jac';
$user->save();

$users = User::getAll();
var_dump($users);
