---
id: cache
title: Using the LanSuite cache
---

== Introduction

In order to avoid unnecessary calculations of unchanged data or retrieval from slow sources LanSuite now uses an internal caching infrastructure.
When you are implementing new features it should be considered if content needs to be created on every call or if content can be cached and reused.
Also for the usage this has some implications as the content on some pages may not represent the latest state, but the cached content.

## Usage

### Architecture

'''Think where data needs to be processed on call and where it requires processing on change.'''
E.g. A user is set to "paid" for the next party. This means that the amount of guests increases by one.
Either the total number of guests is recalculated on every page display again, or this is updated once the amount changes by user payment.
The more efficient variant is the later one, as the case appears far less than just page displays.
Also there is no requirement that the value is always correct, as it is just used for display.

### Code

There is object named `$cache` available in the global scope.
This implements [PSR-16](https://www.php-fig.org/psr/psr-16/) and either works via APCu (if module enabled) or with files in the temporary directory.
Code example:

```
// Import global object
global $cache;

// Check for existence of item in cache
if ($cache->has('module.entry.id') {
   $CachedData = $cache->get('module.entry.id');
   } else if {
   // Not in cache, thus generate content
   $CachedData = expensiveFunction();
   // And store it in cache so that the next iteration uses cache data
   $cache->write('module.entry.id', $CachedData, <$customTTL>);
   }
   //Run processing of data in $CachedData
   ...
```

### Naming convention

Cache entries should be named based on the following schema:
`<module>.<entry>.<identificator>`
e.g. `discord.cache`, `forum.thread.32121` or `translation.de.board`.
Further levels below this are possible and left to the discretion of the developer.

## Pitfalls

### Race conditions

As the cache provides a single instance across parallel executions, it can happen that multiple threads access an entry at the same or close to the same time.
This could cause a cache entry to be updated multiple times, because an update already occured between `$cache->has()` and `$cache->write()`
This can be a problem on high-load servers on restarts or cache misses, because this could cause a massive ammount of recalculations.
This would need a better implementation that includes cache mutexes.

### Cache Item Timeout

Entries have a default Time-To-Live of ten minutes and disappear after that.
A cache entry could disappear between the check in `$cache->has()` and retrieval if the TTL is breached between these two calls.
This can be influenced by defining a custom TTL when writing the item back.

### Cached display

Please remember that while developing your software with cache usage you may see stale data when displaying a page.
It would be recommended to either:
* reduce the TTL to one second
* overwrite $cache with an instance of `NullCache` as that practically disables the cache.
* include a call to `$cache->clear()` to ensure that no old entries are in the cache