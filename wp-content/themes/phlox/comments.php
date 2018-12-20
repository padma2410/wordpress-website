<?php
/**
 * Comment template
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
 */
 ?>

<?php
    // Do not delete these lines
    if ( ! empty($_SERVER['SCRIPT_FILENAME'] ) && 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) )
        die ('Please do not load this page directly. Thanks!');

    if ( post_password_required() ) { ?>
    <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'phlox' ); ?></p>
<?php
        return;
    }
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
    <div id="comments" class="aux-comments">
        
        <h3 class="comments-title">
            <?php comments_number( __('No Responses', 'phlox' ), __('One Response', 'phlox' ), __('% Responses', 'phlox' ) );?>
        </h3>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
        <nav class="aux-comments-navi comments-navi-primary">
            <div class="comments-pre-page"><?php previous_comments_link() ?></div>
            <div class="comment-next-page"><?php next_comments_link() ?></div>
        </nav>
        <?php endif; ?>

        <ol class="aux-commentlist skin-arrow-links">
            <?php wp_list_comments( array(
                'short_ping' => true,
                'callback'   => 'auxin_comment'
            ) ); ?>
        </ol>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
        <nav class="aux-comments-navi comments-navi-secondary">
            <div class="comments-pre-page"><?php previous_comments_link() ?></div>
            <div class="comment-next-page"><?php next_comments_link() ?></div>
        </nav>
        <?php endif; ?>

    </div>

    <div class="clear"></div>

<?php else : // this is displayed if there are no comments so far ?>

    <?php if ( comments_open() ) : ?>
    <!-- If comments are open, but there are no comments. -->

    <?php elseif( get_post_type() == "post" || get_post_type() == "news" ) : // comments are closed ?>
    <!-- If comments are closed. -->
    <p class="nocomments"><?php _e("Comments are closed.", 'phlox' ); ?></p>

    <?php endif; ?>

<?php endif; ?>


<?php

/**
 * Since WorePress 4.4 comment textarea section moves to top
 * this function moves it back to the bottom in comment fields
 *
 */
function auxin_move_comment_field_to_bottom( $fields ) {
    $comment_field     = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;

    return $fields;
}
add_filter( 'comment_form_fields', 'auxin_move_comment_field_to_bottom' );

$req           = get_option( 'require_name_email' );
$comments_args = array(
    
    'must_log_in'          => '<p>'. sprintf( __("You must be %s logged in %s to post a comment", 'phlox' ), '<a href="'.wp_login_url( get_permalink() ).'">', '</a>' ) .'</p>',
    'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'phlox' ), self_admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
    // change the title of send button
    'label_submit'         => __('Submit' , 'phlox' ) ,
    // change the title of the reply section
    'title_reply'          =>'<span>' . esc_html__('Leave a Comment', 'phlox' ) . '</span>',
    // remove "Text or HTML to be displayed after the set of comment fields"
    'comment_notes_before' => '',
    'comment_notes_after'  => '',
    // redefine your own textarea (the comment body)
    'comment_field'        => '<textarea name="comment" id="comment" cols="58" rows="10" placeholder="'. esc_attr__('Comment' , 'phlox' ). '" ></textarea>',
    'fields'               => apply_filters( 'comment_form_default_fields',
        array(
        'author' => '<input type="text"  name="author" id="author" placeholder="'. esc_attr__('Name (required)'  , 'phlox' ) . '" value="'. esc_attr( $comment_author). '" size="22" '. ( $req ? "aria-required='true' required" : "" ) .' />',
        'email'  => '<input type="email" name="email"  id="email"  placeholder="'. esc_attr__('E-Mail (required)', 'phlox' ) . '" value="'. esc_attr( $comment_author_email). '" ' . ( $req ? "aria-required='true' required" : "" ) .' />',
        'url'    => '<input type="url"   name="url"    id="url"    placeholder="'. esc_attr__('Website'          , 'phlox' ) . '" value="'. esc_url( $comment_author_url). '" size="22" />'
        )
    )
);

comment_form( apply_filters( 'auxin_default_comment_form', $comments_args ) );
