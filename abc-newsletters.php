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
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'author', ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-media-document',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'news-events/the-ambassador-newsletter/all',
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
            <p class="filesize">Size: ' . size_format( filesize( get_attached_file( $PDF['id'] ) ) ) . '</p>
            <p>' . $PDF['description'] . '</p>
        </section>' . $content;
    }
    return $content;
}
add_filter( 'the_content', 'newsletter_add_pdf_link' );

// Modify the page title
function filter_newsletter_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'newsletter' ) ) {
          $title = '&ldquo;The Ambassador&rdquo; Newsletter';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_newsletter_page_title' );

// Add shortcode for most recent newsletter
function newsletter_shortcode() {
// WP_Query arguments
    $args = array (
        'post_type'              => array( 'newsletter' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => '1',
        'cache_results'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    // The Query
    $next_newsletter_query = new WP_Query( $args );

    $shortcode_content = '<h1>Last Newsletter</h1>
        <p>See <a href="all/">previous newsletters here</a>.</p>';

    // The Loop
    if ( $next_newsletter_query->have_posts() ) {
        while ( $next_newsletter_query->have_posts() ) {
            $next_newsletter_query->the_post();
            ob_start(); ?>
            <article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> newsletter type-newsletter status-publish has-post-thumbnail hentry">
                <header class="entry-header">

                    <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>	</header><!-- .entry-header -->
            <?php if ( has_post_thumbnail() ) { ?>
                <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
                <?php the_post_thumbnail(); ?>
                </a>
            <?php } ?>
            <div class="entry-content">
            <?php the_content(); ?>
                </div>
                <footer class="entry-footer">
                    <span class="byline"><span class="author vcard">
                       <?php $author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
                        printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
                            get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
                            _x( 'Author', 'Used before post author name.', 'twentysixteen' ),
                            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                            get_the_author()
                        );?>
                        </span>
                    </span>
                    <?php twentysixteen_entry_date(); ?>
                </footer><!-- .entry-footer -->
            </article>
            <?php $shortcode_content .= ob_get_clean();
        }
    }

    // Restore original Post Data
    wp_reset_postdata();

    return $shortcode_content;
}
add_shortcode( 'last_newsletter', 'newsletter_shortcode' );
