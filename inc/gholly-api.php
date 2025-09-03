<?php

function gholly_register_custom_fields_in_api() {
    register_rest_field('project', 'start_date', [
        'get_callback' => function($post_arr) {
            return get_post_meta($post_arr['id'], '_start_date', true);
        },
        'schema' => null,
    ]);
}
add_action('rest_api_init', 'gholly_register_custom_fields_in_api');
