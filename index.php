<?php

require 'vendor/autoload.php';

use Pyjac\ORM\Model;

class User extends Model {

}

$users = User::getAll();
var_dump($users);
var_dump(User::find(1));
var_dump(User::destroy(2));
