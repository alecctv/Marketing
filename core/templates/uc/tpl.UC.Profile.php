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
<div id="content" class="wrapper">
    <?php load_mod('uc/uc.TopPane'); ?>
    <!-- 主要内容区 -->
    <section class="container author-area">
        <div class="inner">
            <?php load_mod('uc/uc.NavTabs'); ?>
            <?php load_mod('uc/uc.Tab.Profile'); ?>
        </div>
    </section>
</div>
<?php tt_get_footer(); ?>