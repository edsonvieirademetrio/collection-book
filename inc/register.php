<?php

//Register a CPT Colletion
function cpt_collection() {
    $labels = array(
        'name'               => _x('Collections', 'post type general name', 'textdomain'),
        'singular_name'      => _x('Collection', 'post type singular name', 'textdomain'),
        'menu_name'          => _x('Collections', 'admin menu', 'textdomain'),
        'name_admin_bar'     => _x('Collection', 'add new on admin bar', 'textdomain'),
        'add_new'            => _x('Add New', 'collection', 'textdomain'),
        'add_new_item'       => __('Add New Collection', 'textdomain'),
        'new_item'           => __('New Collection', 'textdomain'),
        'edit_item'          => __('Edit Collection', 'textdomain'),
        'view_item'          => __('View Collection', 'textdomain'),
        'all_items'          => __('All Collections', 'textdomain'),
        'search_items'       => __('Search Collections', 'textdomain'),
        'parent_item_colon'  => __('Parent Collections:', 'textdomain'),
        'not_found'          => __('No collections found.', 'textdomain'),
        'not_found_in_trash' => __('No collections found in Trash.', 'textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'collection'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
    );

    register_post_type('collection', $args);
}
add_action('init', 'cpt_collection');


//Register a CF Collection
function add_collection_cf($post_id) {
    if (isset($_POST['title'])) {
        update_post_meta($post_id, 'title', sanitize_text_field($_POST['title']));
    }
    if (isset($_POST['description'])) {
        update_post_meta($post_id, 'description', sanitize_text_field($_POST['description']));
    }
    if (isset($_POST['material_type'])) {
        update_post_meta($post_id, 'material_type', sanitize_text_field($_POST['material_type']));
    }
    if (isset($_POST['author_collection'])) {
        update_post_meta($post_id, 'author_collection', sanitize_text_field($_POST['author_collection']));
    }
    if (isset($_POST['location_collection'])) {
        update_post_meta($post_id, 'location_collection', sanitize_text_field($_POST['location_collection']));
    }
}
add_action('save_post', 'add_collection_cf');