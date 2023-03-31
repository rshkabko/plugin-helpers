<?php

namespace Flamix\Plugin\WP;

class Recommendations
{
    /**
     * WordPress recommended plugins.
     *
     * Use for "auto" recommendations in main view section.
     *
     * @return array
     */
    public static function plugins(): array
    {
        return [
            [
                'name' => 'WooCommerce (Orders)',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/woocommerce.php',
                'wp' => 'woocommerce/woocommerce.php',
                'flamix' => 'flamix-bitrix24-and-woo-integrations/flamix-bitrix24-and-woo-integrations.php',
            ],
            [
                'name' => 'WooCommerce (Products)',
                'url' => 'https://flamix.solutions/bitrix24/warehouse/sync_products.php',
                'wp' => 'woocommerce/woocommerce.php',
                'flamix' => 'flamix-bitrix24-and-woo-products-sync/flamix-bitrix24-and-woo-products-sync.php',
            ],
            [
                'name' => 'WooCommerce (Rests)',
                'url' => 'https://flamix.solutions/bitrix24/warehouse/sync_rests.php',
                'wp' => 'woocommerce/woocommerce.php',
                'flamix' => 'flamix-bitrix24-and-woo-products-sync/flamix-bitrix24-and-woo-products-sync.php',
            ],
            [
                'name' => 'Contact Form 7',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/cf7.php',
                'wp' => 'contact-form-7/wp-contact-form-7.php',
                'flamix' => 'flamix-bitrix24-and-contact-forms-7-integrations/flamix-bitrix24-and-contact-forms-7-integrations.php',
            ],
            [
                'name' => 'Ninja Forms',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/ninja-forms.php',
                'wp' => 'ninja-forms/ninja-forms.php',
                'flamix' => 'flamix-bitrix24-ninjaforms/flamix-bitrix24-ninjaforms.php',
            ],
            [
                'name' => 'Elementor forms',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/elementor-forms.php',
                'wp' => 'elementor/elementor.php',
                'flamix' => 'flamix-bitrix24-elementor/flamix-bitrix24-elementor.php',
            ],
            [
                'name' => 'WPForms Lite',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/wpforms.php',
                'wp' => 'wpforms-lite/wpforms.php',
                'flamix' => 'flamix-bitrix24-wpforms/flamix-bitrix24-wpforms.php',
            ],
            [
                'name' => 'WPForms',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/wpforms.php',
                'wp' => 'wpforms/wpforms.php',
                'flamix' => 'flamix-bitrix24-wpforms/flamix-bitrix24-wpforms.php',
            ],
            [
                'name' => 'Fluent form',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/fluent-form.php',
                'wp' => 'fluentform/fluentform.php',
                'flamix' => 'flamix-bitrix24-fluentform/flamix-bitrix24-fluentform.php',
            ],
            [
                'name' => 'Forminator',
                'url' => 'https://flamix.solutions/bitrix24/integrations/site/forminator.php',
                'wp' => 'forminator/forminator.php',
                'flamix' => 'flamix-bitrix24-forminator/flamix-bitrix24-forminator.php',
            ],
        ];
    }

    /**
     * Get banner info.
     *
     * @param string $code Module code. Will be use in utm_campaign.
     * @param bool $images Images or URL?
     * @return string
     */
    public static function banner(string $code, bool $images = true): string
    {
        if ($images)
            return '//s3.eu-central-1.amazonaws.com/flamix-solutions/banners/bitrix24_apps_general/bitrix24_600_1067.jpg';

        return "https://flamix.solutions/?utm_source=module&utm_campaign={$code}&utm_term=" . $_SERVER['HTTP_HOST'] ?? '';
    }
}