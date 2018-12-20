<?php
/**
 * The Template for displaying all single posts
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
*/
$is_pass_protected = post_password_required();

get_header(); ?>

    <main id="main" <?php auxin_content_main_class(); ?> >
        <div class="aux-wrapper">
            <div class="aux-container aux-fold">

                <div id="primary" class="aux-primary" >
                    <div class="content" role="main"  >

                        <?php if ( have_posts() && ! $is_pass_protected ) : ?>

                            <?php get_template_part('templates/theme-parts/single', get_post_type() ); ?>

                            <?php if( get_post_type() != "pricetable" )
                                    comments_template( '/comments.php', true ); ?>

                        <?php elseif( $is_pass_protected ) : ?>

                            <?php echo get_the_password_form(); ?>

                        <?php else : ?>

                            <?php get_template_part('templates/theme-parts/content', 'none' ); ?>

                        <?php endif; ?>

                    </div><!-- end content -->
                </div><!-- end primary -->


                <?php get_sidebar(); ?>


            </div><!-- end container -->
        </div><!-- end wrapper -->
    </main><!-- end main -->

<?php get_sidebar('footer'); ?>
<?php get_footer(); ?>
