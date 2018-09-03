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
- [Retrieving Results](#retrieving-results)
    - [Retrieving All Entities](#retrieving-all-entities)
    - [Retrieving A Single Entity](#retrieving-a-single-entity)
    - [Retrieving A List Of Field Values](#retrieving-a-list-of-field-values)
    - [Aggregates And Scalar Results](#aggregates-and-scalar-results)
- [Selects](#selects)
- [Where Clauses](#where-clauses)
    - [Simple Where Clauses](#simple-where-clauses)
    - [Or Statements](#or-statements)
    - [Additional Where Clauses](#additional-where-clauses)
    - [Parameter Grouping](#parameter-grouping)
- [Ordering](#ordering)
- [Grouping](#grouping)
- [Limit And Offset](#limit-and-offset)
- [Embeddables](#embeddables)
- [Relations](#relations)
    - [Greedy Loading](#greedy-loading)
    - [Nested Relationships](#nested-relationships)
    - [Relations Subqueries](#relations-subqueries)
- [Collections](#collections)
    - [Higher Order Messaging](#higher-order-messaging)
    - [Destructuring](#destructuring)

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
    ->where(function (Query $query): void {
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
    ->and(function (Query $query): void {
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
    ->or(function (Query $query): void {
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

## Ordering

**orderBy**

The `orderBy` method allows you to sort the result of the query 
by a given column. The first argument to the `orderBy` method 
should be the column you wish to sort by, while the second argument 
controls the direction of the sort and may be either asc or desc:

```php
$users = $repository->query
    ->orderBy('name', 'desc')
    ->get();
```

Also, you may use shortcuts `asc()` and `desc()` to simplify the code:

```php
$users = $repository->query
    ->asc('id', 'createdAt')
    ->desc('name')
    ->get();
```

**latest / oldest**

The latest and oldest methods allow you to easily order 
results by date. By default, result will be ordered by the 
`createdAt` Entity field. Or, you may pass the column name 
that you wish to sort by:

```php
$users = $repository->query
     ->latest()
     ->get();
     
$posts = $repository->query
    ->oldest('updatedAt')
    ->get();
```

## Grouping

**groupBy**

The `groupBy` method may be used to group the query results:

```php
$users = $repository->query
     ->groupBy('account')
     ->get();
```

You may pass multiple arguments to the `groupBy` method to group by 
multiple columns:

```php
$users = $repository->query
     ->groupBy('firstName', 'status')
     ->get();
```

**having**

The `having` method's signature is similar to that 
of the `where` method:

```php
$users = $repository->query
    ->groupBy('account')
    ->having('account.id', '>', 100)
    ->get();
```

## Limit And Offset

**skip / take**

To limit the number of results returned from the query, or 
to skip a given number of results in the query, you may 
use the `skip()` and `take()` methods:

```php
$users = $repository->query->skip(10)->take(5)->get();
```

Alternatively, you may use the `limit` and `offset` methods:

```php
$users = $repository->query
    ->offset(10)
    ->limit(5)
    ->get();
```

**before / after**

Usually during a heavy load on the DB, the `offset` can shift while 
inserting new records into the table. In this case it is worth using 
the methods of `before()` and `after()` to ensure that the subsequent 
sample will be strictly following the previous one.

Let's give an example of obtaining 10 articles,
which are located after the id 15:

```php
$articles = $repository->query
    ->where('category', 'news')
    ->after('id', 15)
    ->take(10)
    ->get();
```

**range**

You may use the `range()` method to specify exactly which 
record you want to receive as a result:

```php
$articles = $repository->range(10, 20)->get();
```

## Embeddables

Embeddables are classes which are not entities themselves, but are 
embedded in entities and can also be queried by Hydrogen. 
You'll mostly want to use them to reduce duplication or separating concerns. 
Value objects such as date range or address are the primary use 
case for this feature.

```php
<?php

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class User
{
    /**
     * @ORM\Embedded(class=Address::class) 
     */
    private $address;
}

/**
 * @ORM\Embeddable()
 */
class Address
{
    /**
     * @ORM\Column(type="string") 
     */
    private $city;

    /** 
     * @ORM\Column(type="string") 
     */
    private $country;
}
```

To manage Embeddables through queries, you can use the point (`.`) operator:

```php
<?php

class UsersRepository extends EntityRepository
{
    use Hydrogen;
    
    public function findAllOrderedByCountry(): iterable
    {
        return $this->query->asc('address.country')->get();
    }
}
```

## Relations

The Doctrine ORM provides several types of different relations: `@OneToOne`, 
`@OneToMany`, `@ManyToOne` and `@ManyToMany`. And "greed" for loading these 
relations is set at the metadata level of the entities. The Doctrine 
does not provide the ability to manage relations and load them 
during querying, so when you retrieve the data, you can encounter 
`N+1` queries without the use of DQL, especially 
on `@OneToOne` relations, where there is simply no other loading option.

The Hydrogen allows you to flexibly manage how to obtain relations at 
the query level, as well as their number and additional aggregate functions 
applicable to these relationships:

```php
<?php

/** 
 * @ORM\Entity() 
 */
class Customer
{
    /**
     * @ORM\OneToOne(targetEntity=Cart::class, mappedBy="customer")
     */
    private $cart;
}

/** 
 * @ORM\Entity() 
 */
class Cart
{
    /**
     * @ORM\OneToOne(targetEntity=Customer::class, inversedBy="cart")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;
}
```

### Greedy Loading

If you create a basic query to the repository, in this case you will 
get the same `N+1`, where for each element the blocks will be 
generated with one additional query, for each related entity.

In order to avoid this, use the `with()` method to indicate all 
the links that must be greedily loaded:

```php
<?php

class CustomerRepository extends EntityRepository
{
    use Hydrogen;

    public function findAllWithCarts(): iterable
    {
        return $this->query->with('cart')->get();
    }
}
```

As a result, we obtain the following result:

```php
$repository->findAll();

// 1: SELECT с, c.customer_id FROM Customer c
// 2: SELECT c FROM Cart c WHERE c.id = ?
// 3: SELECT c FROM Cart c WHERE c.id = ?
// ...: SELECT c FROM Cart c WHERE c.id = ?
```

```php
$repository->findAllWithCarts();

// 1: SELECT с1, c2 FROM Customer c1 LEFT JOIN Cart c2 ON c1.customer_id = c2.id
```

### Nested Relationships

The sampling method described above affects only the direct relationship 
indicated in `->with()` method. In order to "greedly" loading of the entire
chain of relationships you may use the point (`.`) operator:

```php
$result = $users->query
    ->with('messages', 'posts.author')
    ->get();
```

### Relations Subqueries

Sometimes you need to carefully select only those elements of the 
relationships that are required or sort them in the desired order. 
In this case, you will be assisted by internal subqueries for relationships, 
which are indicated in the form of an additional callback:

```php
$users = $repository->query
    ->with(['messages' => function (Query $query): void {
        $query->notNull('deletedAt')->asc('createdAt');
    }])
    ->get();
```

## Collections

As the base kernel used a [Illuminate Collections](https://laravel.com/docs/5.5/collections) but 
some new features have been added:

- Add HOM proxy autocomplete.
- Added support for global function calls using the [Higher Order Messaging](https://en.wikipedia.org/wiki/Higher_order_message)
 and the [Pattern Matching](https://en.wikipedia.org/wiki/Pattern_matching).
 
### Higher Order Messaging

Pattern "`_`" is used to specify the location of the delegate in
the function arguments in the higher-order messaging while using global functions.

```php
use RDS\Hydrogen\Collection\Collection;

$data = [
    ['value' => '23'],
    ['value' => '42'],
    ['value' => 'Hello!'],
];


$example1 = Collection::make($data)
    ->map->value // ['23', '42', 'Hello!']
    ->toArray();
    
//
// $example1 = \array_map(function (array $item): string {
//      return $item['value']; 
// }, $data);
//

$example2 = Collection::make($data)
    ->map->value     // ['23', '42', 'Hello!']
    ->map->intval(_) // [23, 42, 0]
    ->filter()       // [23, 42]
    ->toArray();
    
//
//
// $example2 = \array_map(function (array $item): string {
//      return $item['value']; 
// }, $data);
//
// $example2 = \array_map(function (string $value): int {
//      return \intval($value);
//                      ^^^^^ - pattern "_" will replaced to each delegated item value. 
// }, $example1);
//
// $example2 = \array_filter($example2, function(int $value): bool {
//      return (bool)$value;
// });
//
//

$example3 = Collection::make($data)
    ->map->value            // ['23', '42', 'Hello!']
    ->map->mbSubstr(_, 1)   // Using "mb_substr(_, 1)" -> ['3', '2', 'ello!']
    ->toArray();
```

### Destructuring

```php
use RDS\Hydrogen\Collection\Collection;

$collection = Collection::make([
    ['a' => 'A1', 'b' => 'B1' 'value' => '23'],
    ['a' => 'A2', 'b' => 'B2' 'value' => '42'],
    ['a' => 'A3', 'b' => 'B3' 'value' => 'Hello!'],
]);

// Displays all data
foreach($collection as $item) {
    \var_dump($item); // [a => 'A*', b => 'B*', value => '***'] 
}

// Displays only "a" field
foreach ($collection as ['a' => $a]) {
    \var_dump($a); // 'A'
}
```

--------------------

Beethoven approves.

![https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png](https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png)
