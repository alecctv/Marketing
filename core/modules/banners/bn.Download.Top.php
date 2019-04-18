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
<?php if(tt_get_option('tt_enable_dl_top_banner', false)) { ?>
    <section class="ttgg row" id="ttgg-10">
        <div class="tg-inner col-md-6">
            <?php echo tt_get_option('tt_dl_top_banner_1'); ?>
        </div>
        <div class="tg-inner col-md-6">
            <?php echo tt_get_option('tt_dl_top_banner_2'); ?>
        </div>
    </section>
<?php } ?>