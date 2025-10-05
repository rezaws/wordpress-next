<?php

function register_packages_custom_fields()
{
    $fields = [
        '_pkg_href'        => ['type' => 'string'],
        '_pkg_price'       => ['type' => 'string'],
        '_pkg_features'    => ['type' => 'string'],
        '_pkg_max_ads'     => ['type' => 'string'],
        '_pkg_duration'    => ['type' => 'number'],
        '_pkg_featured'    => ['type' => 'boolean'], // اینجا نوعش رو جداگانه مشخص کردی
    ];

    foreach ($fields as $field => $args) {
        register_post_meta('packages', $field, array_merge([
            'show_in_rest' => true,
            'single' => true,
        ], $args));
    };
};

add_action('init', 'register_packages_custom_fields');


function add_packages_meta_box()
{
    add_meta_box(
        'packages_meta_box',          // ID
        'اطلاعات پکیج',              // عنوان
        'render_packages_meta_box',   // تابع نمایش
        'packages',                   // پست تایپ هدف
        'normal',                     // موقعیت
        'default'                     // اولویت
    );
}
add_action('add_meta_boxes', 'add_packages_meta_box');


function render_packages_meta_box($post)
{
    // دریافت مقادیر قبلی
    $price =       get_post_meta($post->ID, '_pkg_price', true);
    $features =    get_post_meta($post->ID, '_pkg_features', true);
    $href =        get_post_meta($post->ID, '_pkg_href', true);
    $featured =    get_post_meta($post->ID, '_pkg_featured', true);
    $duration =    get_post_meta($post->ID, '_pkg_duration', true);
    // nonce برای امنیت
    wp_nonce_field('save_packages_meta_box', 'packages_meta_box_nonce');

    echo '<label>قیمت:</label><br>';
    echo '<input type="text" name="pkg_price" value="' . esc_attr($price) . '" style="width:100%;"><br><br>';

    echo '<label>امکانات:</label><br>';
    echo '<input type="text" name="pkg_features" value="' . esc_attr($features) . '" style="width:100%;"><br><br>';

    echo '<label for="pkg_featured">پکیج ویژه:</label><br>';
    echo '<input type="checkbox" name="pkg_featured" value="true" ' . checked($featured, true, false) . '> فعال<br><br>';

    echo '<label for="pkg_href">لینک پکیج:</label><br>';
    echo '<input type="text" name="pkg_href" value="' . esc_attr($href) . '" style="width:100%;"><br><br>';

    echo '<label for="pkg_href">مدت نمایش:</label><br>';
    echo '<input type="text" name="pkg_duration" value="' . esc_attr($duration) . '" style="width:100%;"><br><br>';
}


function save_packages_meta_box_data($post_id)
{
    // بررسی nonce
    if (
        !isset($_POST['packages_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['packages_meta_box_nonce'], 'save_packages_meta_box')
    ) {
        return;
    }

    // جلوگیری از ذخیره در حالت autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // بررسی سطح دسترسی
    if (!current_user_can('edit_post', $post_id)) return;

    // ذخیره فیلدها
    $fields = [
        '_pkg_href'         => 'pkg_href',
        '_pkg_price'        => 'pkg_price',
        '_pkg_package_id'   => 'pkg_package_id',
        '_pkg_features'     => 'pkg_features',
        '_pkg_featured'     => 'pkg_featured',
        '_pkg_max_ads'      => 'pkg_max_ads',
        '_pkg_duration'     => 'pkg_duration',

    ];

    foreach ($fields as $meta_key => $post_key) {
        if ($meta_key === '_pkg_featured') {
            // چک‌باکس: اگر انتخاب شده مقدار true، در غیر این صورت false
            $value = isset($_POST[$post_key]) ? true : false;
        } else {
            // سایر فیلدها: مقدار متنی
            $value = isset($_POST[$post_key]) ? sanitize_text_field($_POST[$post_key]) : '';
        }

        update_post_meta($post_id, $meta_key, $value);
    }
}
add_action('save_post', 'save_packages_meta_box_data');


/* ----------------------------------------------------------- */

function register_ads_custom_fields()
{
    $fields = [
        '_ads_tags'             => ['type' => 'string'],
        '_ads_google_address'   => ['type' => 'string'],
        '_ads_Phone'            => ['type' => 'string'],
        '_ads_email'            => ['type' => 'string'],
        '_ads_website'          => ['type' => 'string'],
        '_ads_gallery'          => ['type' => 'string'],
        '_ads_logo'             => ['type' => 'string'],
        '_ads_packages_id'      => ['type' => 'string'],
        '_ads_author'           => ['type' => 'string'],
        '_ads_verify'           => ['type' => 'boolean'],
    ];

    foreach ($fields as $field => $args) {
        register_post_meta('ads', $field, array_merge([
            'show_in_rest' => true,
            'single' => true,
        ], $args));
    }
}
add_action('init', 'register_ads_custom_fields');

function add_ads_meta_box()
{
    add_meta_box(
        'ads_meta_box',
        'اطلاعات آگهی',
        'render_ads_meta_box',
        'ads',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_ads_meta_box');

function render_ads_meta_box($post)
{
    $fields = [
        '_ads_tags'             => 'تگ‌ها',
        '_ads_google_address'   => 'نقشه گوگل',
        '_ads_Phone'            => 'تلفن/موبایل',
        '_ads_email'            => 'ایمیل',
        '_ads_website'          => 'وبسایت',
        '_ads_gallery'          => 'گالری',
        '_ads_logo'             => 'لوگو',
        '_ads_packages_id'      => 'شناسه پکیج',
        '_ads_author'           => 'ادمین',
        '_ads_verify'           => 'تأیید شده',
    ];

    wp_nonce_field('save_ads_meta_box', 'ads_meta_box_nonce');

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo "<label for='{$key}'>{$label}:</label><br>";

        if ($key === '_ads_verify') {
            $checked = $value ? 'checked' : '';
            echo "<input type='checkbox' name='{$key}' value='1' {$checked}><br><br>";
        } else {
            echo "<input type='text' name='{$key}' value='" . esc_attr($value) . "' style='width:100%;'><br><br>";
        }
    }
}

function save_ads_meta_fields($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'ads') return;
    if (!isset($_POST['ads_meta_box_nonce']) || !wp_verify_nonce($_POST['ads_meta_box_nonce'], 'save_ads_meta_box')) return;

    $fields = [
        '_ads_tags'             => 'string',
        '_ads_google_address'   => 'string',
        '_ads_Phone'            => 'string',
        '_ads_email'            => 'string',
        '_ads_website'          => 'string',
        '_ads_gallery'          => 'string',
        '_ads_logo'             => 'string',
        '_ads_packages_id'      => 'string',
        '_ads_author'           => 'string',
        '_ads_verify'           => 'boolean',
    ];

    foreach ($fields as $key => $type) {
        $value = $_POST[$key] ?? '';

        switch ($type) {
            case 'string':
                $value = sanitize_text_field($value);
                break;
            case 'boolean':
                $value = isset($_POST[$key]) ? true : false;
                break;
        }

        update_post_meta($post_id, $key, $value);
    }
}
add_action('save_post', 'save_ads_meta_fields');


// -----------------------------------------------------------------------
