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
            ['setting_key' => 'whatsapp_order_template', 'setting_value' => "السلام عليكم 👋\nعايز أطلب:\n\n{items}\n\n━━━━━━━━━━━━━━━\nعدد المنتجات: {total_items}\n💰 المجموع الفرعي: {subtotal}\n💵 الإجمالي: {total_price}\n\nرقم الطلب: {reference_code}", 'setting_group' => 'whatsapp', 'value_type' => 'textarea'],
            ['setting_key' => 'whatsapp_include_images', 'setting_value' => 'true', 'setting_group' => 'whatsapp', 'value_type' => 'boolean'],
            ['setting_key' => 'site_logo_url', 'setting_value' => null, 'setting_group' => 'general', 'value_type' => 'image'],
            ['setting_key' => 'site_favicon_url', 'setting_value' => null, 'setting_group' => 'general', 'value_type' => 'image'],

            // ─── THEME SETTINGS: DEFAULT ─────────────────
            ['setting_key' => 'theme_default_primary_color', 'setting_value' => '#F97316', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_secondary_color', 'setting_value' => '#FFFFFF', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_accent_color', 'setting_value' => '#FED7AA', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_text_color', 'setting_value' => '#1F2937', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_logo_url', 'setting_value' => null, 'setting_group' => 'theme', 'value_type' => 'image'],
            ['setting_key' => 'theme_default_bg_color_1', 'setting_value' => '#F97316', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_bg_color_2', 'setting_value' => '#FB923C', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_default_animation_speed', 'setting_value' => '50', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_default_animation_blur', 'setting_value' => '80', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_default_animation_opacity', 'setting_value' => '60', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_default_animation_blobs_count', 'setting_value' => '4', 'setting_group' => 'theme', 'value_type' => 'number'],

            // ─── THEME SETTINGS: WOMEN ─────────────────
            ['setting_key' => 'theme_women_primary_color', 'setting_value' => '#EC4899', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_secondary_color', 'setting_value' => '#FFFFFF', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_accent_color', 'setting_value' => '#FBCFE8', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_text_color', 'setting_value' => '#1F2937', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_logo_url', 'setting_value' => null, 'setting_group' => 'theme', 'value_type' => 'image'],
            ['setting_key' => 'theme_women_bg_color_1', 'setting_value' => '#EC4899', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_bg_color_2', 'setting_value' => '#F472B6', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_women_animation_speed', 'setting_value' => '50', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_women_animation_blur', 'setting_value' => '80', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_women_animation_opacity', 'setting_value' => '60', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_women_animation_blobs_count', 'setting_value' => '4', 'setting_group' => 'theme', 'value_type' => 'number'],

            // ─── THEME SETTINGS: MEN ─────────────────
            ['setting_key' => 'theme_men_primary_color', 'setting_value' => '#1E40AF', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_secondary_color', 'setting_value' => '#FFFFFF', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_accent_color', 'setting_value' => '#3B82F6', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_text_color', 'setting_value' => '#1F2937', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_logo_url', 'setting_value' => null, 'setting_group' => 'theme', 'value_type' => 'image'],
            ['setting_key' => 'theme_men_bg_color_1', 'setting_value' => '#1E40AF', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_bg_color_2', 'setting_value' => '#3B82F6', 'setting_group' => 'theme', 'value_type' => 'color'],
            ['setting_key' => 'theme_men_animation_speed', 'setting_value' => '50', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_men_animation_blur', 'setting_value' => '80', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_men_animation_opacity', 'setting_value' => '60', 'setting_group' => 'theme', 'value_type' => 'number'],
            ['setting_key' => 'theme_men_animation_blobs_count', 'setting_value' => '4', 'setting_group' => 'theme', 'value_type' => 'number'],
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
