<?php

function register_packages_custom_fields() {
      $fields = [
    '_pkg_href',
    '_pkg_price',
    '_pkg_description',
    '_pkg_features',
    '_pkg_featured',
  ];

  foreach ($fields as $field) {
    register_post_meta('packages', $field, [
      'type' => 'string',
      'show_in_rest' => true,
      'single' => true,
    ]);
  }
}

add_action('init', 'register_packages_custom_fields');

function save_packages_custom_fields($post_id) {
  // بررسی اینکه نوع پست درست باشه
  if (get_post_type($post_id) !== 'packages') return;

  // لیست فیلدهای سفارشی
  $fields = [
    '_pkg_href'         => 'pkg_href',
    '_pkg_price'        => 'pkg_price',
    '_pkg_description'  => 'pkg_description',
    '_pkg_features'     => 'pkg_features',
    '_pkg_featured'     => 'pkg_featured',
  ];

  foreach ($fields as $meta_key => $post_key) {
    if ($meta_key === '_pkg_featured') {
      // چک‌باکس: اگر انتخاب شده مقدار true، در غیر این صورت false
      $value = isset($_POST[$post_key]) ? 'true' : 'false';
    } else {
      // سایر فیلدها: مقدار متنی
      $value = isset($_POST[$post_key]) ? sanitize_text_field($_POST[$post_key]) : '';
    }

    update_post_meta($post_id, $meta_key, $value);
  }
}

add_action('save_post', 'save_packages_custom_fields');



function add_packages_meta_box() {
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


function render_packages_meta_box($post) {
    // دریافت مقادیر قبلی
    $price =       get_post_meta($post->ID, '_pkg_price', true);
    $features =    get_post_meta($post->ID, '_pkg_featured', true);
    $description = get_post_meta($post->ID, '_pkg_description', true);
    $href =        get_post_meta($post->ID, '_pkg_href', true);
    $featured =    get_post_meta($post->ID, '_pkg_featured', true);
    $checked = ($featured === 'true') ? 'checked' : '';
    // nonce برای امنیت
    wp_nonce_field('save_packages_meta_box', 'packages_meta_box_nonce');

    echo '<label>قیمت:</label><br>';
    echo '<input type="text" name="pkg_price" value="' . esc_attr($price) . '" style="width:100%;"><br><br>';

    echo '<label>امکانات:</label><br>';
    echo '<input type="text" name="pkg_features" value="' . esc_attr($features) . '" style="width:100%;"><br><br>';

    echo '<label>دسته بندی:</label><br>';
    echo '<input type="text" name="pkg_description" value="' . esc_attr($description) . '" style="width:100%;"><br>';

    echo '<label for="pkg_featured">پکیج ویژه:</label><br>';
    echo '<input type="checkbox" name="pkg_featured" value="true" ' . $checked . '> فعال<br>';

    echo '<label for="pkg_href">لینک پکیج:</label><br>';
    echo '<input type="text" name="pkg_href" value="' . esc_attr($href) . '" style="width:100%;"><br><br>';
}


function save_packages_meta_box_data($post_id) {
    // بررسی nonce
    if (!isset($_POST['packages_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['packages_meta_box_nonce'], 'save_packages_meta_box')) {
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
      '_pkg_description'  => 'pkg_description',
      '_pkg_features'     => 'pkg_features',
      '_pkg_featured'     => 'pkg_featured',
    ];

  foreach ($fields as $meta_key => $post_key) {
    if ($meta_key === '_pkg_featured') {
      // چک‌باکس: اگر انتخاب شده مقدار true، در غیر این صورت false
      $value = isset($_POST[$post_key]) ? 'true' : 'false';
    } else {
      // سایر فیلدها: مقدار متنی
      $value = isset($_POST[$post_key]) ? sanitize_text_field($_POST[$post_key]) : '';
    }

    update_post_meta($post_id, $meta_key, $value);
  }
}
add_action('save_post', 'save_packages_meta_box_data');
