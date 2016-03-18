
[![Build Status](https://travis-ci.org/andela-joyebanji/PotatoORM.svg?branch=develop)](https://travis-ci.org/andela-joyebanji/PotatoORM) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andela-joyebanji/PotatoORM/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/andela-joyebanji/PotatoORM/?branch=develop) [![StyleCI](https://styleci.io/repos/53060668/shield)](https://styleci.io/repos/53060668) [![Coverage Status](https://coveralls.io/repos/github/andela-joyebanji/PotatoORM/badge.svg?branch=develop)](https://coveralls.io/github/andela-joyebanji/PotatoORM?branch=develop)

# Potato ORM
Potato ORM is a very simple agnostic ORM that can perform the basic CRUD database operations. It is an implementation of the Checkpoint 2 requirement for PHP developers at Andela.

## Installation

Require via composer like so:

```
    composer require pyjac/orm
```

Supported database engines
=======================
    1. MySQL 
    2. Postgres 
    3. SQLite
    
You also need set your environment variables to define your database parameters or create a `.env` file in your project.

    DRIVER   = sqlite
    HOSTNAME = 127.0.0.1
    USERNAME = pyjac
    PASSWORD = pyjac
    DBNAME   = potatoORM
    PORT     = 54320

## Usage

Models that needs to perform CRUD operations on the database need to extend the `Pyjac\ORM\Model` class.
For example:

```php

use Pyjac\ORM\Model;

class User extends Model 
{

}
```  

### Table Names

Note that we did not specify which table to use for our `User` model. The plural name of the class will be unless another name is explicitly specified. So, in this case, PotatoORM will assume the `User` model stores records in the users table. You may specify a custom table by defining a table property on your model:

```php

use Pyjac\ORM\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
}
``` 
        
**To create a new instance of the User class, you do:**

```php
$user = new User();
```

**To create a new record in the database, you do:**

```php
$user = new User();
$user->name = "Pyjac";
$user->age = 200;
$user->save();
```

`(Note) The above assumes you've created a table named users with columns id, name and age in the database.`


**To fetch all the Model of the class from the database, you do:**

```php
$users = User::getAll();
var_dump($users);
```

**To find a Model with a specific id in the database, you do:**

```php
$user = User::find(3);
```

**To delete a Model from the database, you do:**

```php
$user = User::destroy(1);
```

**Updating an existing Model in the database:**

```php
$user = User::find(1);
$user->name = "Nandaa";
$user->save();
```
`NB: The save() method checks first to see if the id exists. if yes, it calls the update method else calls the create method` 

### Exceptions

* `DatabaseDriverNotSupportedException`: This exception is thrown when database driver in not supported.
	
* `ModelNotFoundException`: This exception is thrown when model the find method is trying to get from the database does not exist.


## Security

If you discover any security related issues, please email [Oyebanji Jacob](oyebanji.jacob@andela.com) or create an issue.

## Credits

[Oyebanji Jacob](https://github.com/andela-joyebanji)

## License

### The MIT License (MIT)

Copyright (c) 2016 Oyebanji Jacob <oyebanji.jacob@andela.com>

> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
>
> The above copyright notice and this permission notice shall be included in
> all copies or substantial portions of the Software.
>
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
> THE SOFTWARE.
