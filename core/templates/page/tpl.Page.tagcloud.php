<?php
/**
 * Template Name: 标签云
 * Copyright (c) 2014-2018, yunsheji.cc
 * All right reserved.
 *
 * @since 1.1.0
 * @package Marketing
 * @author 云设计
 * @date 2018/02/14 10:00
 * @link https://yunsheji.cc
 */
$product_args=array( 'order'=> 'DESC', 'orderby' => 'count', 'taxonomy' => 'product_tag', ); $product_tags = get_terms($product_args);//产品标签
$post_args=array( 'order'=> 'DESC', 'orderby' => 'count', 'taxonomy' => 'post_tag', ); $post_tags = get_terms($post_args);//文章标签
?>
<?php tt_get_header(); ?>
    <div id="content" class="wrapper container full-page">
        <section id="mod-insideContent" class="main-wrap content-section clearfix">
            <!-- 页面 -->
            <div id="main" class="main primary post-box" role="main">
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
        <article class="single-article">
        <?php if ($product_tags){ ?>
        <section>
            <section class="home_title">
                <h3>产品标签</h3>
            </section>
            <section class="tagcloud">
                <?php foreach($product_tags as $product_tag) { ?>
                <a href="<?php echo get_tag_link($product_tag); ?>" title="<?php printf( '标签 %s 下有 %s 篇产品', esc_attr($product_tag->name), esc_attr($product_tag->count)); ?>">
                    <?php echo $product_tag->name; ?><span>(<?php echo $product_tag->count; ?>)</span></a>
                <?php } ?>
            </section>
        </section>
        <?php } ?>
        <section>
            <section class="home_title">
                <h3>文章标签</h3>
            </section>
            <section class="tagcloud">
                <?php if ($post_tags) { foreach($post_tags as $post_tag) { ?>
                <a href="<?php echo get_tag_link($post_tag); ?>" title="<?php printf('标签 %s 下有 %s 篇文章', esc_attr($post_tag->name), esc_attr($post_tag->count)); ?>">
                    <?php echo $post_tag->name; ?><span>(<?php echo $post_tag->count; ?>)</span></a>
                <?php } } ?>
            </section>
        </section>
          </article>
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
                    <div id="respond">
                        <?php if($postdata->comment_status) { ?>
                            <h3><?php _e('LEAVE A REPLY', 'tt'); ?></h3>
                            <?php load_mod( 'mod.ReplyForm', true ); ?>
                            <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
                        <?php }else{ ?>
                            <h3><?php _e('COMMENTS CLOSED', 'tt'); ?></h3>
                            <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php tt_get_footer(); ?>