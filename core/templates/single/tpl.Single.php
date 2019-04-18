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
<div id="content" class="wrapper container right-aside">
    <?php load_mod(('banners/bn.Top')); ?>
    <section id="mod-insideContent" class="main-wrap content-section clearfix">
        <!-- 文章 -->
        <?php load_mod('mod.SinglePost'); ?>
        <!-- 边栏 -->
        <?php load_mod('mod.Sidebar'); ?>
    </section>
    <?php load_mod(('banners/bn.Bottom')); ?>
</div>
<?php tt_get_footer(); ?>