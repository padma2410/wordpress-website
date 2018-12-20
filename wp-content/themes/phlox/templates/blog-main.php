<?php
/**
 * The main template for blog 'page templates'
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
 */

get_header();?>

    <main id="main" <?php auxin_content_main_class(); ?> >
        <div class="aux-wrapper">
            <div class="aux-container aux-fold clearfix">

                <div id="primary" class="aux-primary" >
                    <div class="content" role="main" data-target="archive"  >

                    <?php
                    echo auxin_get_the_archive_slider( 'post', 'content' );

                    $content = get_the_content();
                    if( ! empty( $content ) ){
                    ?>
                        <article <?php post_class(); ?> >
                            <div class="entry-main">
                                <div class="entry-content">
                                    <?php
                                        echo $content;
                                        // clear the floated elements at the end of content
                                        echo '<div class="clear"></div>';
                                    ?>
                                </div>
                            </div>
                        </article>
                    <?php
                    }

                    // get template slug
                    $page_template = get_page_template_slug( get_queried_object_id() );

                    // Let the auxin plugins add more custom layouts to current blog templates
                    $result = apply_filters( 'auxin_blog_page_template_archive_content', '', $page_template );

                    if( empty( $result ) ) {

                        $q_args = array(
                            'post_type'     => 'post',
                            'order_by'      => 'date',
                            'order'         => 'DESC',
                            'post_status'   => 'publish',
                            'posts_per_page'=> get_option('posts_per_page'),
                            'paged'         => max( 1, get_query_var('paged'), get_query_var('page') ) // 'paged' for archive pages and 'page' for single pages
                        );

                        $the_wp_query = new WP_Query( $q_args );

                        $result = $the_wp_query->have_posts();
                    }

                    // if it is not a shortcode base blog page
                    if( true === $result ){

                        while ( $the_wp_query->have_posts() ) : $the_wp_query->the_post();
                            include locate_template( 'templates/theme-parts/entry/post.php' );
                        endwhile; // end of the loop.

                    // if it is a shortcode base blog page
                    } elseif( false !== $result && '' !== $result ){
                        echo $result;

                    // if result not found
                    } else {
                        include locate_template( 'templates/theme-parts/content-none.php' );
                    }

                    // generate the archive pagination
                    auxin_the_paginate_nav(
                        array( 'css_class' => esc_attr( auxin_get_option('archive_pagination_skin') ) )
                    );

                    // reset the post data
                    wp_reset_postdata(); ?>

                    </div><!-- end content -->
                </div><!-- end primary -->

                <?php get_sidebar(); ?>

            </div><!-- end container -->
        </div><!-- end wrapper -->
    </main><!-- end main -->

<?php get_sidebar('footer'); ?>
<?php get_footer(); ?>
