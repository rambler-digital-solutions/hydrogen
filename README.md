<p align="center">
    <img src="./resources/logo.png" alt="Hydrogen" />
</p>

<p align="center">
    <a href="https://packagist.org/packages/rds/hydrogen"><img src="https://poser.pugx.org/rds/hydrogen/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/rds/hydrogen"><img src="https://poser.pugx.org/rds/hydrogen/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/rambler-digital-solutions/hydrogen/master/LICENSE"><img src="https://poser.pugx.org/rds/hydrogen/license" alt="License MIT"></a>
</p>

## Introduction

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Queries](#queries)
    - [Non-prefixed queries](#non-prefixed-queries)
    - [Eager loading](#eager-loading)
    - [Relation selection](#relation-selection)
- [Repositories](#repositories)
    - Origins
        - [DatabaseRepository](#databaserepository)
        - TODO: ~~MemoryRepository~~
        - TODO: ~~JsonFileRepository~~
        - TODO: ~~PhpFileRepository~~
    - [Selections](#selections)
    - ["In-place" queries](#in-place-queries)
    - [Scopes](#scopes)
- [Collections](#collections)
    - [Higher Order Messaging](#higher-order-messaging)
    - [Static constructors](#static-constructors)
    - [Destructuring](#destructuring)
    
This package contains a set of frequently used functions of Doctrine ORM 
that are optimized for more convenient usage.

## Requirements

- `PHP >= 7.1`

## Installation

- `composer require rds/hydrogen`

## Queries

The Query object is created in the format `Query::method()->method()->method()->...` 
and has a set of the following methods. 

```php
Query::new()
    //
    // Selections
    //
    ->select('some', 'any')             // SELECT [entity], [relations], some, any
    ->select(['some' => 'alias'])       // SELECT [entity], [relations], some AS alias
    ->select(['count(id)' => 'cnt'])    // SELECT [entity], [relations], COUNT(id) AS cnt
    ->where('field', 23)                // WHERE field = 23
    ->where('field', '>', 42)           // WHERE field > 42
    ->where('field', [1, 2, 3])         // WHERE field IN (1, 2, 3)
    ->orWhere(function(Query $query) {           // 
        $query->where('a', 23)->where('b', 42);  // WHERE XXX OR (a = 23 AND b = 42)
    })                                           // 
    ->whereIn('field', [1, 2, 3])       // WHERE field IN (1, 2, 3)
    ->whereNotIn('field', [1, 2, 3])    // WHERE field NOT IN (1, 2, 3)
    ->whereBetween('field', 1, 2)       // WHERE field BETWEEN 1 AND 2
    ->whereNotBetween('field', 1, 2)    // WHERE field NOT BETWEEN 1 AND 2
    ->whereNull('field')                // WHERE field IS NULL
    ->whereNotNull('field')             // WHERE field IS NOT NULL
    ->orderBy('field')                  // ORDER BY field ASC
    ->orderBy('field', false)           // ORDER BY field DESC
    ->asc('field')                      // ORDER BY field ASC
    ->desc('field')                     // ORDER BY field DESC
    ->latest('updatedAt')               // ORDER BY updatedAt DESC
    ->latest()                          // ORDER BY createdAt DESC
    ->oldest('updatedAt')               // ORDER BY updatedAt ASC
    ->oldest()                          // ORDER BY createdAt ASC
    ->groupBy('field', 'field2')        // GROUP BY field, field2
    ->limit(10)                         // LIMIT 10
    ->take(10)                          // LIMIT 10
    ->skip(10)                          // OFFSET 10
    ->offset(10)                        // OFFSET 10
    ->range(100, 150)                   // LIMIT 50 OFFSET 100
    ->after('field', 150)               // WHERE field > 150 ORDER BY field ASC
    ->before('field', 150)              // WHERE field < 150 ORDER BY field DESC
    ->with('friends')                   // Eager loading of "friends"
    ->with('friends.messages')          // Eager loading of "friends" and "messages" of "friends".
    ->with([                            // Eager loading of "friends" and "messages" of "friends" WHERE message.sent = true
        'friends.messages' => function(Query $q) {
            $q->where('sent', true);
        }
    ]);
    
    //
    // Execution
    //
    ->get()                             // Find all as array
    ->collect()                         // Find all as Collection
    ->first()                           // Select first item
    ->count()                           // Select count of items
    
    //
    // Configure
    //
    ->from($repository)                 // Reference to the repository to which this Query belongs
    ->scope($anyRepository)             // Add repository scope
```

Another example:

```php
Query::where('id', 23)
    ->or->where('id', '>', 42)
    ->or->where('id', [1, 3, 5])
    ->asc('createdAt', 'updatedAt');
    
/**
 * Result:
 *
 * SELECT entity FROM ... 
 * WHERE entity.id = 23 OR 
 *       entity.id > 42 OR 
 *       entity.id IN (1, 3, 5) 
 * ORDER BY 
 *      entity.created_at ASC, 
 *      entity.updated_at ASC;
 */
```

### Non-prefixed queries

In some cases, you need to get a custom value that does not apply to the 
entity itself. In this case, you should use the prefix `this.` in the queries.

```php
Query::select('RAND() as HIDDEN rnd')
    ->orderBy('rnd', 'createdAt')
    ->get();
/**
 * SELECT entity, RAND() as HIDDEN rnd FROM ... ORDER BY entity.created_at ASC, rnd ASC
 */
```

### Eager loading

Suppose we have the following OneToOne relationship between the parent and child.

```php
/** @ORM\Entity */
class Child
{
    /**
     * @var Parent
     * @ORM\OneToOne(targetEntity=Parent::class, inversedBy="child")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
}

/** @ORM\Entity */
class Parent
{
    /**
     * @var Child
     * @ORM\OneToOne(targetEntity=Child::class, mappedBy="parent")
     */
    private $child;
}
```

Regardless of how you indicate your relationship, it will hit `N+1`, like this:

```php
$query = Query::whereIn('id', [1, 2]);

$children->findAll($query);

/**
 * SELECT ... FROM children d0 WHERE d0.id IN ("1", "2");
 * SELECT ... FROM parents t0 LEFT JOIN children t3 ON t3.parent_id = t0.id WHERE t0.id = "1";
 * SELECT ... FROM parents t0 LEFT JOIN children t3 ON t3.parent_id = t0.id WHERE t0.id = "2";
 */
```

Now let's try to force this relationship and see what happens:

```php
$query = Query::whereIn('id', [1, 2])
    ->with('parent'); // Just add "->with(relationName)" method.

$children->findAll($query);

/**
 * SELECT c, p
 * FROM children c
 * LEFT JOIN parents p ON c.parent_id = p.id
 * WHERE c.id IN ("1", "2");
 */
```

### Relation selection

You can add sample criteria for relationships using the second argument of the `with` method.

```php
$query->where('id', [1, 2])->with(['parent' => function(Query $sub) {
    $sub->where('id', [33, 42]);
}]);

/**
 * SELECT child, parent
 * FROM children child
 * LEFT JOIN parents parent ON child.parent_id = parent.id
 * WHERE
 *    child.id IN ("1", "2") AND
 *    parent.id IN ("33", "42");
 */
```

## Repositories

The interface signature has been improved and now contains the following methods.

```php
use RDS\Hydrogen\Collection;
use RDS\Hydrogen\Query;

interface ObjectRepository
{
    public function find($id): ?object;
    public function findAll(): iterable;
    public function findOneBy(Query $query): ?object;
    public function findBy(Query $query): iterable;
    public function count(Query $query): int;
}
```

In addition, basic repositories for different types 
of data sources have been added.

### DatabaseRepository

```php
use RDS\Hydrogen\DatabaseRepository;

class Example extends DatabaseRepository {}
```

### Selections

```php
use RDS\Hydrogen\Query;

$query = Query::where('id', '>=', 42)->orderBy('id');

$result = $repository->findAll($query);

\var_dump($result->toArray());
```

### "In-place" queries

You can make queries on the spot using these repositories as a data source.

```php
$repository = $em->getRepository(EntityClass::class);

Query::from($repository)
    ->where('id', 23)
    ->collect(); // Collection { EntityClass, EntityClass }
```

### Scopes

Also you can create "parts" of the query and separate them into other methods or classes.
Each method should be referred to as METHOD_NAME. When you call a `query()` inside 
the repository, it is already the source of scopes.

```php
class FriendsRepository extends DatabaseRepository
{
    public function findByUser(User $user): Collection
    {
        return $this->query
            ->of($user)      // Call "$this->of($user)->"
            ->collect();
    }
    
    /**
     * This is an example scope method 
     */
    protected function of(User $user): Query
    {
        return $this->query->where('user', $user);
    }
}
```

Also you can add external query scopes as follows.

```php
$query = Query::new()->scope(new UsersRepository(), new FriendsRepository() [, $scope])
    ->callSomeMethodFromRepo()
    ->where('some', 23);
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
use RDS\Hydrogen\Collection;

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
use RDS\Hydrogen\Collection;

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
