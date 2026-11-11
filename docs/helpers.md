# Helpers

Global functions from `src/helpers.php`, loaded by Composer.

## Laravel name collision

`tap()`, `throw_if()` and `throw_unless()` have Laravel's names and are wrapped in `function_exists()`. Inside a Laravel app either version may run (load order is not guaranteed); Laravel's `tap($x)` returns `Illuminate\Support\HigherOrderTapProxy`. Keep calls compatible with both.

## tap()

```php
$order = tap(new Order(), function (Order $order) {
    $order->status = 'NEW';
}); // returns the Order

tap($order)->save(); // calls save() and returns $order, not save()'s result
```

## throw_if() / throw_unless()

```php
throw_if(!$token, InvalidArgumentException::class, 'No token'); // new InvalidArgumentException('No token')
throw_if($error, 'Sync failed');                                 // a string that is not a class: RuntimeException('Sync failed')
throw_unless($order, new LogicException('No order'));            // an instance is thrown as is

$id = throw_unless($id, 'Empty id'); // both return the condition when nothing is thrown
```

## flamix_log()

See [init.md](init.md#flamix_log).

## Strings

```php
use Flamix\Plugin\General\Helpers;
use Flamix\Plugin\General\Status;

Helpers::slug('Привет, мир! 2024');        // 'privet-mir-2024'
Helpers::slug('Hello - big world', '_');   // 'hello_big_world'
Helpers::limit('Hello world', 5);          // 'Hello...' (default limit 100, end '...')
Helpers::parseDomain('shop.com/catalog');  // 'shop.com', a scheme is added when the string does not start with one
Status::key('Sent to CRM');                // 'SENT_TO_CRM', at most 60 characters
```

## Checker

```php
use Flamix\Plugin\General\Checker;

Checker::params('PHP 8', PHP_VERSION_ID >= 80000);           // 'PHP 8: <span style="color: #46b450;">Enable</span>'
Checker::params('cURL', fn() => function_exists('curl_init'), ['Yes', 'No']); // pass text, fail text (default 'Disabled')
Checker::isEmail('a@b.com');                                 // 'a@b.com', or false
```

In WordPress `params()` output goes through `wp_kses_post()`. `Checker::isPluginActive()` is in [wordpress.md](wordpress.md).
