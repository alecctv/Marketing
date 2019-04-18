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

<?php if(tt_get_option('post_item_style', 'style_0') == 'style_0') { ?>

    <?php load_mod('mod.HomeLatestList'); ?>

<?php } else { ?>

    <?php load_mod('mod.HomeLatestCard'); ?>

<?php } ?>
