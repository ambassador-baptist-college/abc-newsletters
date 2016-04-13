<?php
/*
 * Plugin Name: ABC Newsletter
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-newsletter/
 * Description: The Ambassador Newsletter
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-newsletter/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function newsletter_post_type() {

    $labels = array(
        'name'                  => 'Newsletters',
        'singular_name'         => 'Newsletter',
        'menu_name'             => 'Newsletters',
        'name_admin_bar'        => 'Newsletter',
        'archives'              => 'Newsletter Archives',
        'parent_item_colon'     => 'Parent Newsletter:',
        'all_items'             => 'All Newsletters',
        'add_new_item'          => 'Add New Newsletter',
        'add_new'               => 'Add New',
        'new_item'              => 'New Newsletter',
        'edit_item'             => 'Edit Newsletter',
        'update_item'           => 'Update Newsletter',
        'view_item'             => 'View Newsletter',
        'search_items'          => 'Search Newsletter',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into newsletter',
        'uploaded_to_this_item' => 'Uploaded to this newsletter',
        'items_list'            => 'Newsletters list',
        'items_list_navigation' => 'Newsletters list navigation',
        'filter_items_list'     => 'Filter newsletters list',
    );
    $rewrite = array(
        'slug'                  => 'news-events/the-ambassador-newsletter',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Newsletter',
        'description'           => 'The Ambassador',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-media-document',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'news-events/the-ambassador-newsletter',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'newsletter', $args );

}
add_action( 'init', 'newsletter_post_type', 0 );

// Add PDF links
function newsletter_add_pdf_link( $content ) {
    if ( 'newsletter' == get_post_type() && get_field( 'pdf' ) ) {
        $PDF = get_field( 'pdf' );

        $content = '<section class="download dashicons-before dashicons-media-document">
            <h2><a href="' . $PDF['url'] . '">Download the PDF version</a></h2>
            <p>' . $PDF['description'] . '</p>
        </section>' . $content;
    }
    return $content;
}
add_filter( 'the_content', 'newsletter_add_pdf_link' );

// Modify the page title
function filter_newsletter_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'faculty' ) ) {
          $title = '&ldquo;The Ambassador&rdquo; Newsletter';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_newsletter_page_title' );
