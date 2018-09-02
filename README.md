<p align="center">
    <img src="./resources/logo.png" alt="Hydrogen" />
</p>

<p align="center">
    <a href="https://travis-ci.org/rambler-digital-solutions/hydrogen"><img src="https://travis-ci.org/rambler-digital-solutions/hydrogen.svg?branch=master" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/rambler-digital-solutions/hydrogen/?branch=master"><img src="https://scrutinizer-ci.com/g/rambler-digital-solutions/hydrogen/badges/coverage.png?b=master" alt="Code coverage" /></a>
    <a href="https://scrutinizer-ci.com/g/rambler-digital-solutions/hydrogen/?branch=master"><img src="https://scrutinizer-ci.com/g/rambler-digital-solutions/hydrogen/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://packagist.org/packages/rds/hydrogen"><img src="https://poser.pugx.org/rds/hydrogen/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/rds/hydrogen"><img src="https://poser.pugx.org/rds/hydrogen/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/rambler-digital-solutions/hydrogen/master/LICENSE.md"><img src="https://poser.pugx.org/rds/hydrogen/license" alt="License MIT"></a>
</p>

> **The documentation is not finished at the moment.**

- [Introduction](#introduction)
- [Installation](#installation)
    - [Server Requirements](#server-requirements)
    - [Installing Hydrogen](#installing-hydrogen)
- [Usage](#usage)
- [Retrieving Results](#Retrieving Results)
    - [Retrieving All Entities](#Retrieving All Entities)
    - [Retrieving A Single Entity](#Retrieving A Single Entity)
    - [Retrieving A List Of Field Values](#Retrieving A List Of Field Values)
    - [Aggregates and Scalar Results](#Aggregates and Scalar Results)
- [Selects](#Selects)
- [Where Clauses](#Where Clauses)
    - [Simple Where Clauses](#Simple Where Clauses)
    - [Or Statements](#Or Statements)
    - [Additional Where Clauses](#Additional Where Clauses)
    - [Parameter Grouping](#Parameter Grouping)

## Introduction

Hydrogen provides a beautiful, convenient and simple implementation for 
working with Doctrine queries. It does not affect the existing code 
in any way and can be used even in pre-built production applications.

## Installation

### Server Requirements

The Hydrogen library has several system requirements. 
You need to make sure that your server meets the following requirements:

- PHP >= 7.1.3
- PDO PHP Extension
- Mbstring PHP Extension
- JSON PHP Extension
- [doctrine/orm >= 2.5](https://packagist.org/packages/doctrine/orm) 

### Installing Hydrogen

Hydrogen utilizes [Composer](https://getcomposer.org/) to manage its dependencies. 
So, before using Hydrogen, make sure you have Composer installed on your machine.

**Stable**

```bash
composer require rds/hydrogen
```

**Dev**

```bash
composer require rds/hydrogen dev-master@dev
```

## Usage

Hydrogen interacts with the repositories of the Doctrine. 
In order to take advantage of additional features - you need to 
add the main trait to an existing implementation of the repository.

```php
<?php

use Doctrine\ORM\EntityRepository;
use RDS\Hydrogen\Hydrogen;

class ExampleRepository extends EntityRepository 
{
    use Hydrogen;
}
```

After that you get full access to the query builder.

## Retrieving Results

### Retrieving All Entities

You may use the `->query()` method on the Repository to begin a query. 
This method returns a fluent query builder instance for the given repository, 
allowing you to chain more constraints onto the query and then finally 
get the results using the `->get()` method:

```php
<?php

use RDS\Hydrogen\Hydrogen;
use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository 
{
    use Hydrogen;
    
    public function toArray(): iterable
    {
        return $this->query->get();
    }
}
```

The `get()` method returns an `array` containing the results, 
where each result is an instance of the object (Entity) associated 
with the specified repository:

```php
foreach ($users->toArray() as $user) {
    \var_dump($user);
}
```

In addition, you can use the method `collect()` to 
get a collection that is compatible with ArrayCollection:

```php
<?php

use RDS\Hydrogen\Hydrogen;
use Doctrine\ORM\EntityRepository;
use RDS\Hydrogen\Collection\Collection;

class UsersRepository extends EntityRepository 
{
    use Hydrogen;
    
    public function toCollection(): Collection
    {
        return $this->query->collect();
    }
}
```

```php
$users->toCollection()->each(function (User $user): void {
    \var_dump($user);
});
```

**Note:** Direct access to the Hydrogen build, instead of the 
existing methods, which is provided by the Doctrine completely 
**ignores** all relations (like: `@OneToMany(..., fetch="EAGER")`).

### Retrieving A Single Entity

f you just need to retrieve a single row from the database table, 
you may use the first method. This method will return a single Entity object:

```php
$user = $repository->query->where('name', 'John')->first();

echo $user->getName();
```

If you don't even need an entire row, you may extract a single 
values from a record using additional arguments for `->first()` method. 
This method will return the value of the column directly:

```php
[$name, $email] = $repository->query->where('name', 'John')->first('name', 'email');

echo $name . ' with email ' . $email;
```

### Retrieving A List Of Field Values

If you would like to retrieve an array or Collection containing the values of a single Entity's field value, 
you may use the additional arguments for `->get()` or `->collect()` methods. 
In this example, we'll retrieve a Collection of user ids and names:

```php
$users = $repository->query->get('id', 'name');

foreach ($users as ['id' => $id, 'name' => $name]) {
    echo $id . ': ' . $name;
}
```

### Aggregates and Scalar Results

The query builder also provides a variety of aggregate methods such as `count`, `max`, `min`, 
`avg`, and `sum`. You may call any of these methods after constructing your query:

```php
$count = $users->query->count(); 

$price = $prices->query->max('price');
```

Of course, you may combine these methods with other clauses:

```php
$price = $prices->query
    ->where('user', $user)
    ->where('finalized', 1)
    ->avg('price');
```

In the event that your database supports any other functions, 
then you can use these methods directly using `->scalar()` method:

The first argument of the `->scalar()` method requires specifying the field that should be 
contained in the result. The second optional argument allows you 
to convert the type to the desired one.

```php
$price = $prices->query
    ->select('AVG(price) as price')
    ->scalar('price', 'int');
```

**Allowed Types**

| Type       | Description                      |
|------------|----------------------------------|
| `int`      | Returns an integer value         |
| `float`    | Returns a float value            |
| `string`   | Returns a string value           |
| `bool`     | Returns boolean value            |
| `callable` | Returns the Closure instance     |
| `object`   | Returns an object                |
| `array`    | Returns an array                 |
| `iterable` | `array` alias                    |

**Query Invocations**

| Method     | Description                              |
|------------|------------------------------------------|
| `get`      | Returns an array of entities             |
| `collect`  | Returns a Collection of entities         |
| `first`    | Returns the first result                 |
| `scalar`   | Returns the single scalar value          |
| `count`    | Returns count of given field             |
| `sum`      | Returns sum of given field               |
| `avg`      | Returns average of given field           |
| `max`      | Returns max value of given field         |
| `min`      | Returns min value of given field         |

## Selects

Using the `select()` method, you can specify a 
custom select clause for the query:

```php
[0 => $user, 'count' => $count] = $users->query
    ->select(['COUNT(id)' => 'count'])
    ->get();

echo $user->getName();
echo $count;
```

## Where Clauses

### Simple Where Clauses

You may use the where method on a query builder instance to add 
where clauses to the query. The most basic call to where requires 
three arguments. The first argument is the name of the column. 
The second argument is an operator, which can be any of the 
database's supported operators. Finally, the third argument is 
the value to evaluate against the column.

For example, here is a query that verifies the value of the 
"votes" Entity field is equal to 100:

```php
$users = $repository->query->where('votes', '=', 100)->get();
```

For convenience, if you want to verify that a column is equal 
to a given value, you may pass the value directly as the 
second argument to the where method:

```php
$users = $repository->query->where('votes', 100)->get();
```

Of course, you may use a variety of other operators when 
writing a where clause:

```php
$users = $repository->query
    ->where('votes', '>=', 100)
    ->get();
    
$users = $repository->query
    ->where('votes', '<>', 100)
    ->get();
    
$users = $repository->query
    ->where('votes', '<=', 100)
    ->get();
```

### Or Statements

You may chain where constraints together as well as add `or`
clauses to the query. The `orWhere` method accepts the same 
arguments as the where method:

```php
$users = $repository->query
    ->where('votes', '>', 100)
    ->orWhere('name', 'John')
    ->get();
```

Alternatively, you can use the `->or` magic method:

```php
$users = $repository->query
    ->where('votes', '>', 100)
    ->or->where('name', 'John')
    ->get();
```

### Additional Where Clauses

**between**

The `between` method verifies that a Entity fields's value is between two values:

```php
$users = $repository->query
    ->between('votes', 1, 100)
    ->get();

$users = $repository->query
    ->where('name', 'John')
    ->orBetween('votes', 1, 100)
    ->get();
```

**notBetween**

The `notBetween` method verifies that a Entity field's value lies outside of two values:

```php
$users = $repository->query
    ->notBetween('votes', 1, 100)
    ->get();

$users = $repository->query
    ->where('name', 'John')
    ->orNotBetween('votes', 1, 100)
    ->get();
```

**whereIn / whereNotIn**

The `whereIn` method verifies that a given Entity field's value 
is contained within the given array:

```php
$users = $repository->query
    ->whereIn('id', [1, 2, 3])
    ->get();

$users = $repository->query
    ->where('id', [1, 2, 3])
    // Equally: ->whereIn('id', [1, 2, 3])
    ->orWhere('id', [101, 102, 103])
    // Equally: ->orWhereIn('id', [101, 102, 103])
    ->get();
```

The `whereNotIn` method verifies that the given Entity field's value 
is not contained in the given array:

```php
$users = $repository->query
    ->whereNotIn('id', [1, 2, 3])
    ->get();

$users = $repository->query
    ->where('id', '<>', [1, 2, 3])
    // Equally: ->whereNotIn('id', [1, 2, 3])
    ->orWhere('id', '<>', [101, 102, 103])
    // Equally: ->orWhereNotIn('id', [101, 102, 103])
    ->get();
```

**whereNull / whereNotNull**

The `whereNull` method verifies that the value of 
the given Entity field is `NULL`:

```php
$users = $repository->query
    ->whereNull('updatedAt')
    ->get();

$users = $repository->query
    ->where('updatedAt', null)
    // Equally: ->whereNull('updatedAt')
    ->orWhereNull('deletedAt', null)
    // Equally: ->orWhereNull('deletedAt')
    ->get();
```

The `whereNotNull` method verifies that 
the Entity field's value is not `NULL`:

```php
$users = $repository->query
    ->whereNotNull('updatedAt')
    ->get();

$users = $repository->query
    ->whereNotNull('updatedAt')
    ->or->whereNotNull('deletedAt')
    ->get();
```

**like / notLike**

The `like` method verifies that the value of 
the given Entity field like given value:

```php
$messages = $repository->query
    ->like('description', '%some%')
    ->orLike('description', '%any%')
    ->get();

$messages = $repository->query
    ->where('description', '~', '%some%')
    ->orWhere('description', '~', '%any%')
    ->get();
```

The `notLike` method verifies that the value of 
the given Entity field is not like given value:

```php
$messages = $repository->query
    ->notLike('description', '%some%')
    ->orNotLike('description', '%any%')
    ->get();

$messages = $repository->query
    ->where('description', '!~', '%some%')
    ->orWhere('description', '!~', '%any%')
    ->get();
```

### Parameter Grouping

Sometimes you may need to create more advanced where 
clauses such as "where exists" clauses or nested parameter 
groupings. The Hydrogen query builder can handle these as well. 
To get started, let's look at an example of grouping 
constraints within parenthesis:

```php
$users = $repository->query
    ->where('name', 'John')
    ->where(function(Query $query) {
        $query->where('votes', '>', 100)
              ->orWhere('title', 'Admin');
    })
    ->get();
```

As you can see, passing a `Closure` into the `where` method 
instructs the query builder to begin a constraint group. 
The `Closure` will receive a query builder instance which 
you can use to set the constraints that should be contained 
within the parenthesis group. The example above will 
produce the following DQL:

```sql
SELECT u FROM App\Entity\User u 
WHERE u.name = "John" AND (
    u.votes > 100 OR
    u.title = "Admin" 
)
```

In addition to this, instead of the `where` or `orWhere` method, 
you can use another options. Methods `or` and `and` will do the same:

```php
$users = $repository->query
    ->where('name', 'John')
    ->and(function(Query $query) {
        $query->where('votes', '>', 100)
              ->orWhere('title', 'Admin');
    })
    ->get();
    
// SELECT u FROM App\Entity\User u 
// WHERE u.name = "John" AND (
//     u.votes > 100 OR
//     u.title = "Admin"
// )
    
$users = $repository->query
    ->where('name', 'John')
    ->or(function(Query $query) {
        $query->where('votes', '>', 100)
              ->where('title', 'Admin');
    })
    ->get();
    
// SELECT u FROM App\Entity\User u 
// WHERE u.name = "John" OR (
//     u.votes > 100 AND
//     u.title = "Admin"
// )
```


--------------------

Beethoven approves.

![https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png](https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png)
