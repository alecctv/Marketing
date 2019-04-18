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
<?php $paged = get_query_var('paged') ? : 1; ?>
<div id="content" class="wrapper">
    <?php $vm = CategoryPostsVM::getInstance($paged); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Category posts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $category = $data->category; $category_posts = $data->category_posts; ?>
    
    <!-- 判断布局 -->
    <?php 
    if(tt_get_option('post_template_cats_is_sidebar', true)) { 
        $col_mod_num = 'col-md-9';
        $card_col_mod_num = 'col-md-4 col-sm-6 col-xs-6';
    }else{
        $col_mod_num = 'col-md-12';
        $card_col_mod_num = 'col-md-3 col-sm-4 col-xs-6';
    }
    ?>

    <section id="mod-insideContent" class="main-wrap container content-section clearfix">
    <?php if (tt_get_option('tt_enable_k_fbsbt', true)) { ?>
    <!-- 分类名及介绍信息 -->
        <div class="catga-section-info">
            <h2 class="postmodettitle"><?php echo $category['cat_name']; ?></h2>
            <?php if($category['description'] != ''){ ?><div class="postmode-description"><p><?php echo $category['description']; ?></p></div><?php } ?>
        </div>
    <?php } ?>
        <!-- 分类文章列表 -->
        <div id="postlist-main" class="main primary <?php echo $col_mod_num; ?>" role="main">
                <!-- 分类文章 -->
                <section class="category-posts loop-rows row">
                    <!-- List -->
                    <?php foreach ($category_posts as $category_post) { ?>
                        <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="excerpt post type-post status-publish wow bounceInUp">

                            <a href="<?php echo $category_post['permalink']; ?>" class="focus" ><img width="200" height="auto" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $category_post['thumb']; ?>" class="thumb thumb-medium wp-post-image lazy" alt="<?php echo $category_post['title']; ?>"></a>
                            <h2><a href="<?php echo $category_post['permalink']; ?>" title="<?php echo $category_post['title']; ?>"><?php echo $category_post['title']; ?></a></h2>

                            <div class="note"><?php echo $category_post['excerpt']; ?></div>
                            <div class="meta">
                                <time><i class="tico tico-alarm"></i> <?php echo $category_post['time']; ?></time>
                                <span><?php echo $category_post['category']; ?> </span>
                                <span><i class="tico tico-eye"></i> <?php echo $category_post['views']; ?></span>
                                <span><i class="tico tico-comments-o"></i> <a href="<?php echo $category_post['permalink'] . '#respond'; ?>"><?php echo $category_post['comment_count']; ?></a></span>
                            </div>
                        </article>
                    <?php } ?>
                </section>

                <?php if($pagination_args['max_num_pages'] > 1) { ?>
                    <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
                <?php } ?>
        </div>
        <!-- is_sidebar -->
        <?php if (tt_get_option('post_template_cats_is_sidebar', true)) { load_mod('mod.Sidebar'); } ?>

    </section>
    <?php } ?>
</div>