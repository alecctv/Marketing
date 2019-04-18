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

//if(!is_user_logged_in()){
//    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
//}

if(!isset($_GET['_'])){
    wp_die(__('The required resource id is missing', 'tt'), __('Invalid Resource ID', 'tt'), 404);
}

$post_id = (int)tt_decrypt(trim($_GET['_']), tt_get_option('tt_private_token'));

global $origin_post;
$origin_post = get_post($post_id);

if(!$origin_post){
    wp_die(__('The resource id is invalid or resource is not exist', 'tt'), __('Invalid Resource ID', 'tt'), 404);
}

?>
<?php tt_get_header(); ?>
<div id="content" class="wrapper container download-wrapper">
    <?php load_mod(('banners/bn.Top')); ?>
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 下载页面 -->
        <?php load_mod('mod.Download'); ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
    </section>
</div>
<?php tt_get_footer(); ?>