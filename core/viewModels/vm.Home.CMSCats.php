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
<?php

/**
 * Class HomeCMSCatsVM
 */
class HomeCMSCatsVM extends BaseVM {
    /**
     * @var string CMS布局配置相关Hash
     */
    private $_hash = '';

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.6
     * @param   string    $hash   配置Hash
     * @return  static
     */
    public static function getInstance($hash = '') {
        $instance = new static(); // 因为区分不同主题配置，重载基类该方法以区分和独立缓存
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . get_called_class() . '_hash' . $hash;
        $instance->_hash = $hash;
        //$instance->_enableCache = false; // TODO Debug use
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
        if ($show_ids = tt_get_option('tt_cms_home_show_cats')) {
            $args = array(
//                'orderby' => 'id',
//                'order' => 'ASC',
                'include' => $show_ids
            );
            $categories = get_categories($args);
            $show_ids = explode(',', $show_ids);
            $tmp = array();
            foreach ($categories as $category) {
                $tmp[$category->cat_ID] = $category;
            }
            $categories = array();
            foreach ($show_ids as $id) {
                if (array_key_exists($id, $tmp)) {
                    $categories[] = $tmp[$id];
                }
            }
        } else {
            $args = array(
                'orderby' => 'id',
                'order' => 'ASC',
                'exclude' => tt_get_option('tt_cms_home_hide_cats')
            );
            $categories = get_categories($args);
        }

        $stickys = get_option('sticky_posts');
        $index = 0;
        $start_wrap = true;
        $end_wrap = false;
        $cats_count = count($categories);
        $cms = array();
        $tps = array();
        foreach ($categories as $category) {
            $tps[] = tt_get_cms_cat_template($category->cat_ID);
        }
        foreach ($categories as $category) {
            $index++;
            $cat_id = $category->cat_ID;
            $cat_name = $category->cat_name;
            $cat_link = get_category_link($cat_id);
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'cat' => $cat_id,
                'has_password' => false,
                'ignore_sticky_posts' => true,
                'post__not_in' => $stickys,
                'posts_per_page' => -1,
                'showposts' => 10,
                'orderby' => 'date', // modified - 如果按最新编辑时间排序
                'order' => 'DESC'
            );

            $query = new WP_Query($args);
            $cat_posts = array();
            while ($query->have_posts()) : $query->the_post();
                $cat_post = array();
                global $post;
                $cat_post['ID'] = $post->ID;
                $cat_post['title'] = get_the_title($post);
                $cat_post['permalink'] = get_permalink($post);
                $cat_post['comment_count'] = $post->comment_count;
                $cat_post['excerpt'] = get_the_excerpt($post);
                $cat_post['category'] = get_the_category_list(' ', '', $post->ID);
                $cat_post['author'] = get_the_author();
                $cat_post['author_url'] = get_author_posts_url($post->post_author);
                $cat_post['time'] = get_post_time('Y-m-d', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
                $cat_post['datetime'] = get_the_time('Y-m-d', $post);
                $cat_post['thumb'] = tt_get_thumb($post, 'medium');
                $cat_post['format'] = get_post_format($post) ? : 'standard';
                $cat_post['sticky_class'] = '';
                $cat_post['comment_count'] = $post->comment_count;
                $cat_post['views'] = absint(get_post_meta( $post->ID, 'views', true ));

                $cat_posts[] = $cat_post;
            endwhile;

            wp_reset_postdata();

            $tp = $tps[$index-1];
            $full = $tp != 'Style_0' && $tp != 'Style_6';
            if ($index == 1 || $end_wrap) {
                $start_wrap = true;
            } else {
                $start_wrap = false;
            }
            if ($index == $cats_count || ($tp != 'Style_0' && $tp != 'Style_6') || !$start_wrap || ($tps[$index] != 'Style_0' && $tps[$index] != 'Style_6')) {
                $end_wrap = true;
            } else {
                $end_wrap = false;
            }
            $cms[] = (object)array(
                'cat_id' => $cat_id,
                'cat_name' => $cat_name,
                'cat_link' => $cat_link,
                'tp' => $tp,
                'index' => $index,
                'posts' => $cat_posts,
                'full' => $full,
                'start_wrap' => $start_wrap,
                'end_wrap' => $end_wrap
            );
        }

        return (object)array(
            'cms' => $cms
        );
    }
}