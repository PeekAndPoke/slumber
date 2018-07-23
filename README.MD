[![Code Coverage](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/badges/build.png?b=master)](https://scrutinizer-ci.com/g/PeekAndPoke/slumber/build-status/master)


# Slumber

What is Slumber? It is a tool for mapping data from objects to array and vice versa. 
E.g. it gives you the possibility to easily map your domain model to JSON. Or map from JSON to your
Domain Model classes. 

Slumber uses Doctine Annotations. The annotations are used to mark which properties of classes ar to 
be mapped and in which way.

(PHP5.6 compatibility until v0.4.x)


# Mapping 

## Basic example

The "trademark" of the slumber annotated classes are these annotations: 

```php

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

class MyClass {

    /**
     * @Slumber\AsString()
     */
    private $name;

    /**
     * @Slumber\AsInteger()
     */
    private $age;

    /**
     * @Slumber\AsDecimal()
     */
    private $height;

    /**
     * @Slumber\AsObject(Address::class)
     */
    private $address;
}
```

## Mapping annotations

You will find the implementation of all mappers [here](./src/Slumber/Annotation/Slumber)

### Slumber\AsBool()

Maps values from and to booleans.

```php
class C {
  /**
   * @Slumber\AsBool()
   */
  private $val = true;
}
```
maps to
```json
{
  "val": true
}
```

```php
class C {
  /**
   * @Slumber\AsBool()
   */
  private $val = 0;
}
```
maps to
```json
{
  "val": false
}
```

The same behaviour applys for the opposite direction.
For more details on how the mapper works have a look [here](./src/Slumber/Core/Codec/Property/BoolMapper.php)


### Slumber\AsDecimal()

Maps values from and to floating point values.

```php
class C {
  /**
   * @Slumber\AsDecimal()
   */
  private $val = 1.23;
}
```
maps to
```json
{
  "val": 1.23
}
```

```php
class C {
  /**
   * @Slumber\AsDecimal()
   */
  private $val = "abc";
}
```
maps to
```json
{
  "val": 0
}
```

The same behaviour applys for the opposite direction.
For more details on how the mapper works have a look [here](./src/Slumber/Core/Codec/Property/DecimalMapper.php)


### Slumber\AsEnum()

Maps values from and to enum values.

For this to work we need an Enum class:

```php
class Enum extends Enumeratd {

    /** @var Enum */
    public static $ONE;
    /** @var Enum */
    public static $TWO;
}

Enum::init();
```

```php```
class C {
  /**
   * @Slumber\AsEnum(Enum::class)
   */
  private $val = Enum::$ONE;
}
```
maps to
```json
{
  "val": "ONE"
}
```

```php
class C {
  /**
   * @Slumber\AsEnum(Enum::class)
   */
  private $val = "abc";
}
```
maps to
```json
{
  "val": null
}
```

The same behaviour applys for the opposite direction.
For more details on how the mapper works have a look [here](./src/Slumber/Core/Codec/Property/EnumMapper.php)


### Slumber\AsInteger()

Maps values from and to floating point values.

```php
class C {
  /**
   * @Slumber\AsInteger()
   */
  private $val = 1.23;
}
```
maps to
```json
{
  "val": 1
}
```

```php
class C {
  /**
   * @Slumber\AsDecimal()
   */
  private $val = "abc";
}
```
maps to
```json
{
  "val": 0
}
```

The same behaviour applys for the opposite direction.
For more details on how the mapper works have a look [here](./src/Slumber/Core/Codec/Property/IntegerMapper.php)


## Polymorphism example

When serializing the polymorphism information is not needed. Since we know exactly which class
we have in our hand to serialize.

But while de-serialization Polymorphism needs to be annotated explicitly. Only by doing this
we can know which class to instantiate. We also need a field on the data, which acts as the 
discriminator.


```php

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/*
 * @Slumber\Polymorphic(
 *     {
 *         "adyen"  : Adyen::class,
 *         "paypal" : Paypal::class,
 *         "stripe" : Stripe::class,
 *     },
 *     tellBy  = "provider",
 *     default = PaymentMean::class
 * )
 */
class PaymentMean {

  /**
   * The discriminator field
   *
   * @Slumber\AsString()
   */
  private $provider;

  /* ... */
}

class Adyen extends PaymentMean {

  /* ... */
}

class Paypal extends PaymentMean {

  /* ... */
}

class Stripe extends PaymentMean {

  /* ... */
}

```






# TICKETS:

next ticket number: 14

## PRIO I

SLUMBER-13 - 0%  
    ( ) unit test for polymorphics that fall back to the default  
       ( ) a) discriminator no set  
       ( ) b) unknown discriminator set  

SLUMBER-12 - 50% - Implement aliases class for repositories
    -> in order to store multiple type in one collection we must be able to specify alias classes for repositories
    (x) reported 2016-11-15
    (x) implemented 2016-11-15
    ( ) unit-tests
    
SLUMBER-4 - 0% - Refactor the hardcoded visitors in the MongoDbCodec set to come from the MongoDbEntityConfig
    -> the config reader must automatically add the visitors (can user override these somehow?)
    -> it is more generic and will work fine in conjunction with user-attached listeners  
    (x) reported 2016-05-01
    
SLUMBER-7 - 0% -    
    
## PRIO II

SLUMBER-3 - 0% - Let visitors like onPreCreate() pass an event class instead of multiple parameters
    -> better extensibility
    -> event propagation could be stopped
    (x) reported 2016-05-01
  
SLUMBER-2 - 0% - setting and reading of properties through getters and setters first. If not possible use reflection  
    -> reading and writing of inherited private properties will be possible  
    -> +33% speed  
    (x) reported 2016-05-01

SLUMBER-8 - 0% - implement GeoJSON support for Data\MongoDB   
    -> implement GeoSpatialIndex for GeoJson types
    -> implement GeoJsonPolygon, GeoJsonMultiPolygon and other GeoJson types
    (x) reported 2016-05-12

SLUMBER-7 - IRepository::save should return a more specific result than @return array|null
    (x) reported 2016-05-12

## PRIO III

SLUMBER-6 - 0% - Make Slumber\Swagger an own package and base it on a generic code generation component
    -> this is a project of its own 
    -> get rid of gossi/php-code-gen
    (x) reported 2016-05-01


## Completed

SLUMBER-1 - 100% - Remove @AsOne2One annotation and do similar as with @AsId
  -> currently entities marked like this cannot be used with the ArrayCodec (which is broken behaviour)
  (x) completed 2016-05-11      
     
SLUMBER-5 - 100% - move Slumber\MongoDb to Slumber\Data\MongoDb
  -> Slumber\Data will be the home of all database things
  (x) completed 2016-05-11  
     
SLUMBER-9 - 100% - implement polymorphic slumbering and awaking
  (x) reported 2016-05-14
  (x) completed 2016-10-01
  (x) unit-tests
       
SLUMBER-10 - 100% implement usage of collection classes for all AsCollection mappings
  -> This will make it possible to wrap incoming arrays into Collection classes.
  -> This will increase convenience for operations on array since the code will be encapsulated alongside the data
  -> Example: a TagsSet collection type which contains tags, could have methods like has(), addUnique(), remove()
  (x) reported 2016-11-01
  (x) completed 2016-11-13
  (x) unit-tests
     
SLUMBER-11 - 100% implement LazyDbRefCollection for storing lists of referenced objects
  (x) reported 2016-11-13
  (x) completed 2016-11-13
  (x) unit-tests
