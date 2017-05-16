# Slumber

# depends on
 
"doctrine/common": "~2.5"  
"symfony/finder": "~2.5"  
"gossi/php-code-generator":"dev-master"  

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
