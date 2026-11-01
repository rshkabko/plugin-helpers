![Screenshot](img/plugin_helpers.jpg)

# Plugin Helpers

Microframework for CMS plugins (plain PHP, no Laravel): bootstrap, file logs, a jobs queue, WordPress markup and helpers.

## Install

```bash
composer require flamix/plugin-helpers
```

## Documentation

- [Init and logging](docs/init.md) — `Init::init()`, `setLogsPath()`, `flamix_log()`
- [Helpers](docs/helpers.md) — `tap()`, `throw_if()`, `throw_unless()` (Laravel name collision), strings, `Checker`
- [Queue](docs/queue.md) — `JobCommands`, `SQL`, `ShouldQueue`
- [WordPress](docs/wordpress.md) — `Markup`, `Checker::isPluginActive()`, `Recommendations`
