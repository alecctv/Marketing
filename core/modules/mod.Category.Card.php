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
        <div id="postcard-main" class="main primary <?php echo $col_mod_num; ?>" role="main">
                <!-- 分类文章 -->
                <section class="category-posts loop-rows row">
                    <!-- Card -->
                <?php foreach ($category_posts as $category_post) { ?>
                        <div class="<?php echo $card_col_mod_num;?>">

                            <article id="<?php echo 'post-' . $category_post['ID']; ?>" class="post type-post status-publish wow bounceInUp">
                                <div class="entry-thumb hover-scale">
                                    <a href="<?php echo $category_post['permalink']; ?>">
                                        <img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $category_post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="" style="display: block;" />
                                    </a>
                                    <?php echo $category_post['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title h4">
                                            <a href="<?php echo $category_post['permalink']; ?>" rel="bookmark" target="_blank" title="<?php echo $category_post['title']; ?>"><?php echo $category_post['title']; ?></a>
                                        </h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="entry-date text-muted"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $category_post['time']; ?>"><?php echo $category_post['time']; ?></time></span>
                                            <span class="comments-link text-muted pull-right"><i class="tico tico-comments-o"></i><a href="<?php echo $category_post['permalink'] . '#respond'; ?>" target="_blank"><?php echo $category_post['comment_count']; ?></a></span>
                                            <span class="views-count text-muted pull-right mr10"><i class="tico tico-eye"></i><?php echo $category_post['views']; ?></span>
                                        </div>
                                    </header>
                                </div>
                            </article>

                        </div>
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