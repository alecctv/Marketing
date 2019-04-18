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

class HotReviewedProductsVM extends BaseVM {

    /**
     * @var int 文章数量
     */
    private $_count = 5;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $count   商品数量
     * @return  static
     */
    public static function getInstance($count = 5) {
        $posts_count = max(5, absint($count));
        $instance = new static(); // 因为配置不同文章数量共用该模型，不采用单例模式
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_count' . $posts_count . '_user' . get_current_user_id();
        $instance->_count = $posts_count;;
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {
//        // 排除分类
//        $uncat = tt_filter_of_multicheck_option(tt_get_option('tt_home_undisplay_cats', array()));
        // 检索置顶用于排除
        $stickies = get_option('sticky_posts');

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'has_password' => false,
            'ignore_sticky_posts' => true,
            'post__not_in' => $stickies,
            'showposts'	=> $this->_count,
            'orderby' => 'meta_value_num',
            'meta_key' => 'views',
            'order'	=> 'desc'
        );

        $query = new WP_Query($args);

        $hotreviewed_Products = array();

        while ($query->have_posts()) : $query->the_post();
            $hotreviewed_Product = array();
            global $post;
            $hotreviewed_Product['ID'] = $post->ID;
            $hotreviewed_Product['title'] = get_the_title($post);
            $hotreviewed_Product['permalink'] = get_permalink($post);
            $hotreviewed_Product['comment_count'] = $post->comment_count;
            //$hothit_Product['category'] = get_the_category_list(' · ', '', $post->ID);
            //$hothit_Product['author'] = get_the_author(); //TODO add link
            //$hotreviewed_Product['time'] = get_post_time('Y-m-d H:i', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            //$hotreviewed_Product['datetime'] = get_the_time(DATE_W3C, $post);
            //$hotreviewed_Product['timediff'] = Utils::getTimeDiffString($hotreviewed_post['time']);
            $hotreviewed_Product['thumb'] = tt_get_thumb($post, array(
                'width' => 200,
                'height' => 150,
                'str' => 'thumbnail'
            ));

            // 点击数
            $hotreviewed_Product['views'] = absint(get_post_meta( $post->ID, 'views', true ));
            // 销量
            $hotreviewed_Product['sales'] = get_post_meta($post->ID, 'tt_product_sales', true);
            // 支付类型
            $hotreviewed_Product['currency'] = get_post_meta( $post->ID, 'tt_pay_currency', true) ? 'cash' : 'credit';
            // 价格
            $hotreviewed_Product['price'] = $hotreviewed_Product['currency'] == 'cash' ? sprintf('%0.2f', get_post_meta($post->ID, 'tt_product_price', true)) : (int)get_post_meta($post->ID, 'tt_product_price', true);
            // 实际价格
            $hotreviewed_Product['min_price'] = tt_get_specified_user_product_price($post->ID, get_current_user_id());
            // 价格图标
            $hotreviewed_Product['price_icon'] = !($hotreviewed_Product['price'] > 0) ? '' : $hotreviewed_Product['currency'] == 'cash' ? '<i class="tico tico-cny"></i>' : '<i class="tico tico-diamond"></i>';
            // 折扣
            $hotreviewed_Product['discount'] = maybe_unserialize(get_post_meta($post->ID, 'tt_product_discount', true)); // array 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣

            $hotreviewed_Products[] = $hotreviewed_Product;
        endwhile;

        wp_reset_postdata();

        return $hotreviewed_Products;
    }
}