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
<?php global $tt_vars; $data = $tt_vars['data']; ?>
<div id="main" class="main primary col-md-9 search-results" role="main">
    <?php if($data->count > 0) { $search_results = $data->results; $max_pages = $data->max_pages; ?>
    
    <div class="loop-rows posts-loop-row clearfix">
        <!-- List -->
        <?php foreach ($search_results as $search_result) { ?>
            <article id="<?php echo 'post-' . $search_result['ID']; ?>" class="excerpt post type-post status-publish wow bounceInUp">

                <a href="<?php echo $search_result['permalink']; ?>" class="focus" ><img width="200" height="auto" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $search_result['thumb']; ?>" class="thumb thumb-medium wp-post-image lazy" alt="<?php echo $search_result['title']; ?>"></a>
                <h2><a href="<?php echo $search_result['permalink']; ?>" title="<?php echo $search_result['title']; ?>"><?php echo $search_result['title']; ?></a></h2>

                <div class="note"><?php echo $search_result['excerpt']; ?></div>
                <div class="meta">
                    <time><i class="tico tico-alarm"></i> <?php echo $search_result['time']; ?></time>
                    <span><?php echo $search_result['category']; ?> </span>
                    <span><i class="tico tico-eye"></i> <?php echo $search_result['views']; ?></span>
                    <span><i class="tico tico-comments-o"></i> <a href="<?php echo $search_result['permalink'] . '#respond'; ?>"><?php echo $search_result['comment_count']; ?></a></span>
                </div>
            </article>
        <?php } ?>
    </div>
    
    <?php if($max_pages > 1) { ?>
        <div class="pagination-mini clearfix">
            <?php if($tt_vars['page'] == 1) { ?>
                <div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
            <?php }else{ ?>
                <div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
            <?php } ?>
            <div class="col-md-6 page-nums">
                <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_vars['page']); ?></span>
                <span class="separator">/</span>
                <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
            </div>
            <?php if($tt_vars['page'] != $data->max_pages) { ?>
                <div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
            <?php }else{ ?>
                <div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php }else{ ?>
    <div class="empty-results">
        <span class="tico tico-dropbox"></span>
        <p><?php _e('No results matched your search words', 'tt'); ?></p>
    </div>
    <?php } ?>
</div>