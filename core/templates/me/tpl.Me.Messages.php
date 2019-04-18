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
    <!-- 主要内容区 -->
    <section class="container user-area">
        <div class="inner row">
            <?php load_mod('me/me.NavMenu'); ?>
            <?php load_mod('me/me.Tab.Messages'); ?>
        </div>
    </section>
</div>
<?php tt_get_footer(); ?>