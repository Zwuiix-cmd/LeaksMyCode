# libgamespyquery
A query virion and library for Pocketmine-MP\
This virion/library uses GS4 to query servers which provides more info. Servers that don't have GS4 supported/enabled can't be queried using this virion/library, so you need to use another virion such as [libpmquery](https://github.com/jasonwynn10/libpmquery)

## Installation
Add this to your `.poggit.yml` to add the virion into your plugin
```yml
libs:
  - src: KygekDev/libgamespyquery/libgamespyquery
    branch: main
    version: ^2.0
```
Or if you want to use as a Composer library, open a command line and run this
```
composer require mmm545/libgamespyquery
```
More information about this Composer library can be found in [Packagist](https://packagist.org/packages/mmm545/libgamespyquery)

## Usage
First you create a new `GameSpyQuery` instance, first argument is the IP address to query, second argument is the port to query
```php
$query = new GameSpyQuery("someserver.org", 19132);
```
Then we query the server
```php
$query->query();
```
You can also set a timeout in seconds (optional), the default timeout is 2 seconds
```php
$query->query(5);
```
The `query()` function will throw a `GameSpyQueryException` if the destination IP and port can't be queried, so you need to surround it with a try-catch block\
If everything worked correctly, you can use the `get()` function to get some info about the server\
List of the data you can get:
```php
$query->get("hostname"); // Server MOTD
$query->get("gametype"); // Game type, not sure what that means
$query->get("game_id"); // I think that's the game edition
$query->get("version"); // Version of minecraft the server is running on
$query->get("server_engine"); // Server software being used
$query->get("plugins"); // Plugins list with their version
$query->get("map"); // Current world
$query->get("numplayers"); // Number of online players
$query->get("maxplayers"); // Max number of players
$query->get("whitelist"); // On if whitelist is turned on, otherwise off
$query->get("hostip"); // Host ip
$query->get("hostport"); // Host port
$query->get("players"); // List of online players names
```
In case you want to get the raw response, you can use `getStatusRaw()`
