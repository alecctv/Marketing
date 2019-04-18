<?php
/**
 * Default Page Template
 *
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
        <section id="mod-insideContent" class="main-wrap content-section clearfix">
            <!-- 页面 -->
            <?php load_mod('mod.SinglePage'); ?>
            <!-- 边栏 -->
            <?php load_mod('mod.Sidebar'); ?>
        </section>
    </div>
<?php tt_get_footer(); ?>