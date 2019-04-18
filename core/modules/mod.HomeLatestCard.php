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
    if(tt_get_option('post_item_is_sidebar', true)) { 
        $col_mod_num = 'col-md-9';
        $card_col_mod_num = 'col-md-4 col-sm-6 col-xs-6';
    }else{
        $col_mod_num = 'col-md-12';
        $card_col_mod_num = 'col-md-3 col-sm-4 col-xs-6';
    }
?>
<!-- 卡片风格 -->  
<div id="postcard-main" class="main primary <?php echo $col_mod_num; ?>" role="main">
    <?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>
    <?php $vm = HomeLatestVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Latest posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <div id="latest-posts" class="block5">
        <aside class="block5-widget">
            <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $latest_posts = $data->latest_posts; ?>
            <div class="block5_widget_content block5_list loop-rows posts-loop-rows row">
                <?php if($paged === 1) { ?>
                <?php $sticky_vm = StickysVM::getInstance(); ?>
                    <?php if($sticky_vm->isCache && $sticky_vm->cacheTime) { ?>
                        <!-- Sticky posts cached <?php echo $sticky_vm->cacheTime; ?> -->
                    <?php } ?>
                    <?php if($sticky_data = $sticky_vm->modelData) {
                        $sticky_posts = $sticky_data->sticky_posts; $sticky_count = $sticky_data->count;
                        $latest_posts = $sticky_count > 0 && $sticky_posts ? array_merge($sticky_posts, $latest_posts) : $latest_posts;
                    } ?>
                <?php } ?>
                <?php foreach ($latest_posts as $latest_post) { ?>
                <div class="<?php echo $card_col_mod_num;?>">

                    <article id="<?php echo 'post-' . $latest_post['ID']; ?>" class="post type-post status-publish wow bounceInUp">
                        <div class="entry-thumb hover-scale">
                            <a href="<?php echo $latest_post['permalink']; ?>">
                                <img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $latest_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="" style="display: block;" />
                            </a>
                            <?php echo $latest_post['category']; ?>
                        </div>
                        <div class="entry-detail">
                            <header class="entry-header">
                                <h2 class="entry-title h4">
                                    <a href="<?php echo $latest_post['permalink']; ?>" rel="bookmark" target="_blank" title="<?php echo $latest_post['title']; ?>"><?php echo $latest_post['title']; ?></a>
                                </h2>
                                <div class="entry-meta entry-meta-1">
                                    <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $latest_post['time']; ?>"><?php echo $latest_post['time']; ?></time></span>
                                    <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $latest_post['permalink'] . '#respond'; ?>" target="_blank"><?php echo $latest_post['comment_count']; ?></a></span>
                                    <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $latest_post['views']; ?></span>
                                </div>
                            </header>
                        </div>
                    </article>

                </div>
                <?php } ?>
            </div>

            <?php if($pagination_args['max_num_pages'] > 1) { ?>
            <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
            <?php } ?>
            <?php } ?>
        </aside>
    </div>
</div>