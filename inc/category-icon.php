<?php

if (!defined('ABSPATH')) {
    exit;
}

// ==================== ایجاد آیکون دسته بندی ====================
function category_icon_field() {
  register_rest_field('category', 'icon', [    
    'get_callback' => 'a',
    'schema' => null,
  ]);
};
function a($term) {
  return get_term_meta($term['id'], 'term_icon', true);
}
add_action('rest_api_init', 'category_icon_field');

add_action('category_add_form_fields', function () {
  echo '<div class="form-field">
    <label for="term_image">تصویر دسته‌بندی</label>
    <input type="file" name="term_image" id="term_image" accept="image/*">
    <p class="description">فایل تصویر را انتخاب کنید</p>
  </div>';
});

add_action('category_edit_form_fields', function ($term) {
  $icon = get_term_meta($term->term_id, 'term_icon', true);
  echo '<tr class="form-field">
    <th><label for="term_image">تصویر دسته‌بندی</label></th>
    <td>
      <input type="file" name="term_image" id="term_image" accept="image/*"><br>';
  if ($icon) {
    echo '<img src="' . esc_url($icon) . '" style="margin-top:10px;max-width:100px;height:auto;border:1px solid #ddd;border-radius:4px;">';
  }
  echo '<p class="description">می‌تونی تصویر جدید آپلود کنی</p>
    </td>
  </tr>';
});

function gholly_save_category_image($term_id) {
  if (!empty($_FILES['term_image']['name'])) {
    require_once ABSPATH . 'wp-admin/includes/file.php';

    // حذف تصویر قبلی
    $old_url = get_term_meta($term_id, 'term_icon', true);
    if ($old_url) {
      $upload_dir = wp_upload_dir();
      $relative_path = str_replace($upload_dir['baseurl'], '', $old_url);
      $full_path = $upload_dir['basedir'] . $relative_path;
      if (file_exists($full_path)) {
        unlink($full_path); // حذف فایل قبلی
      }
    }

    // آپلود تصویر جدید
    $upload = wp_handle_upload($_FILES['term_image'], ['test_form' => false]);
    if (!isset($upload['error']) && isset($upload['url'])) {
      update_term_meta($term_id, 'term_icon', esc_url_raw($upload['url']));
    }
  }
}
add_action('created_category', 'gholly_save_category_image');
add_action('edited_category', 'gholly_save_category_image');


add_action('admin_head', function () {
  echo '<script>
    document.addEventListener("DOMContentLoaded", function () {
      const addForm = document.querySelector("form#addtag");
      const editForm = document.querySelector("form#edittag");
      if (addForm) addForm.setAttribute("enctype", "multipart/form-data");
      if (editForm) editForm.setAttribute("enctype", "multipart/form-data");
    });
  </script>';
});

add_filter('upload_mimes', function ($mimes) {
  if (current_user_can('administrator')) {
    $mimes['svg'] = 'image/svg+xml';
  }
  return $mimes;
});
