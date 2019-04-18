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
<?php tt_get_header(); ?>
<?php $paged = get_query_var('paged'); if((!$paged || $paged===1) ) : ?>
<?php load_mod(('banners/bn.Top')); ?>
    
    <!-- /.hero -->
    <section id="home-hero">
        <?php load_mod('mod.HomeHero'); ?>
    </section>
    

<!-- home-information./ -->
<?php load_mod('mod.HomeBulletins'); ?>

<!--shop List item./-->
<?php if(tt_get_option('tt_home_products_recommendation', false)) { ?>
<section id="home-postmode" class="wrapper container">
    <div class="section-info">
        <h2 class="postmodettitle"><?php echo tt_get_option('tt_home_products_title');?></h2>
        <div class="postmode-description"><?php echo tt_get_option('tt_home_products_desc');?></div>
    </div>
    
    <?php load_mod('mod.ProductGallery', true); ?>

</section>

<?php } ?>

<!-- home features./ -->
<?php if(tt_get_option('tt_home_features', false)) { ?>
<section id="home-features" class="wrapper widget_woothemes_features"> 
    <div class="features container">
        <div class="feature first">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_1');?>" title="主题定制"><?php echo tt_get_option('feature_title_1');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_1');?></div>
        </div>
        <div class="feature">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_2');?>" title="主题定制"><?php echo tt_get_option('feature_title_2');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_2');?></div>
        </div>
        <div class="feature last">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_3');?>" title="主题定制"><?php echo tt_get_option('feature_title_3');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_3');?></div>
        </div>
        <div class="fix"></div>
        <div class="feature first">
           <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_4');?>" title="主题定制"><?php echo tt_get_option('feature_title_4');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_4');?></div>
        </div>
        <div class="feature">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_5');?>" title="主题定制"><?php echo tt_get_option('feature_title_5');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_5');?></div>
        </div>
        <div class="feature last">
            <h3 class="feature-title"><a href="<?php echo tt_get_option('feature_href_6');?>" title="主题定制"><?php echo tt_get_option('feature_title_6');?></a></h3>
            <div class="feature-content"><?php echo tt_get_option('feature_desc_6');?></div>
        </div>
        <div class="fix"></div>
    </div>
</section>

<?php } ?>

<?php endif; ?> 

<!-- 判断首页end -->

<div id="content" class="wrapper container right-aside">
    <?php $paged = get_query_var('paged'); if((!$paged || $paged===1) ) : ?>
    <div class="section-info">
        <h2 class="postmodettitle"><?php echo tt_get_option('tt_home_postlist_title');?></h2>
        <div class="postmode-description"><?php echo tt_get_option('tt_home_postlist_desc');?></div>
    </div>
    <?php endif; ?> 
    <!-- 近期文章与边栏 -->
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 近期文章列表 -->
        <?php load_mod('mod.HomeLatest'); ?>
        <?php if(tt_get_option('post_item_is_sidebar', true)) { ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
        <?php } ?>
    </section>
    <?php load_mod(('banners/bn.Bottom')); ?>
</div>



<?php tt_get_footer(); ?>