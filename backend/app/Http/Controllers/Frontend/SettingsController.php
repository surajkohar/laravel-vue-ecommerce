<?php

/**
 * Products Class
 *
 * @package    ProductsController
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Admin\Settings;

class SettingsController extends ApiController
{

    public function getFrontendSettings(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => [
                    'currency' => Settings::getCurrencySettings(),
                    'payment' => Settings::getPaymentConfig(),
                    'site' => [
                        'name' => Settings::get('site_name') ?: 'Pinders',
                        'logo' => Settings::get('site_logo') ? asset('storage/' . Settings::get('site_logo')) : null,
                        'contact_email' => Settings::get('contact_email'),
                        'contact_phone' => Settings::get('contact_phone'),
                    ],
                    'shipping' => [
                        'default_cost' => (float) (Settings::get('default_shipping_cost') ?: 4.99),
                        'free_threshold' => (float) (Settings::get('free_shipping_threshold') ?: 0),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Get frontend settings error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to get settings'
            ], 500);
        }
    }
}
