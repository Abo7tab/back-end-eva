<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Operations\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'seo_meta_title', 'setting_value' => 'Eva Beauty Hub', 'setting_group' => 'seo', 'value_type' => 'text'],
            ['setting_key' => 'seo_meta_description', 'setting_value' => 'Premium cosmetics and beauty products', 'setting_group' => 'seo', 'value_type' => 'textarea'],
            ['setting_key' => 'whatsapp_order_template', 'setting_value' => 'New Order: {ref}\nTotal: {total}', 'setting_group' => 'whatsapp', 'value_type' => 'textarea'],
            ['setting_key' => 'whatsapp_include_images', 'setting_value' => 'true', 'setting_group' => 'whatsapp', 'value_type' => 'boolean'],
            ['setting_key' => 'site_logo_url', 'setting_value' => null, 'setting_group' => 'general', 'value_type' => 'image'],
            ['setting_key' => 'site_favicon_url', 'setting_value' => null, 'setting_group' => 'general', 'value_type' => 'image'],
            ['setting_key' => 'contact_phone', 'setting_value' => '+201000000000', 'setting_group' => 'general', 'value_type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
