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
<div class="cms-cat cms-cat-s3">
    <?php
    global $cat_data;
    $posts = $cat_data->posts;
    $i = 0;
    foreach ($posts as $post) {
        $i++;
        if ($i == 6) {
            ?>
            <div class="col col-right">
                <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
                    <div class="entry-thumb hover-scale">
                        <a href="<?php echo $post['permalink']; ?>"><img src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $post['title']; ?>"></a>
                    </div>
                    <div class="entry-detail">
                        <h3 class="entry-title">
                            <a href="<?php echo $post['permalink']; ?>"><?php echo $post['title']; ?></a>
                        </h3>
                        <div class="entry-meta">
                            <span class="datetime text-muted"><i class="tico tico-alarm"></i><?php echo $post['datetime']; ?></span>
                            <span class="views-count text-muted"><i class="tico tico-eye"></i><?php printf(__('%d (Views)', 'tt'), $post['views']); ?></span>
                            <span class="comments-count text-muted"><i class="tico tico-comments-o"></i><?php printf(__('%d (Comments)', 'tt'), $post['comment_count']); ?></span>
                        </div>
                        <p class="entry-excerpt"><?php echo $post['excerpt']; ?></p>
                    </div>
                </article>
            </div>
            <?php
        } elseif ($i < 6) {
            ?>
            <?php if ($i == 1) { ?><div class="col col-left"><?php } ?>
            <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
                <div class="entry-thumb hover-scale">
                    <a href="<?php echo $post['permalink']; ?>"><img width="250" height="170" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $post['thumb']; ?>" class="thumb-medium wp-post-image lazy" alt="<?php echo $post['title']; ?>" style="max-height: 175px;"></a>
                </div>
                <div class="entry-detail">
                    <h3 class="entry-title">
                        <a href="<?php echo $post['permalink']; ?>"><?php echo $post['title']; ?></a>
                    </h3>
                    <p class="entry-excerpt"><?php echo $post['excerpt']; ?></p>
                </div>
            </article>
            <?php if ($i == 5 || ($i < 5 && $i == count($posts))){ ?></div><?php } ?>
            <?php
        }
    }
    ?>
</div>