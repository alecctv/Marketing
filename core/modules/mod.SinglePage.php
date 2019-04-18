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
<div id="main" class="main primary col-md-9 post-box wow bounceInUp" role="main">
    <?php global $post; $vm = SinglePageVM::getInstance($post->ID); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Page cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php global $postdata; $postdata = $vm->modelData; ?>
    <div class="post page">
        <div class="single-header">
            <div class="header-wrap">
                <h1 class="h2"><?php echo $postdata->title; ?></h1>
                <div class="header-meta">
                    <span class="meta-date"><?php _e('Post on: ', 'tt'); ?><time class="entry-date" datetime="<?php echo $postdata->datetime; ?>" title="<?php echo $postdata->datetime; ?>"><?php echo $postdata->timediff; ?></time></span>
                    <span class="separator" role="separator"> · </span>
                    <span class="meta-date"><?php _e('Modified on: ', 'tt'); ?><time class="entry-date" datetime="<?php echo $postdata->modified; ?>" title="<?php echo $postdata->modified; ?>"><?php echo $postdata->modifieddiff; ?></time></span>
                </div>
            </div>
        </div>
        <div class="single-body">
            <article class="single-article"><?php echo $postdata->content; apply_filters('the_content', 'content'); // 一些插件(如crayon-syntax-highlighter)将非内容性的钩子(wp_enqueue_script等)挂载在the_content上, 缓存命中时将失效 ?></article>
            <div class="article-footer">
                <div class="support-author"></div>
                <div class="post-like">
                    <a class="post-meta-likes js-article-like <?php if(in_array(get_current_user_id(), $postdata->star_uids)) echo 'active'; ?>" href="javascript: void(0)" data-post-id="<?php echo $postdata->ID; ?>" data-nonce="<?php echo wp_create_nonce('tt_post_star_nonce'); ?>"><i class="tico tico-favorite"></i><span class="text"><?php in_array(get_current_user_id(), $postdata->star_uids) ? _e('Stared', 'tt') : _e('Star It', 'tt'); ?></span></a>
                    <ul class="post-like-avatars">
                        <?php foreach ($postdata->star_users as $star_user) { ?>
                            <li class="post-like-user"><img src="<?php echo $star_user->avatar; ?>" alt="<?php echo $star_user->name; ?>" title="<?php echo $star_user->name; ?>" data-user-id="<?php echo $star_user->uid; ?>"></li>
                        <?php } ?>
                        <li class="post-like-counter"><span><span class="js-article-like-count num"><?php echo $postdata->stars; ?></span> <?php _e('persons', 'tt'); ?></span><?php _e('Stared', 'tt'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- 评论 -->
        <?php if($postdata->comment_status) { ?>
        <div id="respond">
            <h3><?php _e('LEAVE A REPLY', 'tt'); ?></h3>
                <?php load_mod( 'mod.ReplyForm', true ); ?>
                <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
            </div>
      <?php } ?>
    </div>
</div>