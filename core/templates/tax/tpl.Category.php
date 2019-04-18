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
<?php
    $cat_id = get_queried_object_id();
    $alt_tpl_cats = tt_get_option('tt_alt_template_cats', array());
    if (isset($alt_tpl_cats[$cat_id]) && $alt_tpl_cats[$cat_id]) {
        load_mod('mod.Category.List');
    } else {
        load_mod('mod.Category.Card');
    }
?>
<?php tt_get_footer(); ?>