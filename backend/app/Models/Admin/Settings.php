<?php

namespace App\Models\Admin;

use App\Models\AppModel;

class Settings extends AppModel
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Fetch option value
     *
     * @param  string $key
     * @return Response
     */
    public static function get($key)
    {
        return self::where('name', $key)->limit(1)->pluck('value')->first();
    }


    /**
     * Update option value
     *
     * @param  Settings $key
     * @return Response
     */
    public static function put($key, $value)
    {
        $option = self::where('name', $key)->limit(1)->first();
        if ($option) {
            $option->value = $value ? $value : "";
            return $option->save();
        } else {
            $option = new Settings();
            $option->name = $key;
            $option->value = $value;
            return $option->save();
        }
        return false;
    }


    public static function dateFormats()
    {
        $formats = [
            'd-m-Y' => 'DD-MM-YYYY',
            'd/m/Y' => 'DD/MM/YYYY',
            'm-d-Y' => 'MM-DD-YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'Y-m-d' => 'YYYY-MM-DD'
        ];
        return $formats;
    }

    public static function timeFormats()
    {
        $formats = [
            'H:i' => '24 hours',
            'h:i a' => '12 hours'
        ];
        return $formats;
    }

    public static function timeZones()
    {
        $zones = [
            ['label' => 'EUROPE', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE)],
            ['label' => 'AMERICA', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA)],
            ['label' => 'INDIAN', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN)],
            ['label' => 'AUSTRALIA', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA)],
            ['label' => 'ASIA', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA)],
            ['label' => 'AFRICA', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA)],
            ['label' => 'ANTARCTICA', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA)],
            ['label' => 'ARCTIC', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC)],
            ['label' => 'ATLANTIC', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC)],
            ['label' => 'PACIFIC', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC)],
            ['label' => 'UTC', 'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC)]
        ];
        return $zones;
    }

    public static function getPaymentConfig()
    {
        return [
            'stripe_enabled' => self::get('stripe_enabled') === 'true',
            'stripe_public_key' => self::get('stripe_public_key'),
            'stripe_secret_key' => self::get('stripe_secret_key'),
            'stripe_webhook_secret' => self::get('stripe_webhook_secret'),
            'test_mode' => self::get('payment_test_mode') === 'true',
            'currency_code' => self::get('currency_code') ?: 'INR',
            'currency_symbol' => self::get('currency_symbol') ?: '₹',
            'tax_rate' => (float) (self::get('tax_rate') ?: 20),
            'free_shipping_threshold' => (float) (self::get('free_shipping_threshold') ?: 0),
        ];
    }

    /**
     * Get all currency settings
     */
    public static function getCurrencySettings()
    {
        return [
            'currency_symbol' => self::get('currency_symbol') ?: '£',
            'currency_position' => self::get('currency_position') ?: 'left',
            'decimal_separator' => self::get('decimal_separator') ?: '.',
            'thousand_separator' => self::get('thousand_separator') ?: ',',
        ];
    }

    /**
     * Format price with currency symbol
     */
    public static function formatPrice($amount)
    {
        $settings = self::getCurrencySettings();
        $formatted = number_format(
            $amount,
            2,
            $settings['decimal_separator'],
            $settings['thousand_separator']
        );

        if ($settings['currency_position'] === 'left') {
            return $settings['currency_symbol'] . $formatted;
        } else {
            return $formatted . ' ' . $settings['currency_symbol'];
        }
    }
}
