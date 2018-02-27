## Laravel Page Cache

This package uses middleware to wrap around all requests and store the response HTML. If the request has been executed before it'll return the already processed HTML ready without having to execute any further methods, such as controllers, models, view rendering, database queries etc...

### Installation

You will need composer to install this package (get composer). Then run:

```bash
composer require jsefton/laravel-page-cache
```

#### Register Service Provider

Add the below into your `config/app.php` within `providers` array

```
JSefton\PageCache\PageCacheServiceProvider::class
```

After installation you will need to publish the config file and a storage folder for the render cache. To do this run:

```bash
php artisan vendor:publish --tag=pagecache
```

This will create the file `config/pagecache.php` where you can configure the settings around page cache.


### Usage

#### Clearing Cache

You can run an artisan command to clear all cache storage with:

```bash
php artisan pagecache:clear
```
