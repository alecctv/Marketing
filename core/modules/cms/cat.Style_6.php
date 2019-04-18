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
<div class="cms-cat cms-cat-s0">
    <?php
    global $cat_data;
    $posts = $cat_data->posts;
    $i = 0;
    foreach ($posts as $post) {
        $r = fmod($i, 3)+1;
        $i++;
        if ($i <= 10) {
            ?>
            <div class="row-small">
                <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-publish <?php echo 'format-' . $post['format']; ?>">
                    <div class="entry-detail">
                        <h3 class="entry-title">
                            <i class=""></i>
                            <a href="<?php echo $post['permalink']; ?>" title="<?php echo $post['title']; ?>"><?php echo $post['title']; ?></a>
                        </h3>
                    </div>
                </article>
            </div>
            <?php
        }
    }
    ?>
</div>
