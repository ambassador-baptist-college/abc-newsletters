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

// Add PDF links
function newsletter_add_pdf_link( $content ) {
    global $post;

    foreach ( get_the_category( $post->ID ) as $category ) {
        if ( 'newsletter' == $category->slug && get_field( 'pdf' ) ) {
            $PDF = get_field( 'pdf' );

            $content = '<section class="download dashicons-before dashicons-media-document">
                <h2><a href="' . $PDF['url'] . '">Download the PDF version</a></h2>
                <p class="filesize">Size: ' . size_format( filesize( get_attached_file( $PDF['id'] ) ) ) . '</p>
                <p>' . $PDF['description'] . '</p>
            </section>' . $content;
        }
    }

    return $content;
}
add_filter( 'the_content', 'newsletter_add_pdf_link' );
add_filter( 'get_the_excerpt', 'newsletter_add_pdf_link' );

// Add shortcode for most recent newsletter
function newsletter_shortcode() {
// WP_Query arguments
    $args = array (
        'category_name'          => 'newsletter',
        'posts_per_page'         => '1',
    );

    // The Query
    $next_newsletter_query = new WP_Query( $args );

    $shortcode_content = '<h1>Last Newsletter</h1>
        <p>See <a href="' . home_url( '/category/newsletter/' ) . '">previous newsletters here</a>.</p>';

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
