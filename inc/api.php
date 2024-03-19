<?php

//Create a CPT Collection
add_action('rest_api_init', function() {
    register_rest_route('collection-book/v1', '/collections', array(
        'methods'  => 'POST',
        'callback' => 'create_collection',
        'permission_callback' => 'collections_permissions_callback',
    ));
});

function create_collection($request) {
    $params = $request->get_params();

    $new_post = array(
        'post_title'    => sanitize_text_field($params['title']),
        'post_content'  => sanitize_textarea_field($params['description']),
        'post_type'     => 'collection',
        'post_status'   => 'publish',
    );

    $post_id = wp_insert_post($new_post);

    if ($post_id) {
        // Adicionando os custom fields
        update_post_meta($post_id, 'material_type', sanitize_text_field($params['material_type']));
        update_post_meta($post_id, 'author_collection', sanitize_text_field($params['author_collection']));
        update_post_meta($post_id, 'location_collection', sanitize_text_field($params['location_collection']));
        
        return new WP_REST_Response(array('message' => 'Collection created successfully', 'post_id' => $post_id), 200);
    } else {
        return new WP_Error('failed_to_create', 'Failed to create collection', array('status' => 500));
    }
}

//Read a CPT Collection
add_action('rest_api_init', function() {
    register_rest_route('collection-book/v1', '/collection/(?P<id>\d+)', array(
        'methods'  => 'GET',
        'callback' => 'get_collection',
        'permission_callback' => 'collections_permissions_callback',
    ));
});

function get_collection($request) {

    $post_id = $request['id'];
    $post = get_post($post_id);

    if ($post && $post->post_type == 'collection') {
        $response = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'description' => $post->post_content,
            'material_type' => get_post_meta($post->ID, 'material_type', true),
            'author_collection' => get_post_meta($post->ID, 'author_collection', true),
            'location_collection' => get_post_meta($post->ID, 'location_collection', true),
        );
        return new WP_REST_Response($response, 200);
    } else {
        return new WP_Error('collection_not_found', 'Collection not found', array('status' => 404));
    }
}

//Update a CPT Collection
add_action('rest_api_init', function() {
    register_rest_route('collection-book/v1', '/collections/(?P<id>\d+)', array(
        'methods'  => 'POST',
        'callback' => 'update_collection',
        'permission_callback' => 'collections_permissions_callback',
    ));
});

function update_collection($request) {
    $post_id = $request['id'];
    $params = $request->get_params();

    $updated_post = array(
        'ID'            => $post_id,
        'post_title'    => sanitize_text_field($params['title']),
        'post_content'  => sanitize_textarea_field($params['description']),
    );

    $result = wp_update_post($updated_post);

    if ($result) {
        // Atualizando os custom fields
        update_post_meta($post_id, 'material_type', sanitize_text_field($params['material_type']));
        update_post_meta($post_id, 'author_collection', sanitize_text_field($params['author_collection']));
        update_post_meta($post_id, 'location_collection', sanitize_text_field($params['location_collection']));
        
        return new WP_REST_Response(array('message' => 'Collection updated successfully'), 200);
    } else {
        return new WP_Error('failed_to_update', 'Failed to update collection', array('status' => 500));
    }
}

//Delete a CPT Collection
add_action('rest_api_init', function() {
    register_rest_route('collection-book/v1', '/collections/(?P<id>\d+)', array(
        'methods'  => 'DELETE',
        'callback' => 'delete_collection',
        'permission_callback' => 'collections_permissions_callback',
    ));
});

function delete_collection($request) {
    $post_id = $request['id'];

    $result = wp_delete_post($post_id, true);

    if ($result !== false) {
        // Deletando os custom fields
        delete_post_meta($post_id, 'material_type');
        delete_post_meta($post_id, 'author_collection');
        delete_post_meta($post_id, 'location_collection');
        
        return new WP_REST_Response(array('message' => 'Collection deleted successfully'), 200);
    } else {
        return new WP_Error('failed_to_delete', 'Failed to delete collection', array('status' => 500));
    }
}

function collections_permissions_callback($request){

     //Verify if cookie wp auth is here in request
     if (isset($_COOKIE[LOGGED_IN_COOKIE])) {
        return true;
    } 

    return false;
}

//List all Collections
add_action('rest_api_init', function() {
    register_rest_route('collection-book/v1', '/collections', array(
        'methods'  => 'GET',
        'callback' => 'get_all_collections',
        'permission_callback' => 'collections_permissions_callback'
    ));
});

function get_all_collections($request) {

    $material_type = $request->get_param('materialType');
    $author_collection = $request->get_param('authorCollection');
    $location_collection = $request->get_param('locationCollection');
    $title = $request->get_param('title');
    $description = $request->get_param('description');

    $args = array(
        'post_type' => 'collection',
        'posts_per_page' => -1, 
    );

    //Filter based params
    if ($material_type) {
        $args['meta_query'][] = array(
            'key' => 'material_type',
            'value' => $material_type,
            'compare' => 'LIKE'
        );
    }

    if ($author_collection) {
        $args['meta_query'][] = array(
            'key' => 'author_collection',
            'value' => $author_collection,
            'compare' => 'LIKE'
        );
    }

    if ($location_collection) {
        $args['meta_query'][] = array(
            'key' => 'location_collection',
            'value' => $location_collection,
            'compare' => 'LIKE'
        );
    }

    if ($title) {
        $args['s'] = $title; 
    }

    if ($description) {
        $args['s'] = $description; 
    }


    $collections = get_posts($args);

    $response = array();
    foreach ($collections as $collection) {
        $material_type = get_post_meta($collection->ID, 'material_type', true);
        $author_collection = get_post_meta($collection->ID, 'author_collection', true);
        $location_collection = get_post_meta($collection->ID, 'location_collection', true);

        $response[] = array(
            'id' => $collection->ID,
            'title' => $collection->post_title,
            'description' => $collection->post_content,
            'material_type' => $material_type,
            'author_collection' => $author_collection,
            'location_collection' => $location_collection,
            // Adicione outros custom fields aqui
        );
    }

    return new WP_REST_Response($response, 200);
    
}