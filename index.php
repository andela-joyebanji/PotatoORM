<?php

require 'vendor/autoload.php';

use Pyjac\ORM\Model;

class User extends Model
{
    //protected $table = 'users';
}

$user = User::find(13);
echo "BEFORE\n";
var_dump($user);
$user->name = 'Uncle Pyjac';
$user->save();
echo "AFTER\n";
var_dump(User::find(13));
