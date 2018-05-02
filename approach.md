# Development approaches

Developping an ORM isn't easy, much choices need to be made. In order to create a well thought through ORM I started to document some of the possible approaches, their Pro's Con's and some code examples. The goal here is not to create an ORM with a static hell, everything being a service, creating proxy classes for every Entity or having to write a shit load of bootstrap code. Instead we want an efficient approach with low coupling and a nice readable syntax.

## Final approach

Both the Entity approach and the Entity Manager approach have their pros and cons. So we'we chosen to use an approach based on both of them. We'll create a EntityTrait which will have a static Entity Manager. It will also have some functions for retrieving and storing them. The Entity Manager will be the hard of the ORM as it will supply the Entity with the database connection and it's repository class/object.

## Preferred approach

The Entity Manager approach allows an Entity to be 100% decoupled from the database which allows you to use it for many other things. The big drawback of this approach is that you'll need the Entity manager almost everywhere.

## Entity model approach

An entity represents a database table and is aware of a database connection. Through a trait we can give the Entity knowledge of the relations it might have. We have 2 approaches to make the Entity aware of the DB connection.

1. Static connection property
We can give the entity a static property and set at kernel boot the connection on the entity. The benefit of this approach is that an Entity always can query the database and we don't need the DB connection everywhere. But it uses a static property.

2. Constructor injection
We can supply the connection using constructor injection. This approach requires you to have the DB connection available everywhere you instantiate an new Entity or query for one. But this still alows you to store an Entity instance or get it's relations without needing the DB connection.

```php
Entity::setConnection($connection);

$user = User::findById(1);
$user->name = "John Doe";
$user->store();

$user = User::find($connection)->byId(1);
```

## Entity Manager approach

The Entity Manager is a service which manages all entities. It can create a `Finder/Repository` services for an Entity. It allows the Entity itself to be completly independant from the database or the connection. But this approach will require you to have the EM almost everywhere in your code available.

### Pro's

- No use of static's
- Entities don't have knowledge of the database nor the connection.
- Multiple connections possible using services

### Con's

- You will need the EM almost everywhere
- An Entity with relations has to be a Service or it will require the EM to be available when accessing a relation

```php
$em = new Manager($connection);
$user = $em->find(User::class)->byId(1);
$user->name = "John Doe";
$em->store($user);
```

# Code scratches

```php
abstract class Entity {

  protected static $connection;
  
  public function setConnection(Connection $connection)
  {
    self::$connection = $connection;
  }

  public static function findById()
  {
    
  }
  
}

// Entity approach
class User extends Entity {

  use RelationsTrait;

  /**
   * @var string
   */
  public $name;
  
  public function group()
  {
    return $this->belongsTo(Group::class);
  }
}

$groupRelation = $user->group();

// EM approach 1
class User {
  /**
   * @var string
   */
  public $name;
  
  // Approach 1
  public function group(Manager $em)
  {
    return $em->relation($this)->belongsTo(Group::class);
  }
  
  // Approch 2
  public function group()
  {
    return $this->belongsTo(Group::class);
  }
}

// Approach 1
$groupRelation = $user->group($em);

// Approach 2
$groupRelation = $em->relation($user->group());
```
