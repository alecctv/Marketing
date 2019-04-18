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
<!-- 搜索模态框 -->
<div id="globalSearch" class="js-search search-form search-form-modal fadeZoomIn" role="dialog" aria-hidden="true">
    <form method="get" action="<?php echo home_url(); ?>" role="search">
        <div class="search-form-inner">
            <div class="search-form-box">
                <input class="form-search" type="text" name="s" placeholder="<?php _e('Type a keyword', 'tt'); ?>">
            </div>
        </div>
    </form>
</div>