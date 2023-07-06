# Object hydrator

Lightweight, zero-dependency PHP library that helps you hydrate your DTOs from raw data.

## Installation
```bash
composer require recoded-dev/object-hydrator
```

## Usage

### Hydration
Consider the following class:

```php
<?php

namespace Acme;

use Recoded\ObjectHydrator\Attributes\SnakeCase;

#[SnakeCase]
readonly class User
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {
    }
}
```

This class could be hydrated like the following:
```php
use Acme\User;
use Recoded\ObjectHydrator\Hydration\ReflectionHydrator;

$dto = (new ReflectionHydrator())->hydrate([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john-doe@example.org',
], User::class);
```

### Custom data mapping
You can create a new attribute that implements
the`Recoded\ObjectHydrator\Contracts\Mapping\DataMapper` interface
this will make sure it gets picked up by the package and used for mapping.

In the `map` method of the interface you will receive the current value as
a first parameter. What you return will become the new value and other
mapping attributes after yours will receive that value as their first parameter.

For a simple example look at the
[EmptyStringToNull](https://github.com/recoded-dev/object-hydrator/blob/main/src/Attributes/EmptyStringToNull.php)
attribute.

### Optimization (dumping and caching)

In order to get the maximum performance out of this package
we have to get rid of the reflection every hydration.
Dumping makes this possible. It runs the reflection planning once
and stores it in a static file.

Dumping is actually quite easy to do. All you need is to put the following
piece of code in a script or command which you run everytime your DTOs change:
```php
(new Dumper())
    ->classes([FooBarDTO::class])
    ->dump($path);
```

Utilising your dumped hydration plans is also very easy.
Instead of using the ReflectionHydrator you should use the
`Recoded\ObjectHydrator\Hydration\CachedHydrator`, and pass the path to your dump
as a first argument when constructing the hydrator. Like so:
```php
use Recoded\ObjectHydrator\Hydration\CachedHydrator;

$hydrator = new CachedHydrator('/path/to/dump.php');

$hydrator->hydrate([...], DTO::class);
```

If you are using a container, it might be worth binding whatever hydrator you're using
to the `Recoded\ObjectHydrator\Contracts\Hydrator` interface. This interface contains
the `hydrate` method, which is all you should need.

## Contributing
Everyone is welcome to contribute. Feel free to PR your work once you think it's ready.
Or open a draft-PR if you want to get some opinions or further help.

I would like to keep this package relatively small and want to avoid bloat. The package
should remain extensible and unopinionated.
