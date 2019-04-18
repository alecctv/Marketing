<?php
/**
 * Copyright (c) 2014-2018, yunsheji.cc
 * All right reserved.
 *
 * @since 1.1.0
 * @package Marketing
 * @author 云设计
 * @date 2018/02/14 10:00
 * @link https://yunsheji.cc
 */
?>
<?php
global $postdata;
$vm = PostCommentsVM::getInstance($postdata->ID);
if($vm->isCache && $vm->cacheTime) { ?>
    <!-- Comments cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $comment_list = $vm->modelData->list_html; ?>
<div id="comments-wrap">
    <ul class="comments-list">
        <input type="hidden" id="comment_star_nonce" name="tt_comment_star_nonce" value="<?php echo wp_create_nonce('tt_comment_star_nonce'); ?>">
        <?php echo $comment_list; ?>
        <div class="pages"><?php //paginate_comments_links('prev_text=«&next_text=»&type=list'); ?></div>
    </ul>
    <?php if($vm->modelData->list_count > 0){ ?>
    <div class="load-more"><button class="btn btn-primary btn-wide btn-more"><?php _e('Load More Comments', 'tt'); ?></button></div>
    <?php } ?>
    <div class="err text-primary text-center h3"></div>
</div>