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
<?php $vm = StickysVM::getInstance(4); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
    <!-- CMS stickies cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>
<?php $data = $vm->modelData; ?>
<?php if(is_array($data->sticky_posts) && count($data->sticky_posts) > 0) { ?>
<?php $stickies = $data->sticky_posts; ?>
<div id="cms-stickies" class="block5 wow bounceInUp">
    <section class="block-wrapper clearfix">
        <div class="sticky-container clearfix">
            <h2 class="home-heading clearfix">
                <span class="heading-text">
                    <?php _e('置顶推荐', 'tt');?>
                </span>
            </h2>
            <div class="cms-stickies">
                <?php foreach ($stickies as $sticky) { ?>
                    <div class="col col-1_2">
                    <article id="<?php echo 'post-' . $sticky['ID']; ?>" class="post type-post sticky-post status-publish <?php echo 'format-' . $sticky['format']; ?>">
                        <div class="entry-thumb hover-scale">
                            <a href="<?php echo $sticky['permalink']; ?>"><img src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $sticky['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $sticky['title']; ?>"></a>
                        </div>
                        <div class="entry-detail">
                            <h3 class="entry-title">
                                <a href="<?php echo $sticky['permalink']; ?>"><?php echo $sticky['title']; ?></a>
                            </h3>
<!--                            <div class="entry-meta">-->
<!--                                <span class="datetime text-muted"><i class="tico tico-alarm"></i>--><?php //echo $sticky['datetime']; ?><!--</span>-->
<!--                                <span class="views-count text-muted"><i class="tico tico-eye"></i>--><?php //printf(__('%d (Views)', 'tt'), $sticky['views']); ?><!--</span>-->
<!--                                <span class="comments-count text-muted"><i class="tico tico-comments-o"></i>--><?php //printf(__('%d (Comments)', 'tt'), $sticky['comment_count']); ?><!--</span>-->
<!--                            </div>-->
                            <p class="entry-excerpt"><?php echo $sticky['excerpt']; ?></p>
                        </div>
                    </article>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<?php } ?>