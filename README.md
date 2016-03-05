
[![Build Status](https://travis-ci.org/andela-joyebanji/PotatoORM.svg?branch=develop)](https://travis-ci.org/andela-joyebanji/PotatoORM) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andela-joyebanji/PotatoORM/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/andela-joyebanji/PotatoORM/?branch=develop) [![StyleCI](https://styleci.io/repos/53060668/shield)](https://styleci.io/repos/53060668) [![Coverage Status](https://coveralls.io/repos/github/andela-joyebanji/PotatoORM/badge.svg?branch=develop)](https://coveralls.io/github/andela-joyebanji/PotatoORM?branch=develop)

# Potato ORM
Potato ORM is a very simple agnostic ORM that can perform the basic crud database operations.

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

## Usage

Models that needs to perform CRUD operations on the database need to extend the `Pyjac\ORM\Model` class.
For example:

```php

<?php

use Pyjac\ORM\Model;

class User extends Model {

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

        &user = User::find(1);
        $user->name = "Nandaa";
        $user->save();     
        
`NB: The save() method checks first to see if the id exists. if yes, it calls the upadte method else calls the create method` 

### Exceptions
	DatabaseDriverNotSupportedException: This exception is thrown when database driver in not supported.
	ModelNotFoundException: This exception is thrown when model the find method is trying to get from the database does not exist.


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