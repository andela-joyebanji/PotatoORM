<?php

require 'vendor/autoload.php';

use Pyjac\ORM\Model;

class User extends Model
{
}

$users = User::getAll();
var_dump($users);

//$u = User::find(11);
//$u->name = "NaPeacer";
//var_dump($u->save());

//$user = new User;
//$user->age = 50000;
//$user->name = "Nannna 32";
//$user->save(); // saves the car details in the Car table.
//var_dump($user->save());
//var_dump(User::destroy(2));
