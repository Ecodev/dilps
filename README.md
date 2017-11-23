# Dilps

Master:
[![Build Status](https://travis-ci.org/Ecodev/dilps.svg?branch=master)](https://travis-ci.org/Ecodev/dilps) &nbsp;
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Ecodev/dilps/badges/quality-score.png?b=master&s=2b6588a62b5d35d80bd104014502605b7520f49a)](https://scrutinizer-ci.com/g/Ecodev/dilps/?branch=master) &nbsp;
[![Code Coverage](https://scrutinizer-ci.com/g/Ecodev/dilps/badges/coverage.png?b=master&s=cc2eec510484f44409973822e7e3a805df6a1e91)](https://scrutinizer-ci.com/g/Ecodev/dilps/?branch=master)

Develop:
[![Build Status](https://travis-ci.org/Ecodev/dilps.svg?branch=develop)](https://travis-ci.org/Ecodev/dilps) &nbsp;
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Ecodev/dilps/badges/quality-score.png?b=develop&s=2b6588a62b5d35d80bd104014502605b7520f49a)](https://scrutinizer-ci.com/g/Ecodev/dilps/?branch=develop) &nbsp;
[![Code Coverage](https://scrutinizer-ci.com/g/Ecodev/dilps/badges/coverage.png?b=develop&s=cc2eec510484f44409973822e7e3a805df6a1e91)](https://scrutinizer-ci.com/g/Ecodev/dilps/?branch=develop)

## Introduction

Dilps is a web application based on GraphQL for the API and Angular for the client.


## Installation

The recommended way to get a working copy is the following:

1. Set up a nginx virtual host to point to `htdocs/` directory and to include `configuration/nginx.conf`
2. Create a database in MariaDB named "dilps"
3. Configure database in `config/autoload/local.php` (see example ``config/autoload/local.php.dist``)
4. Finally, build the app:
```sh
./bin/build.sh
```

## Development

### Server

To switch the API to development (to enable logging), run:

```sh
composer development-enable
```

Logs will be available in ``logs/all.log``.

#### Configuration caching

By default, the skeleton will create a configuration cache in
`data/config-cache.php`. When in development mode, the configuration cache is
disabled, and switching in and out of development mode will remove the
configuration cache.

You may need to clear the configuration cache in production when deploying if
you deploy to the same directory. You may do so using the following:

```bash
$ composer clear-config-cache
```

You may also change the location of the configuration cache itself by editing
the `config/config.php` file and changing the `config_cache_path` entry of the
local `$cacheConfig` variable.

### Client

Run `yarn dev` for a dev server. Navigate to `http://localhost:4200/`. The app will
automatically reload if you change any of the source files.

## Testing

### PHPUnit

PHPUnit tests require a reference database dump. When the dump is loaded it **will destroy**
existing database. This must be done once before running tests. Then each test is ran
within a transaction which is rolled back, so the database state is always predictable.

To run PHPunit test:

```sh
./bin/load-test-data.php
./vendor/bin/phpunit # as many times as necessary
```

### Karma

Run `ng test` to execute the unit tests via [Karma](https://karma-runner.github.io).

### Protractor

Run `ng e2e` to execute the end-to-end tests via [Protractor](http://www.protractortest.org/).

