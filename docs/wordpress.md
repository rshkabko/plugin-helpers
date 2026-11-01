# WordPress

Need WordPress loaded (`esc_html`, `get_option`, `get_locale`).

## Markup

Both echo a `<tr class="form-field">` for a settings `<table>`. `description` is printed raw, so it may hold HTML.

```php
use Flamix\Plugin\WP\Markup;

Markup::markup_input('flamix_api_key', [
    'label' => 'API key',        // default: the id
    'value' => get_option('flamix_api_key'),
    'placeholder' => 'xxxx',
    'description' => 'From <a href="https://flamix.solutions">your account</a>',
]);

Markup::markup_select('flamix_status', [
    'label' => 'Status',
    'options' => ['NEW' => 'New', 'SENT' => 'Sent'],
    'value' => 'NEW',            // compared with === to the option key
]);

echo Markup::adminMessage('Saved');           // <div class="updated notice">...
echo Markup::adminMessage('Failed', 'error'); // $msg is not escaped
```

## Checker::isPluginActive()

```php
use Flamix\Plugin\General\Checker;

Checker::isPluginActive('contact-form-7');                        // checks 'contact-form-7/contact-form-7.php'
Checker::isPluginActive('contact-form-7/wp-contact-form-7.php');  // a path with .php is used as is
```

Reads the `MULTISITE` constant directly: define it, or single-site WordPress on PHP 8 throws "Undefined constant".

## Recommendations

```php
use Flamix\Plugin\WP\Recommendations;

foreach (Recommendations::plugins() as $item) {
    // ['name' => 'Contact Form 7', 'url' => 'https://flamix.solutions/...', 'wp' => 'contact-form-7/wp-contact-form-7.php', 'flamix' => 'flamix-bitrix24-and-contact-forms-7-integrations']
    if (Checker::isPluginActive($item['wp']) && !Checker::isPluginActive($item['flamix'])) { /* recommend */ }
}

echo Recommendations::banner('cf7'); // 300x600 banner; get_locale() prefix en or ru, anything else (uk_UA too) gets en
```
