<?php 

function add_custom_post_types() {
    register_post_type('ads', [
        'labels' => [
            'name' => 'Ads',
            'singular_name' => 'ad',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true, // برای REST API
        'supports' => ['title', 'editor', 'custom-fields'],
        'menu_icon' => 'dashicons-portfolio',
    ]);

    register_post_type('packages', [
        'labels' => [
            'name' => 'Packages',
            'singular_name' => 'package',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true, // برای REST API
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'menu_icon' => 'dashicons-star-filled',
    ] );
}

add_action('init', 'add_custom_post_types');
