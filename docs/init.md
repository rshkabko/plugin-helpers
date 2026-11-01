# Init and logging

Bootstrap a plugin once, from its main file.

```php
use Flamix\Plugin\Init;

$plugin = Init::init(__DIR__, 'FLAMIX_EXCHANGE_PLUGIN')
    ->setLogsPath(WP_CONTENT_DIR . '/uploads/flamix'); // defines FLAMIX_LOGS_PATH, the first call wins

FLAMIX_EXCHANGE_PLUGIN_CODE;  // 'flamix-exchange': the last segment of __DIR__
FLAMIX_EXCHANGE_PLUGIN_DIR;   // __DIR__
$plugin->getPluginCode();     // same as _CODE
$plugin->getPluginDir();      // same as _DIR

// All three constants; call it only after setLogsPath(), otherwise "Undefined constant FLAMIX_LOGS_PATH"
$plugin->defined();
```

A second `Init::init()` with the same constant name throws `Exception('Plugin already defined!')`.

## flamix_log()

```php
flamix_log('Order sent', ['id' => 7]);             // FLAMIX_LOGS_PATH/logs/2026-11-01-info-<md5 of date>.log
flamix_log('Order sent', ['id' => 7], 'orders');   // FLAMIX_LOGS_PATH/logs/orders/...
// Line: [...] commerceml.INFO: Order sent {"id":7} []  (the logger is always named "commerceml")

\Flamix\Plugin\General\Helpers::log('Same thing', [], 'orders'); // what flamix_log() calls
```

Without `setLogsPath()` it throws `Exception('Logs path not defined, use setLogsPath() when init plugin!')`.
