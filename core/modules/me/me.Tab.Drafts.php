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
<?php global $tt_me_vars; $tt_user_id = $tt_me_vars['tt_user_id']; $tt_page = $tt_me_vars['tt_paged']; ?>
<div class="col col-md-10 col-right drafts">
    <?php $vm = MeDraftsVM::getInstance($tt_user_id, $tt_page); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Drafts cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php $data = $vm->modelData; $drafts = $data->drafts; $count = $data->count; $max_pages = $data->max_pages; ?>
    <div class="me-tab-box drafts-tab">
        <div class="tab-content me-drafts">
            <!-- 订单列表 -->
            <section class="my-drafts clearfix">
                <header><h2><?php _e('My Drafts', 'tt'); ?></h2></header>
                <?php if($count > 0) { ?>
                    <div class="loop-wrap loop-rows posts-loop-rows clearfix">
                        <?php foreach ($drafts as $draft) { ?>
                            <article id="<?php echo 'post-' . $draft['ID']; ?>" class="post type-post status-draft <?php echo 'format-' . $draft['format']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <a href="<?php echo $draft['edit_link']; ?>"><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $draft['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $draft['title']; ?>"></a>
                                    <?php echo $draft['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title"><a href="<?php echo $draft['edit_link']; ?>" rel="bookmark"><?php echo $draft['title']; ?></a><span><a href="<?php echo $draft['edit_link']; ?>" title="<?php _e('Edit Draft', 'tt'); ?>"><i class="tico tico-new"></i></a> </span></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="entry-date text-muted"><time class="entry-date" datetime="<?php echo $draft['datetime']; ?>" title="<?php echo $draft['datetime']; ?>"><?php echo $draft['time']; ?></time></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $draft['excerpt']; ?></div>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                    <?php if($max_pages > 1) { ?>
                        <div class="pagination-mini clearfix">
                            <?php if($tt_page == 1) { ?>
                                <div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php }else{ ?>
                                <div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php } ?>
                            <div class="col-md-6 page-nums">
                                <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_page); ?></span>
                                <span class="separator">/</span>
                                <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
                            </div>
                            <?php if($tt_page != $data->max_pages) { ?>
                                <div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php }else{ ?>
                                <div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="empty-content">
                        <span class="tico tico-dropbox"></span>
                        <p><?php _e('Nothing found here', 'tt'); ?></p>
                        <a class="btn btn-info" href="/"><?php _e('Back to home', 'tt'); ?></a>
                    </div>
                <?php } ?>
            </section>
        </div>
    </div>
</div>