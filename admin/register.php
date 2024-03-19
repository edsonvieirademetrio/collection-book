<?php

//Admin page Collections
add_action('admin_menu', 'collection_admin_menu');

function collection_admin_menu() {
    add_menu_page(
        'Controle Básico de Acervo', 
        'Acervo', 
        'manage_options', 
        'collections-settings', 
        'collection_settings_page' 
    );
}


// Plugin Settings Page
function collection_settings_page() {
    // Check permissions user
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.'));
    }

    // Content
    echo '<div class="wrap">';
    require_once('template.php');
    echo '</div>';
}
