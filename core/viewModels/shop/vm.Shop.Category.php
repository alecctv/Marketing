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
 * Class ShopCategoryVM
 */
class ShopCategoryVM extends BaseVM {

    /**
     * @var int 分页号
     */
    private $_page = 1;

    /**
     * @var int  分类ID
     */
    private $_cat;

    protected function __construct() {
        $this->_cacheUpdateFrequency = 'daily';
        $this->_cacheInterval = 3600*24; // 缓存保留一天
    }

    /**
     * 获取实例
     *
     * @since   2.0.0
     * @param   int    $page   分页号
     * @param   int    $cat    分类ID
     * @return  static
     */
    public static function getInstance($page = 1, $cat = 0) {
        $instance = new static();
        $instance->_cacheKey = 'tt_cache_' . $instance->_cacheUpdateFrequency . '_vm_' . __CLASS__ . '_page' . $page . '_category' . $cat . '_user' . get_current_user_id();
        $instance->_page = max(1, $page);
        $instance->_cat = absint($cat);
        $instance->configInstance();
        return $instance;
    }

    protected function getRealData() {

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 12, //get_option('posts_per_page', 10),
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_category',
                    'field' => 'term_id',
                    'terms' => $this->_cat
                )
            ),
            'paged' => $this->_page,
            'has_password' => false,
            'ignore_sticky_posts' => true,
            'orderby' => 'date', // modified - 如果按最新编辑时间排序
            'order' => 'DESC'
        );

//        if($this->_sort == 'popular') {
//            $args['orderby'] = 'meta_value_num';
//            $args['meta_key'] = 'tt_product_sales';
//        }

        $query = new WP_Query($args);
        $GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query) //TODO 缓存时无效

        $products = array();
        $pagination = array(
            'max_num_pages' => $query->max_num_pages,
            'current_page' => $this->_page,
            'base' => str_replace('999999999', '%#%', get_pagenum_link(999999999))
        );

        while ($query->have_posts()) : $query->the_post();
            $product = array();
            global $post;
            $product['ID'] = $post->ID;
            $product['title'] = get_the_title($post);
            $product['permalink'] = get_permalink($post);
            $product['comment_count'] = $post->comment_count;
            $product['excerpt'] = get_the_excerpt($post);
            $product['category'] = get_the_category_list(' ', '', $post->ID);
            $product['author'] = get_the_author();
            $product['author_url'] = home_url('/@' . $product['author']); //TODO the link
            $product['time'] = get_post_time('F j, Y', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
            $product['datetime'] = get_the_time(DATE_W3C, $post);
            $product['thumb'] = tt_get_thumb($post, array('width' => 350, 'height' => 250, 'str' => 'medium'));
            //$product['format'] = get_post_format($post) ? : 'standard';

            $product['views'] = (int)get_post_meta($post->ID, 'views', true);

            // 销量
            $product['sales'] = (int)get_post_meta($post->ID, 'tt_product_sales', true);

            // 支付类型
            $product['currency'] = get_post_meta( $post->ID, 'tt_pay_currency', true) ? 'cash' : 'credit';

            // 价格
            $product['price'] = $product['currency'] == 'cash' ? sprintf('%0.2f', get_post_meta($post->ID, 'tt_product_price', true)) : (int)get_post_meta($post->ID, 'tt_product_price', true);
           
            // 实际价格
            $product['min_price'] = tt_get_specified_user_product_price($post->ID, get_current_user_id());
            
            // 折扣
            $product['discount'] = maybe_unserialize(get_post_meta( $post->ID, 'tt_product_discount', true)); // array 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣

            // 单位
            $product['price_unit'] = $product['currency'] == 'cash' ? __('YUAN', 'tt') : __('CREDITS', 'tt');
			
			// 价格图标
            $product['price_icon'] = !($product['price'] > 0) ? '' : $product['currency'] == 'cash' ? '<i class="tico tico-cny"></i>' : '<i class="tico tico-diamond"></i>';

            // 点赞
            $star_user_ids = array_unique(get_post_meta( $post->ID, 'tt_post_star_users', false));
            $product['stars'] = count($star_user_ids);

            $products[] = $product;
        endwhile;

        wp_reset_postdata();

        return (object)array(
            'pagination' => $pagination,
            'products' => $products
        );
    }
}