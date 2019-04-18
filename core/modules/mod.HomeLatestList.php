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
    }else{
        $col_mod_num = 'col-md-12';
    }
?>
<!-- 列表风格 -->
<div id="postlist-main" class="main primary <?php echo $col_mod_num; ?>" role="main">
    <?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>
    <?php $vm = HomeLatestVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Latest posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <div id="latest-posts" class="block5">
        <aside class="block5-widget">
            <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $latest_posts = $data->latest_posts; ?>
            <div class="block5_widget_content block5_list loop-rows posts-loop-rows">
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
                <article id="<?php echo 'post-' . $latest_post['ID']; ?>" class="excerpt post type-post status-publish wow bounceInUp">

                    <a href="<?php echo $latest_post['permalink']; ?>" class="focus" ><img width="200" height="auto" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $latest_post['thumb']; ?>" class="thumb thumb-medium wp-post-image lazy" alt="<?php echo $latest_post['title']; ?>"></a>
                    <h2><a href="<?php echo $latest_post['permalink']; ?>" title="<?php echo $latest_post['title']; ?>"><?php echo $latest_post['title']; ?></a></h2>

                    <div class="note"><?php echo $latest_post['excerpt']; ?></div>
                    <div class="meta">
                        <time><i class="tico tico-alarm"></i> <?php echo $latest_post['time']; ?></time>
                        <span><?php echo $latest_post['category']; ?> </span>
                        <span><i class="tico tico-eye"></i> <?php echo $latest_post['views']; ?></span>
                        <span><i class="tico tico-comments-o"></i> <a href="<?php echo $latest_post['permalink'] . '#respond'; ?>"><?php echo $latest_post['comment_count']; ?></a></span>
                    </div>
                </article>
                <?php } ?>
            </div>

            <?php if($pagination_args['max_num_pages'] > 1) { ?>
            <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
            <?php } ?>
            <?php } ?>
        </aside>
    </div>
</div>