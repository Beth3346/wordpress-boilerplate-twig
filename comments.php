<?php
    use Framework\Helpers\Comments;

    function customThemeComment($comment, $args, $depth) {
        $comments = new Comments;
        $comments->customThemeComment($comment, $args, $depth);
    }
?>

<?php if (post_password_required()) : ?>
    <p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'elr'); ?></p>
<?php
        /* Stop the rest of comments.php from being processed,
         * but don't kill the script entirely -- we still have
         * to fully load the template.
         */
        return;
    endif;
?>

<?php
    // You can start editing here -- including this comment!
?>

<?php if (have_comments() || comments_open()) : ?>
<div id="comments" class="commentwrap comments">
<?php endif; // end commentwrap ?>

<?php if (have_comments()) : ?>
    <h4 class="comment-title"><?php comments_number(__('No Comments','elr'), __('One Comment','elr'), __('% Comments','elr'));?></h4>

    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? ?>
        <div class="pagenav top">
            <?php paginate_comments_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;'));?>
        </div>
        <!-- /.pagenav -->
    <?php endif; // check for comment navigation ?>

    <ul class="commentlist elr-unstyled-list">
        <?php
            wp_list_comments('callback=customThemeComment');
        ?>
    </ul>

    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through? ?>
        <div class="pagenav bottom">
            <?php paginate_comments_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;'));?>
        </div>
        <!-- /.pagenav -->
    <?php endif; // check for comment navigation ?>

<?php else : // or, if we don't have comments:

    /* If there are no comments and comments are closed,
     * let's leave a little note, shall we?
     */
    if (!comments_open()) :
?>

<?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>

<?php

if (!isset($post_id)) {
    $post_id = NULL;
}

$custom_comment_form = ['fields' => apply_filters('comment_form_default_fields', [
    'author' => '<p class="comment-form-author">' .
            ($req ? '<span class="required">* </span>' : ' ') .
            '<label for="author">' . __('Your Name: ' , 'elr') . '</label><br>' .
            '<input id="author" name="author" type="text" value="' .
            esc_attr($commenter['comment_author']) . '" size="30"' . ' class="required" />' .
            '</p>',
    'email'  => '<p class="comment-form-email">' .
            ($req ? '<span class="required">* </span>' : '') .
            '<label for="email">' . __('Your Email: ' , 'elr') . '</label><br>' .
            '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email']) . '" size="30"' . ' class="required email" />' .
            '</p>']),
    'comment_field' => '<p class="comment-form-comment">' .
            '<label for="comment">' . __('Comments: ' , 'elr') . '</label><br>' .
            '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="required"></textarea>' .
            '</p>',
    'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s">Log out?</a>'), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_id)))) . '</p>',
    'title_reply' => __('Leave a Reply' , 'elr'),
    'comment_notes_before' => '',
    'comment_notes_after' => '',
    'cancel_reply_link' => __('Cancel' , 'elr'),
    'label_submit' => __('Post Comment' , 'elr'),
];

comment_form($custom_comment_form);
?>

<?php if (have_comments() || comments_open()) : ?>
</div>
<!-- /.commentwrap -->
<?php endif; // end commentwrap ?>