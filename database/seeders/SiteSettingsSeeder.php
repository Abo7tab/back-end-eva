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
            
            // ─── GENERAL ─────────────────
            ['setting_key' => 'site_name', 'setting_value' => 'Eva Beauty Hub', 'setting_group' => 'general', 'value_type' => 'text'],
            ['setting_key' => 'site_tagline_ar', 'setting_value' => 'منصتك الأولى لمنتجات التجميل', 'setting_group' => 'general', 'value_type' => 'text'],
            ['setting_key' => 'copyright_text_ar', 'setting_value' => '© 2025 Eva Beauty Hub - جميع الحقوق محفوظة', 'setting_group' => 'general', 'value_type' => 'text'],
            ['setting_key' => 'business_hours_ar', 'setting_value' => 'السبت - الخميس: 9 صباحاً - 10 مساءً', 'setting_group' => 'general', 'value_type' => 'text'],

            // ─── CONTACT ─────────────────
            ['setting_key' => 'contact_email', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'contact_phone_display', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'contact_address_ar', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'textarea'],
            ['setting_key' => 'social_facebook', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'social_instagram', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'social_tiktok', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'social_youtube', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],
            ['setting_key' => 'social_whatsapp_main', 'setting_value' => null, 'setting_group' => 'contact', 'value_type' => 'text'],

            // ─── SEO (Additional) ─────────────────
            ['setting_key' => 'seo_meta_keywords', 'setting_value' => 'منتجات تجميل, كوزمتك, عناية بالبشرة, مصر', 'setting_group' => 'seo', 'value_type' => 'textarea'],
            ['setting_key' => 'seo_og_image_url', 'setting_value' => null, 'setting_group' => 'seo', 'value_type' => 'image'],
            ['setting_key' => 'seo_business_name', 'setting_value' => 'Eva Beauty Hub', 'setting_group' => 'seo', 'value_type' => 'text'],
            ['setting_key' => 'seo_business_type', 'setting_value' => 'Beauty & Cosmetics Store', 'setting_group' => 'seo', 'value_type' => 'text'],
            ['setting_key' => 'seo_business_description_ar', 'setting_value' => null, 'setting_group' => 'seo', 'value_type' => 'textarea'],

            // ─── AEO ─────────────────
            ['setting_key' => 'aeo_business_summary_ar', 'setting_value' => 'متجر متخصص في منتجات التجميل والعناية بالبشرة عالية الجودة', 'setting_group' => 'aeo', 'value_type' => 'textarea'],
            ['setting_key' => 'aeo_key_features_ar', 'setting_value' => 'توصيل سريع, منتجات أصلية, أسعار منافسة, خدمة عملاء ممتازة', 'setting_group' => 'aeo', 'value_type' => 'textarea'],
            ['setting_key' => 'aeo_target_audience_ar', 'setting_value' => 'النساء والرجال المهتمين بالعناية الشخصية', 'setting_group' => 'aeo', 'value_type' => 'textarea'],
            ['setting_key' => 'aeo_faqs_json', 'setting_value' => '[]', 'setting_group' => 'aeo', 'value_type' => 'textarea'],

            // ─── CONTENT (CMS) ─────────────────
            ['setting_key' => 'content_landing_title_ar', 'setting_value' => 'أهلاً بك في إيفا', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_landing_subtitle_ar', 'setting_value' => 'اختر قسمك للبدء', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_landing_women_button_ar', 'setting_value' => 'منتجات حريمي', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_landing_men_button_ar', 'setting_value' => 'منتجات رجالي', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_landing_skip_ar', 'setting_value' => 'تصفح الكل', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_home_hero_title_default_ar', 'setting_value' => 'اكتشف عالم الجمال', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_home_hero_subtitle_default_ar', 'setting_value' => 'أفضل منتجات التجميل بين يديك', 'setting_group' => 'content', 'value_type' => 'textarea'],
            ['setting_key' => 'content_home_hero_cta_default_ar', 'setting_value' => 'تسوق الآن', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_home_hero_title_women_ar', 'setting_value' => 'جمالك يبدأ من هنا', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_home_hero_subtitle_women_ar', 'setting_value' => 'منتجات مختارة بعناية خصيصاً لكِ', 'setting_group' => 'content', 'value_type' => 'textarea'],
            ['setting_key' => 'content_home_hero_cta_women_ar', 'setting_value' => 'اكتشفي المنتجات', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_home_hero_title_men_ar', 'setting_value' => 'للرجل العصري', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_home_hero_subtitle_men_ar', 'setting_value' => 'منتجات العناية والأناقة للرجل', 'setting_group' => 'content', 'value_type' => 'textarea'],
            ['setting_key' => 'content_home_hero_cta_men_ar', 'setting_value' => 'تصفح الآن', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_featured_section_title_ar', 'setting_value' => 'منتجاتنا المميزة', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_trending_section_title_ar', 'setting_value' => 'الأكثر رواجاً', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_categories_section_title_ar', 'setting_value' => 'تصفح حسب القسم', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_brands_section_title_ar', 'setting_value' => 'أفضل البراندات', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_add_to_cart_button_ar', 'setting_value' => 'أضف للسلة', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_view_product_button_ar', 'setting_value' => 'عرض التفاصيل', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_order_now_button_ar', 'setting_value' => 'اطلب الآن', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_continue_shopping_button_ar', 'setting_value' => 'متابعة التسوق', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_empty_cart_ar', 'setting_value' => 'سلة التسوق فارغة', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_no_products_ar', 'setting_value' => 'لا توجد منتجات في هذا القسم', 'setting_group' => 'content', 'value_type' => 'text'],
            ['setting_key' => 'content_search_no_results_ar', 'setting_value' => 'لا نتائج للبحث', 'setting_group' => 'content', 'value_type' => 'text'],

            ['setting_key' => 'content_footer_about_ar', 'setting_value' => 'إيفا - وجهتك المفضلة لمنتجات التجميل عالية الجودة', 'setting_group' => 'content', 'value_type' => 'textarea'],

            // ─── ANALYTICS ─────────────────
            ['setting_key' => 'analytics_google_id', 'setting_value' => null, 'setting_group' => 'analytics', 'value_type' => 'text'],
            ['setting_key' => 'analytics_facebook_pixel_id', 'setting_value' => null, 'setting_group' => 'analytics', 'value_type' => 'text'],
            ['setting_key' => 'analytics_tiktok_pixel_id', 'setting_value' => null, 'setting_group' => 'analytics', 'value_type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
