![Screenshot](img/plugin_helpers.jpg)

# About

Microframework for writing CMS plugins, contains development logic and several helpers.

# Install & Usage

```bash
composer require flamix/plugin-helpers
```

```php
// Init
$plugin = \Flamix\Plugin\Init::init(__DIR__, 'FLAMIX_EXCHANGE_PLUGIN')->setLogsPath(WP_CONTENT_DIR . '/upload/flamix/');
// Show witch constant was defined
dd($plugin->defined());
```
