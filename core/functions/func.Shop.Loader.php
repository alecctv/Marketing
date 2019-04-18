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

// load_func('shop/func.Shop');
/**
 * 创建商品自定义文章类型
 *
 * @since 2.0.0
 * @return void
 */
function tt_create_product_post_type() {
    $shop_slug = tt_get_option('tt_product_archives_slug', 'shop');
    register_post_type( 'product',
        array(
            'labels' => array(
                'name' => _x( 'Products', 'taxonomy general name', 'tt' ),
                'singular_name' => _x( 'Product', 'taxonomy singular name', 'tt' ),
                'add_new' => __( 'Add New', 'tt' ),
                'add_new_item' => __( 'Add New Product', 'tt' ),
                'edit' => __( 'Edit', 'tt' ),
                'edit_item' => __( 'Edit Product', 'tt' ),
                'new_item' => __( 'Add Product', 'tt' ),
                'view' => __( 'View', 'tt' ),
                'all_items' => __( 'All Products', 'tt' ),
                'view_item' => __( 'View Product', 'tt' ),
                'search_items' => __( 'Search Product', 'tt' ),
                'not_found' => __( 'Product not found', 'tt' ),
                'not_found_in_trash' => __( 'Product not found in trash', 'tt' ),
                'parent' => __( 'Parent Product', 'tt' ),
                'menu_name' => __( 'Shop and Products', 'tt' ),
            ),

            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'author', 'editor', 'comments', 'excerpt', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-cart',
            'has_archive' => true,
            'rewrite'	=> array('slug'=>$shop_slug)
        )
    );
}
add_action( 'init', 'tt_create_product_post_type' );


/**
 * 为商品启用单独模板
 *
 * @since 2.0.0
 * @param $template_path
 * @return string
 */
function tt_include_shop_template_function( $template_path ) {
    if ( get_post_type() == 'product' ) {
        if ( is_single() ) {
            //指定单个商品模板
            if ( $theme_file = locate_template( array ( 'core/templates/shop/tpl.Product.php' ) ) ) {
                $template_path = $theme_file;
            }
        }elseif(tt_is_product_category()){
            //指定商品分类模板
            if ( $theme_file = locate_template( array ( 'core/templates/shop/tpl.Product.Category.php' ) ) ) {
                $template_path = $theme_file;
            }
        }elseif(tt_is_product_tag()){
            //指定商品标签模板
            if ( $theme_file = locate_template( array ( 'core/templates/shop/tpl.Product.Tag.php' ) ) ) {
                $template_path = $theme_file;
            }
        }elseif(is_archive()){
            //指定商店首页模板
            if ( $theme_file = locate_template( array ( 'core/templates/shop/tpl.Product.Archive.php' ) ) ) {
                $template_path = $theme_file;
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'tt_include_shop_template_function', 1 );


/**
 * 为商品启用分类和标签
 *
 * @since 2.0.0
 * @return void
 */
function tt_create_product_taxonomies() {
    $shop_slug = tt_get_option('tt_product_archives_slug', 'shop');
    // Categories
    $product_category_labels = array(
        'name' => _x( 'Products Categories', 'taxonomy general name', 'tt' ),
        'singular_name' => _x( 'Products Category', 'taxonomy singular name', 'tt' ),
        'search_items' => __( 'Search Products Categories', 'tt' ),
        'all_items' => __( 'All Products Categories', 'tt' ),
        'parent_item' => __( 'Parent Products Category', 'tt' ),
        'parent_item_colon' => __( 'Parent Products Category:', 'tt' ),
        'edit_item' => __( 'Edit Products Category', 'tt' ),
        'update_item' => __( 'Update Products Category', 'tt' ),
        'add_new_item' => __( 'Add New Products Category', 'tt' ),
        'new_item_name' => __( 'Name of New Products Category', 'tt' ),
        'menu_name' => __( 'Products Categories', 'tt' ),
    );
    register_taxonomy( 'product_category', 'product', array(
        'hierarchical'  => true,
        'labels'        => $product_category_labels,
        'show_ui'       => true,
        'query_var'     => true,
        'rewrite'       => array(
            'slug'          => $shop_slug . '/category',
            'with_front'    => false,
        ),
    ) );
    // Tags
    $product_tag_labels = array(
        'name' => _x( 'Product Tags', 'taxonomy general name', 'tt' ),
        'singular_name' => _x( 'Product Tag', 'taxonomy singular name', 'tt' ),
        'search_items' => __( 'Search Product Tags', 'tt' ),
        'popular_items' => __( 'Popular Product Tags', 'tt' ),
        'all_items' => __( 'All Product Tags', 'tt' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Product Tag', 'tt' ),
        'update_item' => __( 'Update Product Tag', 'tt' ),
        'add_new_item' => __( 'Add New Product Tag', 'tt' ),
        'new_item_name' => __( 'Name of New Product Tag', 'tt' ),
        'separate_items_with_commas' => __( 'Separate Product Tags with Commas', 'tt' ),
        'add_or_remove_items' => __( 'Add or Remove Product Tag', 'tt' ),
        'choose_from_most_used' => __( 'Choose from Most Used Product Tags', 'tt' ),
        'menu_name' => __( 'Product Tags', 'tt' ),
    );

    register_taxonomy('product_tag', 'product', array(
        'hierarchical'  => false,
        'labels'        => $product_tag_labels,
        'show_ui'       => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'     => true,
        'rewrite'       => array(
            'slug' => $shop_slug . '/tag',
            'with_front'    => false,
        ),
    ) );
}
add_action( 'init', 'tt_create_product_taxonomies', 0 );

/**
 * 自定义产品的链接
 *
 * @since 2.0.0
 * @param $link
 * @param object $post
 * @return string|void
 */
function tt_custom_product_link( $link, $post = null ){
    $shop_slug = tt_get_option('tt_product_archives_slug', 'shop');
    $product_slug = tt_get_option('tt_product_link_mode')=='post_name' ? $post->post_name : $post->ID;
    if ( $post->post_type == 'product' ){
        return home_url( $shop_slug . '/' . $product_slug . '.html' );
    } else {
        return $link;
    }
}
add_filter('post_type_link', 'tt_custom_product_link', 1, 2);


/**
 * 处理商品自定义链接Rewrite规则
 *
 * @since 2.0.0
 * @return void
 */
function tt_handle_custom_product_rewrite_rules(){
    $shop_slug = tt_get_option('tt_product_archives_slug', 'shop');
    if(tt_get_option('tt_product_link_mode') == 'post_name'):
        add_rewrite_rule(
            $shop_slug . '/([一-龥a-zA-Z0-9_-]+)?.html([\s\S]*)?$',
            'index.php?post_type=product&name=$matches[1]',
            'top' );
    else:
        add_rewrite_rule(
            $shop_slug . '/([0-9]+)?.html([\s\S]*)?$',
            'index.php?post_type=product&p=$matches[1]',
            'top' );
    endif;
}
add_action( 'init', 'tt_handle_custom_product_rewrite_rules' );


/**
 * 后台商品列表信息列
 *
 * @since 2.0.0
 * @param $columns
 * @return array
 */
function tt_product_columns( $columns ) {
    $columns['product_ID'] = __('Product ID', 'tt');
    $columns['product_price'] = __('Price', 'tt');
    $columns['product_quantity'] = __('Quantities', 'tt');
    $columns['product_sales'] = __('Sales', 'tt');
    unset( $columns['comments'] );
    if(isset($columns['title'])) {
        $columns['title'] = __('Product Name', 'tt');
    }
    if(isset($columns['author'])) {
        $columns['author'] = __('Publisher', 'tt');
    }
    if(isset($columns['views'])) {
        $columns['views'] = __('Hot Hits', 'tt');
    }

    // TODO thumbnail(qiniu plugin)
    return $columns;
}
add_filter( 'manage_edit-product_columns', 'tt_product_columns' );
function tt_populate_product_columns( $column ) {
    if ( 'product_ID' == $column ) {
        $product_ID = esc_html( get_the_ID() );
        echo $product_ID;
    }
    elseif ( 'product_price' == $column ) {
        $product_price = get_post_meta( get_the_ID(), 'tt_product_price', true ) ? : '0.00';
        $currency = get_post_meta( get_the_ID(), 'tt_pay_currency', true );
        if($currency==0){
            $text= __('Credit', 'tt');
        }else{
            $text = __('RMB YUAN', 'tt');
        }
        $price = $product_price . ' ' . $text;
        echo $price;
    }elseif( 'product_quantity' == $column ){
        $product_quantity = get_post_meta( get_the_ID(), 'tt_product_quantity', true ) ? : 0;
        echo $product_quantity . ' ' . __('pieces', 'tt');
    }elseif( 'product_sales' == $column ){
        $product_sales = get_post_meta( get_the_ID(), 'tt_product_sales', true ) ? : 0;
        echo $product_sales . ' ' . __('pieces', 'tt');
    }
}
add_action( 'manage_posts_custom_column', 'tt_populate_product_columns' );


/**
 * 后台商品列表信息列排序
 *
 * @param $columns
 * @return mixed
 */
function tt_sort_product_columns($columns){
    $columns['product_ID'] = __('Product ID', 'tt');
    $columns['product_price'] = __('Price', 'tt');
    $columns['product_quantity'] = __('Quantities', 'tt');
    $columns['product_sales'] = __('Sales', 'tt');
    return $columns;
}
add_filter('manage_edit-product_sortable_columns', 'tt_sort_product_columns');
function tt_product_column_orderby($vars){
    if(!is_admin())
        return $vars;
    if(isset($vars['orderby'])&&'product_price'==$vars['orderby']){
        $vars = array_merge($vars, array('meta_key'=>'tt_product_price', 'orderby'=>'meta_value'));
    }elseif(isset($vars['orderby'])&&'product_quantity'==$vars['orderby']){
        $vars = array_merge($vars, array('meta_key'=>'tt_product_quantity', 'orderby'=>'meta_value')); //Note v1中postmeta 中使用的是product_amount, 而筛选使用的是product_quantity, 导致筛选无效
    }elseif(isset($vars['orderby'])&&'product_sales'==$vars['orderby']){
        $vars = array_merge($vars, array('meta_key'=>'tt_product_sales', 'orderby'=>'meta_value'));
    }
    return $vars;
}
add_filter('request','tt_product_column_orderby');


/**
 * 后台商品列表分类筛选
 *
 * @since 2.0.0
 * @return void
 */
function tt_filter_products_list() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'store' ) {
        wp_dropdown_categories( array(
            'show_option_all' => __('Show all categories', 'tt'),
            'taxonomy' => 'products_category',
            'name' => __('Product Category'),
            'id' => 'filter-by-products_category',
            'orderby' => 'name',
            'selected' => ( isset( $wp_query->query['products_category'] ) ? $wp_query->query['products_category'] : '' ),
            'hierarchical' => false,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ) );
    }
}
add_action( 'restrict_manage_posts', 'tt_filter_products_list' );
function tt_perform_products_filtering( $query ) {
    $qv = &$query->query_vars;
    if ( isset( $qv['products_category'] ) && is_numeric( $qv['products_category'] ) ) {
        $term = get_term_by( 'id', $qv['products_category'], 'products_category' );
        $qv['products_category'] = $term->slug;
    }
    return $query;
}
add_filter( 'parse_query','tt_perform_products_filtering' );


/**
 * 统计不同支付和价格类型的商品数量
 *
 * @since 2.0.0
 * @param $type
 * @return int
 */
function tt_count_products_by_price_type($type = 'free') {
    switch ($type) {
        case 'free':
            $query = new WP_Query( array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'tt_product_price',
                        'value' => 0.01,
                        'compare' => '<'
                    )
                )
            ));
            return absint($query->found_posts);
            break;
        case 'credit':
            $query = new WP_Query( array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'tt_pay_currency',
                        'value' => 0,
                        'compare' => '='
                    ),
                    array(
                        'key' => 'tt_product_price',
                        'value' => '0.00',
                        'compare' => '>'
                    )
                )
            ));
            return absint($query->found_posts);
            break;
        case 'cash':
            $query = new WP_Query( array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'tt_pay_currency',
                        'value' => 1,
                        'compare' => '='
                    ),

                    array(
                        'key' => 'tt_product_price',
                        'value' => '0.00',
                        'compare' => '>'
                    )
                )
            ));
            return absint($query->found_posts);
            break;
        default:
            return 1;
    }
}


/**
 * 判断当前页面是否为商品分类
 *
 * @since 2.0.0
 * @return bool
 */
function tt_is_product_category() {
    $object =get_queried_object();
    if($object instanceof WP_Term && $object->taxonomy == 'product_category') {
        return true;
    }
    return false;
}


/**
 * 判断当前页面是否为商品标签
 *
 * @since 2.0.0
 * @return bool
 */
function tt_is_product_tag() {
    $object =get_queried_object();
    if($object instanceof WP_Term && $object->taxonomy == 'product_tag') {
        return true;
    }
    return false;
}


/**
 * 检查用户是否购买了某产品(只考虑交易成功的)
 *
 * @since 2.0.0
 * @param $product_id
 * @param int $user_id
 * @return bool
 */
function tt_check_user_has_buy_product($product_id, $user_id = 0) {
    $the_orders = tt_get_specified_user_and_product_orders($product_id, $user_id);
    if(!$the_orders){
        return false;
    }
    foreach ($the_orders as $the_order){
        if($the_order->parent_id > 0) {
            // 这是个合并订单的子订单，支付状态根据父级订单算
            $parent_order = tt_get_order_by_sequence($the_order->parent_id);
            if($parent_order && $parent_order->order_status == OrderStatus::TRADE_SUCCESS) {
                return true;
            }
        }elseif($the_order->order_status == OrderStatus::TRADE_SUCCESS){
            return true;
        }
    }
    return false;
}


/**
 * 检查某商品对某用户的实际价格(会员优惠、全站折扣等)
 *
 * @since 2.0.0
 * @param $product_id
 * @param int $user_id
 * @return double|int
 */
function tt_get_specified_user_product_price($product_id, $user_id = 0) {
    $currency = get_post_meta( $product_id, 'tt_pay_currency', true) ? 'cash' : 'credit';
    $price = $currency == 'cash' ? sprintf('%0.2f', get_post_meta($product_id, 'tt_product_price', true)) : (int)get_post_meta($product_id, 'tt_product_price', true);

    $discount_summary = tt_get_product_discount_array($product_id); // array 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣

    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id) {
        return $currency == 'cash' ? sprintf('%0.2f', $price * absint($discount_summary[0]) / 100) : intval($price * absint($discount_summary[0]) / 100);
    }
    $member = new Member($user_id);
    switch ($member->vip_type){
        case Member::MONTHLY_VIP:
            $discount = $discount_summary[1];
            break;
        case Member::ANNUAL_VIP:
            $discount = $discount_summary[2];
            break;
        case Member::PERMANENT_VIP:
            $discount = $discount_summary[3];
            break;
        default:
            $discount = $discount_summary[0];
            break;
    }
    $discount = min($discount_summary[0], $discount); // 会员的价格不能高于普通打折价

    // 最低价格
   
    return $currency == 'cash' ? sprintf('%0.2f', $price * absint($discount) / 100) : intval($price * absint($discount) / 100);
}


/**
 * 获取商品中的下载链接
 *
 * @since 2.0.0
 * @param $product_id
 * @param $html
 * @return string
 */
function tt_get_product_download_content($product_id, $html = true){
    $content = '';
    $array_content = array();
    $dl_links = get_post_meta($product_id, 'tt_product_download_links', true);
    if(!empty($dl_links)):
        $dl_links = explode(PHP_EOL, $dl_links);
        foreach($dl_links as $dl_link){
            $dl_info = explode('|', $dl_link);
            $dl_info[0] = isset($dl_info[0]) ? $dl_info[0] : '';
            $dl_info[1] = isset($dl_info[1]) ? $dl_info[1] : '';
            $dl_info[2] = isset($dl_info[2]) ? $dl_info[2] : __('None', 'tt');
            $content .= sprintf(__('<li style="margin: 0 0 10px 0;"><p style="padding: 5px 0; margin: 0;">%1$s</p><p style="padding: 5px 0; margin: 0;">下载链接：<a href="%2$s" title="%1$s" target="_blank">%2$s</a>下载密码：%3$s</p></li>', 'tt'), $dl_info[0], $dl_info[1], $dl_info[2]);
            $array_content[] = array(
                'name' => $dl_info[0],
                'link' => $dl_info[1],
                'password' => $dl_info[2]
            );
        }
    endif;
    return $html ? $content : $array_content;
}


/**
 * 获取商品付费内容(包含下载链接和付费可见内容)
 *
 * @since 2.0.0
 * @param $product_id
 * @param bool $html 是否输出HTML
 * @return string
 */
function tt_get_product_pay_content($product_id, $html = true){
    $user_id = get_current_user_id();

    $price = tt_get_specified_user_product_price($product_id, $user_id);
    $show = $price < 0.01 || tt_check_user_has_buy_product($product_id, $user_id);
    if(!$show) {
        return $html ? __('<div class="contextual-bg bg-paycontent"><span><i class="tico tico-paypal">&nbsp;</i>付费内容</span><p>你只有购买支付后才能查看该内容！</p></div>', 'tt') : null;
    }

    $pay_content = get_post_meta($product_id, 'tt_product_pay_content', true);
    $download_content = tt_get_product_download_content($product_id, $html);

    return $html ? sprintf(__('<div class="contextual-bg bg-paycontent"><span><i class="tico tico-paypal">&nbsp;</i>付费内容</span><p>%1$s</p><p>%2$s</p></div>', 'tt'), $download_content, $pay_content) : array('download_content' => $download_content, 'pay_content' => $pay_content);
}


/**
 * 获取商品折扣数组
 *
 * @since 2.0.0
 * @param $product_id
 * @return array|mixed
 */
function tt_get_product_discount_array($product_id){
    $discount = maybe_unserialize(get_post_meta($product_id, 'tt_product_discount', true));
    if(!is_array($discount)) {
        return array(100, intval(tt_get_option('tt_monthly_vip_discount', 100)), intval(tt_get_option('tt_annual_vip_discount', 90)), intval(tt_get_option('tt_permanent_vip_discount', 80)));
    }
    $discount[0] = isset($discount[0]) ? min(100, absint($discount[0])) : 100;
    $discount[1] = isset($discount[1]) ? min(100, absint($discount[1])) : $discount[0];
    $discount[2] = isset($discount[2]) ? min(100, absint($discount[2])) : $discount[0];
    $discount[3] = isset($discount[3]) ? min(100, absint($discount[3])) : $discount[0];
    return $discount;
}


/**
 * 获取现金支付的方式
 *
 * @since 2.0.0
 * @return string
 */
function tt_get_cash_pay_method() {
    $pay_method = tt_get_option('tt_pay_channel', 'alipay')=='alipay' && tt_get_option('tt_alipay_email') && tt_get_option('tt_alipay_partner') ? 'alipay' : 'qrcode';
    return $pay_method;
}


/**
 * 获取支付宝支付网关
 *
 * @since 2.0.0
 * @param $order_id
 * @return string
 */
function tt_get_alipay_gateway($order_id) {
    return add_query_arg(array('oid' => $order_id, 'spm' => wp_create_nonce('pay_gateway'), 'channel' => 'alipay'), tt_url_for('paygateway'));
}


/**
 * 获取扫码转账支付网关
 *
 * @since 2.0.0
 * @param $order_id
 * @return string
 */
function tt_get_qrpay_gateway($order_id) {
    return add_query_arg(array('oid' => $order_id), tt_url_for(tt_get_option('tt_pay_channel') === 'youzan' ? 'youzanpay' : 'qrpay'));
}


/**
 * 查找某个商品的购买用户的邮箱
 *
 * @param $product_id
 * @return array|null|object
 */
function tt_get_buyer_emails($product_id) {
    $cache_key = 'tt_product' . $product_id . '_buyer_emails';
    if($cache = get_transient($cache_key)) {
        return maybe_unserialize($cache);
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $sql = sprintf("SELECT `user_id` FROM $orders_table WHERE `deleted`=0 AND `order_status`=%d AND `product_id`=%d ORDER BY `id` DESC", OrderStatus::TRADE_SUCCESS, $product_id); // TODO 子订单因为状态由父级订单决定，不好查找
    $results = $wpdb->get_col($sql);

    if(!$results || count($results) < 1) return null;

//    $users = get_users(array(
//       'include' => $results,
//        'fields' => array('user_email')
//    ));

    $user_emails = $wpdb->get_col(sprintf("SELECT `user_email` FROM $wpdb->users WHERE ID IN (%s) AND `user_email`<>''", implode(',', $results)));

    set_transient($cache_key, maybe_serialize($user_emails), 3600*24);
    return $user_emails;
}

// load_func('shop/func.Shop.Order');
/**
 * 创建商品系统必须的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_orders_table() {
    global $wpdb;
    include_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    if($wpdb->has_cap('collation')) {
        if(!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if(!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    // 订单数据表
    // - id 自增ID
    // - parent_id 父级订单ID, 默认0(购物车合并支付时需要创建合并订单)
    // - order_id 一定规律生成的唯一订单号
    // - trade_no 支付系统(支付宝)交易号
    // - product_id 商品ID
    // - product_name 商品名称
    // - order_time 订单创建时间
    // - order_success_time 订单交易成功时间
    // - order_price 订单单价
    // - order_currency 支付类型(积分或现金)
    // - order_quantity 购买数量
    // - order_total_price 订单总价
    // - order_status 订单状态 0/1/2/3/4/9
    // - coupon_id 使用的优惠码ID
    // - user_id 用户ID
    // - address_id 使用的地址ID
    // - user_message 用户备注留言
    // - user_alipay 用户支付宝账户
    // - deleted
    // - deleted_by
    $create_orders_sql = "CREATE TABLE $orders_table (id int(11) NOT NULL auto_increment,parent_id int(11) NOT NULL DEFAULT 0,order_id varchar(30) NOT NULL,trade_no varchar(50) NOT NULL,product_id int(20) NOT NULL,product_name varchar(250),order_time datetime NOT NULL default '0000-00-00 00:00:00',order_success_time datetime NOT NULL default '0000-00-00 00:00:00',order_price double(10,2) NOT NULL,order_currency varchar(20) NOT NULL default 'credit',order_quantity int(11) NOT NULL default 1,order_total_price double(10,2) NOT NULL,order_status tinyint(4) NOT NULL default 1,coupon_id int(11) DEFAULT 0,user_id int(11) NOT NULL,address_id int(11) NOT NULL DEFAULT 0,user_message text,user_alipay varchar(100),deleted tinyint(4) NOT NULL default 0,deleted_by int(11),PRIMARY KEY (id),INDEX orderid_index(order_id),INDEX tradeno_index(trade_no),INDEX productid_index(product_id),INDEX uid_index(user_id)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($orders_table,$create_orders_sql);
}
add_action( 'admin_init', 'tt_install_orders_table' );

/**
 * 生成随机订单号
 *
 * @since 2.0.0
 * @return string
 */
function tt_generate_order_num(){
    $orderNum = mt_rand(10,25) . time() . mt_rand(1000,9999);
    return strval($orderNum);
}

/**
 * 获取订单状态文字
 *
 * @since 2.0.0
 * @param $code
 * @return string
 */
function tt_get_order_status_text($code){
    switch($code){
        case (OrderStatus::WAIT_PAYMENT):
            $status_text = __('Wait Payment', 'tt'); //等待买家付款
            break;
        case (OrderStatus::PAYED_AND_WAIT_DELIVERY):
            $status_text = __('Payed, Wait Delivery', 'tt'); //已付款，等待卖家发货
            break;
        case (OrderStatus::DELIVERED_AND_WAIT_CONFIRM):
            $status_text = __('Delivered, Wait Confirm', 'tt'); //已发货，等待买家确认
            break;
        case (OrderStatus::TRADE_SUCCESS):
            $status_text = __('Trade Succeed', 'tt'); //交易成功
            break;
        case (OrderStatus::TRADE_CLOSED):
            $status_text = __('Trade Closed', 'tt'); //交易关闭
            break;
        default:
            $status_text = __('Order Created', 'tt'); //订单创建成功
    }
    return $status_text;
}


/**
 * 获取指定订单
 *
 * @since 2.0.0
 * @param $order_id
 * @return array|null|object|void
 */
function tt_get_order($order_id) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $order = $wpdb->get_row(sprintf("SELECT * FROM $orders_table WHERE `order_id`='%s'", $order_id));
    return $order;
}


/**
 * 获取指定订单(通过订单序号)
 *
 * @since 2.0.0
 * @param $seq
 * @return array|null|object|void
 */
function tt_get_order_by_sequence($seq) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $order = $wpdb->get_row(sprintf("SELECT * FROM $orders_table WHERE `id`='%d'", $seq));
    return $order;
}


/**
 * 获取指定订单(通过tradeNo)
 *
 * @since 1.1.0
 * @param $trade_no
 * @return array|null|object|void
 */
function tt_get_order_by_trade_no($trade_no) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $order = $wpdb->get_row(sprintf("SELECT * FROM $orders_table WHERE `trade_no`='%d'", $trade_no));
    return $order;
}


/**
 * 获取多条订单记录
 *
 * @since 2.0.0
 * @param $limit
 * @param $offset
 * @param string $currency_type
 * @return array|null|object
 */
function tt_get_orders($limit, $offset, $currency_type = 'all'){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    if($currency_type == 'all'){
        $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 ORDER BY `id` DESC LIMIT %d OFFSET %d", $limit, $offset);
    }else{
        $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `order_currency`='%s' ORDER BY `id` DESC LIMIT %d OFFSET %d", $currency_type, $limit, $offset);
    }
    $results = $wpdb->get_results($sql);
    return $results;
}


/**
 * 获取订单数量
 *
 * @since 2.0.0
 * @param string $currency_type
 * @return int
 */
function tt_count_orders($currency_type = 'all'){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    if($currency_type == 'all'){
        $sql = "SELECT COUNT(*) FROM $orders_table WHERE `deleted`=0";
    }else{
        $sql = sprintf("SELECT COUNT(*) FROM $orders_table WHERE `deleted`=0 AND `order_currency`='%s'", $currency_type);
    }
    $count = $wpdb->get_var($sql);
    return (int)$count;
}


/**
 * 获取用户的订单
 *
 * @since 2.0.0
 * @param int $user_id
 * @param int $limit
 * @param int $offset
 * @param string $currency_type
 * @return array|null|object
 */
function tt_get_user_orders($user_id = 0, $limit = 20, $offset = 0, $currency_type = 'all'){
    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id){
        return null;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    if($currency_type == 'all'){
        $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d ORDER BY `id` DESC LIMIT %d OFFSET %d", $user_id, $limit, $offset);
    }else{
        $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d AND `order_currency`='%s' ORDER BY `id` DESC LIMIT %d OFFSET %d", $user_id, $currency_type, $limit, $offset);
    }
    $results = $wpdb->get_results($sql);

    return $results;
}


/**
 * 获取用户的对某一文章下资源的订单(只返回成功的订单)
 *
 * @since 2.0.0
 * @param int $user_id
 * @param int $post_id
 * @return array|null|object
 */
function tt_get_user_post_resource_orders($user_id, $post_id){
    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id){
        return null;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d AND `product_id`=%d AND `order_status`=4 ORDER BY `id` DESC", $user_id, $post_id);
    $results = $wpdb->get_results($sql);

    return $results;
}

/**
 * 统计用户订单数量
 *
 * @since 2.0.0
 * @param int $user_id
 * @param string $currency_type
 * @return int
 */
function tt_count_user_orders($user_id = 0, $currency_type = 'all'){
    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id){
        return 0;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    if($currency_type == 'all'){
        $sql = sprintf("SELECT COUNT(*) FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d", $user_id);
    }else{
        $sql = sprintf("SELECT COUNT(*) FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d AND `order_currency`='%s'", $user_id, $currency_type);
    }
    $count = $wpdb->get_var($sql);
    return (int)$count;
}


/**
 * 获取指定用户指定商品的订单
 *
 * @param $product_id
 * @param int $user_id
 * @return array|null|object
 */
function tt_get_specified_user_and_product_orders($product_id, $user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    if(!$user_id){
        return null;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $sql = sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `user_id`=%d AND `product_id`=%d ORDER BY `id` DESC", $user_id, $product_id);
    $results = $wpdb->get_results($sql);
    return $results;
}


/**
 * 获取子订单
 *
 * @since 2.0.0
 * @param $parent_id
 * @return array|null|object
 */
function tt_get_sub_orders($parent_id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $results = $wpdb->get_results(sprintf("SELECT * FROM $orders_table WHERE `deleted`=0 AND `parent_id`=%d ORDER BY `id` ASC", $parent_id));
    return $results;
}


/**
 * 创建单个订单
 *
 * @since 2.0.0
 * @param $product_id
 * @param string $product_name
 * @param int $order_quantity
 * @param int $parent_id
 * @return bool|array
 */
function tt_create_order($product_id, $product_name = '', $order_quantity = 1, $parent_id = 0){
    $user_id = get_current_user_id();
    $member = new Member($user_id);
    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    $currency = get_post_meta( $product_id, 'tt_pay_currency', true) ? 'cash' : 'credit';
    $order_price = $currency == 'cash' ? sprintf('%0.2f', get_post_meta($product_id, 'tt_product_price', true)) : (int)get_post_meta($product_id, 'tt_product_price', true);
    // 折扣
    $discount_summary = tt_get_product_discount_array($product_id); // array 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣
    switch ($member->vip_type){
        case Member::MONTHLY_VIP:
            $discount = $discount_summary[1];
            break;
        case Member::ANNUAL_VIP:
            $discount = $discount_summary[2];
            break;
        case Member::PERMANENT_VIP:
            $discount = $discount_summary[3];
            break;
        default:
            $discount = $discount_summary[0];
            break;
    }
    $discount = min($discount_summary[0], $discount); // 会员的价格不能高于普通打折价
    $order_quantity = max(1, $order_quantity);
    $order_total_price = $currency == 'cash' ? $order_price * absint($order_quantity) * $discount / 100 : absint($order_price * $order_quantity * $discount / 100);

    $product_name = $product_name ? : get_the_title($product_id);

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => $parent_id,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => $order_quantity,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id
        ),
        array(
            '%d',
            '%s',
            '%d',
            '%s',
            '%s',
            '%f',
            '%s',
            '%d',
            '%f',
            '%d'
        )
    );
    if($insert) {
        // 新创建现金订单时邮件通知管理员
        if($currency == 'cash') {
            do_action('tt_order_status_change', $order_id);
        }

        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price
        );
    }
    return false;
}


/**
 * 创建合并订单(购物车结算)
 *
 * @since 2.0.0
 * @param $product_ids
 * @param $order_quantities
 * @return array|WP_Error
 */
function tt_create_combine_orders($product_ids, $order_quantities){
    $product_names = array();
    foreach ($product_ids as $product_id){
        $product_names[] = get_the_title($product_id);
    }
    $user_id = get_current_user_id();
    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    // 创建父级订单
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => -1, // -1表示有子订单
            'order_id' => $order_id,
            'product_id' => 0,
            'product_name' => implode('|', $product_names),
            'order_time' => $order_time,
            'order_price' => 0,
            'order_currency' => 'cash',
            'order_quantity' => 0,
            'order_total_price' => 0,
            'user_id' => $user_id
        ),
        array(
            '%d',
            '%s',
            '%d',
            '%s',
            '%s',
            '%f',
            '%s',
            '%d',
            '%f',
            '%d'
        )
    );
    if(!$insert) {
        return new WP_Error('create_order_failed', __('Create combine order failed', 'tt'));
    }
    $insert_id = $wpdb->insert_id;
    // 创建子订单
    $total_price = 0;
    foreach ($product_ids as $key => $product_id){
        $sub_order = tt_create_order($product_id, $product_names[$key], $order_quantities[$key], $insert_id);
        if(!$sub_order) {
            return new WP_Error('create_order_failed', __('Create combine order failed', 'tt')); //TODO distinguish
        }
        $total_price += $sub_order['total_price'];
    }
    // 更新父级订单
    $update = $wpdb->update(
        $orders_table,
        array(
            'order_total_price' => $total_price
        ),
        array('id' => $insert_id),
        array(
            '%f'
        ),
        array('%d')
    );
    if(!$update){
        return new WP_Error('create_order_failed', __('Create combine order failed', 'tt')); //TODO distinguish
    }
    return array(
        'insert_id' => $insert_id,
        'order_id' => $order_id,
        'total_price' => $total_price
    );
}


/**
 * 更新订单内容
 *
 * @since 2.0.0
 * @param $order_id
 * @param $data
 * @param $format
 * @return bool
 */
function tt_update_order($order_id, $data, $format){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $update = $wpdb->update(
        $orders_table,
        $data,
        array('order_id' => $order_id),
        $format,
        array('%s')
    );
    if(!($update===false)){
        if(isset($data['order_status'])) { // 删除订单时不触发
            // 钩子 - 用于清理缓存等
            do_action('tt_order_status_change', $order_id);
        }
        return true;
    }
    return false;
}


/**
 * 使用优惠码更新订单总价
 *
 * @param $order_id
 * @param $coupon_code
 * @return bool|object|WP_Error
 */
function tt_update_order_by_coupon($order_id, $coupon_code){
    // 检验order
    $order = tt_get_order($order_id);
    if(!$order) {
        return new WP_Error('order_id_invalid', __('The order with the a order id you specified is not existed', 'tt'), array( 'status' => 404 ));
    }
    if($order->coupon_id != 0) {
        return new WP_Error('coupon_invalid', __('该订单已使用过优惠码', 'tt'), array( 'status' => 404 ));
    }
    $total_price = $order->order_total_price;
    // 检验coupon
    $coupon = tt_check_coupon($coupon_code);
    if($coupon instanceof WP_Error) {
        return $coupon;
    }elseif(!$coupon){
        return new WP_Error('coupon_invalid', __('The coupon is invalid', 'tt'), array( 'status' => 404 ));
    }
    $discount = $coupon->discount_value;
    // 标记一次性coupon为已使用
    if($coupon->coupon_type == 'once'){
        $mark_used = tt_update_coupon($coupon->id, array('coupon_status' => 0), array('%d'));
    }
    // 更新订单
    $update = tt_update_order($order_id, array('order_total_price' => abs($total_price * $discount), 'coupon_id' => $coupon->id), array('%f', '%d'));
    return !($update === false);
}


/**
 * 根据自增id删除指定订单
 *
 * @since 2.0.0
 * @param $id
 * @return bool
 */
function tt_delete_order($id){
//    global $wpdb;
//    $prefix = $wpdb->prefix;
//    $orders_table = $prefix . 'tt_orders';
//    $delete = $wpdb->delete(
//        $orders_table,
//        array('id' => $id),
//        array('%d')
//    );
    $user_id = get_current_user_id();
    // 清理VM缓存
    tt_clear_cache_key_like('tt_cache_daily_vm_MeOrdersVM_user' . $user_id);

    $order = tt_get_order_by_sequence($id);
    return tt_update_order($order->order_id, array('deleted' => 1, 'deleted_by' => $user_id), array('%d', '%d'));
//    return !!$delete;
}


/**
 * 根据order_id字段删除指定订单
 *
 * @since 2.0.0
 * @param $order_id
 * @return bool|WP_Error
 */
function tt_delete_order_by_order_id($order_id){
//    global $wpdb;
//    $prefix = $wpdb->prefix;
//    $orders_table = $prefix . 'tt_orders';
//    $delete = $wpdb->delete(
//        $orders_table,
//        array('order_id' => $order_id),
//        array('%d')
//    );
//    return !!$delete;
    $user_id = get_current_user_id();
    $order = tt_get_order($order_id);
    if(!$order){
        return new WP_Error('order_not_exist', __('The order is not exist', 'tt'));
    }

    if($order->user_id != $user_id && !current_user_can('edit_users')){
        return new WP_Error('delete_order_denied', __('You are not permit to delete this order', 'tt'));
    }

    // 清理VM缓存
    tt_clear_cache_key_like('tt_cache_daily_vm_MeOrdersVM_user' . $user_id);

    return tt_update_order($order_id, array('deleted' => 1, 'deleted_by' => $user_id), array('%d', '%d'));
}


/**
 * 发送订单状态变化邮件
 *
 * @since 2.0.0
 * @param $order_id
 */
function tt_order_email($order_id) {
    if(!tt_get_option('tt_order_events_notify', true)) return;
    $order = tt_get_order($order_id);
    if(!$order) {
        return;
    }
    $user = get_user_by('id', $order->user_id);
    $order_url = tt_url_for('my_order', $order->id);

    $blog_name = get_bloginfo('name');
    $admin_email = get_option('admin_email');
    $order_status_text = tt_get_order_status_text($order->order_status);
    $subject = sprintf(__('%s 商店交易状态变更提醒', 'tt'), $blog_name);
    $args = array(
        'blogName' => $blog_name,
        'buyerName' => $user->display_name,
        'orderUrl' => $order_url,
        'adminEmail' => $admin_email,
        'productName' => $order->product_name, //TODO suborders
        'orderId' => $order_id,
        'orderTotalPrice' => $order->order_total_price,
        'orderTime' => $order->order_time,
        'orderStatusText' => $order_status_text
    );
    tt_async_mail('', $user->user_email, $subject, $args, 'order-status');  // 同一时间多封异步邮件只会发送第一封, 其他丢失
    //tt_mail('', $user->user_email, $subject, $args, 'order-status');

    // 如果有新订单创建或交易成功 发信通知管理员
    if($order->order_status == OrderStatus::WAIT_PAYMENT || $order->order_status == OrderStatus::TRADE_SUCCESS){
        $admin_subject = $order->order_status==OrderStatus::TRADE_SUCCESS ? sprintf(__('%s 商店新成功交易提醒', 'tt'), $blog_name) : sprintf(__('%s 商店新订单提醒', 'tt'), $blog_name);
        $admin_args = array(
            'blogName' => $blog_name,
            'buyerName' => $user->display_name,
            'orderUrl' => tt_url_for('manage_order', $order->id),
            'adminEmail' => $admin_email,
            'productName' => $order->product_name, //TODO suborders
            'orderId' => $order_id,
            'orderTotalPrice' => $order->order_total_price,
            'orderTime' => $order->order_time,
            'orderStatusText' => $order_status_text,
            'buyerUC' => get_author_posts_url($user->ID)
        );
        tt_mail('', $admin_email, $admin_subject, $admin_args, 'order-status-admin'); // 同一时间多封异步邮件只会发送第一封, 其他丢失
    }

    // TODO 站内消息
}
add_action('tt_order_status_change', 'tt_order_email');

/**
 * 根据订单更新商品销量和存量
 *
 * @since 2.0.0
 * @param $order_id
 */
function tt_update_order_product_quantity($order_id) {
    $order = tt_get_order($order_id);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS){
        return;
    }
    $parent_id = $order->parent_id;
    if($parent_id == -1){ // 这是一个合并订单
        $sub_orders = tt_get_sub_orders($order->id);
        $product_ids = array();
        $buy_amounts = array();
        foreach ($sub_orders as $sub_order){
            $product_ids[] = $sub_order->product_id;
            $buy_amounts[] = $sub_order->order_quantity;
        }
    }else{
        $product_ids = array($order->product_id);
        $buy_amounts = array($order->order_quantity);
    }

    foreach ($product_ids as $key => $product_id){
        // 更新存量
        $quantity = (int)get_post_meta($product_id, 'tt_product_quantity', true);
        update_post_meta($product_id, 'tt_product_quantity', max(0, $quantity-$buy_amounts[$key]));
        // 更新销量
        $sales = (int)get_post_meta($product_id, 'tt_product_sales', true);
        update_post_meta($product_id, 'tt_product_sales', $sales+$buy_amounts[$key]);
    }
}
add_action('tt_order_status_change', 'tt_update_order_product_quantity');


/**
 * 根据订单发送相应内容(付费内容,开通会员,增加积分等)
 *
 * @since 2.0.0
 * @param $order_id
 */
function tt_send_order_goods($order_id){
    $order = tt_get_order($order_id);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS){
        return;
    }

    $user_id = $order->user_id;
    $user = get_user_by('id', $user_id);
    $parent_id = $order->parent_id;
    if($parent_id == -1){ // 这是一个合并订单
        $sub_orders = tt_get_sub_orders($order->id);
        $product_ids = array();
        foreach ($sub_orders as $sub_order){
            $product_ids[] = $sub_order->product_id;
        }
    }else{
        $product_ids = array($order->product_id);
    }

    $is_embed_resource = strpos($order_id, '_') != false;

    $blog_name = get_bloginfo('name');
    foreach ($product_ids as $product_id){
        if($product_id > 0) {
            if ($is_embed_resource) {
                $pay_content = '';
                $pieces = explode('_', $order_id);
                $seq = count($pieces) > 1 ? intval($pieces[1]) : 0;
                $download_content = tt_get_post_download_content($product_id, $seq);
            } else {
                $pay_content = get_post_meta($product_id, 'tt_product_pay_content', true);
                $download_content = tt_get_product_download_content($product_id);
            }
//            if(!$pay_content || !$download_content){
//                continue;
//            }
            $subject = sprintf(__('The Resources You Bought in %s', 'tt'), $blog_name);
            $args = array(
                'blogName' => $blog_name,
                'totalPrice' => $order->order_currency == 'credit' ? sprintf(__('%d Credits', 'tt'), $order->order_total_price) : sprintf(__('%0.2f YUAN', 'tt'), $order->order_total_price),
                'payContent' => $download_content . PHP_EOL . $pay_content
            );
            // tt_async_mail('', $user->user_email, $subject, $args, 'order-pay-content');
            tt_mail('', $user->user_email, $subject, $args, 'order-pay-content');
        }elseif($product_id == Product::MONTHLY_VIP){
            tt_add_or_update_member($user_id, Member::MONTHLY_VIP);
        }elseif($product_id == Product::ANNUAL_VIP){
            tt_add_or_update_member($user_id, Member::ANNUAL_VIP);
        }elseif($product_id == Product::PERMANENT_VIP){
            tt_add_or_update_member($user_id, Member::PERMANENT_VIP);
        }elseif($product_id == Product::CREDIT_CHARGE){
            tt_add_credits_by_order($order_id);
        }else{
            // TODO more
        }
    }
}
add_action('tt_order_status_change', 'tt_send_order_goods');


/**
 * 继续完成未支付订单
 *
 * @since 2.0.0
 * @param $order_id
 * @return bool|WP_Error|WP_REST_Response
 */
function tt_continue_pay($order_id){
    $order = tt_get_order($order_id);
    if(!$order) {
        return new WP_Error('order_not_found', __('The order is not found', 'tt'), array('status' => 404));
    }

    // 如果是子订单则转向父级订单
    if($order->parent_id > 0){
        $parent_order = tt_get_order_by_sequence($order->parent_id);
        if(!$parent_order) {
            return new WP_Error('order_not_found', __('The order is not found', 'tt'), array('status' => 404));
        }
        return tt_continue_pay($parent_order->order_id);
    }

    if(in_array($order->order_status, array(OrderStatus::PAYED_AND_WAIT_DELIVERY, OrderStatus::DELIVERED_AND_WAIT_CONFIRM, OrderStatus::TRADE_SUCCESS))) {
        return new WP_Error('invalid_order_status', __('The order has been payed', 'tt'), array('status' => 200));
    }

    if($order->order_status == OrderStatus::TRADE_CLOSED) {
        return new WP_Error('invalid_order_status', __('The order has been closed', 'tt'), array('status' => 404));
    }

    if($order->order_currency == 'credit'){
        $pay = tt_credit_pay($order->order_total_price, $order->product_name, true);
        if($pay instanceof WP_Error) return $pay;
        if($pay) {
            // 更新订单支付状态和支付完成时间
            tt_update_order($order_id, array('order_success_time' => current_time('mysql'), 'order_status' => 4), array('%s', '%d')); //TODO 确保成功
            return tt_api_success('', array('data' => array(
                'orderId' => $order_id,
                'url' => add_query_arg(array('oid' => $order_id, 'spm' => wp_create_nonce('pay_result')), tt_url_for('payresult'))
                //TODO 添加积分充值链接
            )));
        }

        return new WP_Error('continue_pay_failed', __('Some error happened when continue the payment', 'tt'), array('status' => 500));
    }else{ // 现金支付
        $pay_method = tt_get_cash_pay_method();
        switch ($pay_method){
            case 'alipay':
                return tt_api_success('', array('data' => array( // 返回payment gateway url
                    'orderId' => $order_id,
                    'url' => tt_get_alipay_gateway($order_id)
                )));
            default: //qrcode
                return tt_api_success('', array('data' => array( // 直接返回扫码支付url,后面手动修改订单
                    'orderId' => $order_id,
                    'url' => tt_get_qrpay_gateway($order_id)
                )));
        }
    }
}


/**
 * 维护若干天之前的未支付订单状态
 *
 * @since 2.3.0
 * @param $days
 * @return void
 */
function tt_maintain_orders($days)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';

    $sql = sprintf("SELECT * FROM $orders_table WHERE `order_status`=%d AND `deleted`=0 AND `parent_id`=0 AND `order_time`<=DATE_ADD(CURDATE(), INTERVAL %d DAY) ORDER BY `id` DESC LIMIT %d OFFSET %d", OrderStatus::WAIT_PAYMENT, (-1) * $days, 100, 0);
    $results = $wpdb->get_results($sql);

    if ($results && count($results) > 0) {
        // TODO optimize
        foreach ($results as $result) {
            tt_update_order($result->order_id, array('order_status' => OrderStatus::TRADE_CLOSED), array('%d'));
        }
    }
}

// load_func('shop/func.Shop.Coupon');
/**
 * 创建商品系统必须的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_coupons_table() {
    global $wpdb;
    include_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    if($wpdb->has_cap('collation')) {
        if(!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if(!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }

    // 优惠券数据表
    // - id 自增ID
    // - coupon_code 优惠券号码
    // - coupon_type 优惠券类型(once/multi)
    // - coupon_status 优惠券状态(1/0)
    // - discount_value 折扣值
    // - begin_date 开始时间
    // - expire_date 到期时间
    // - unavailable_products 不可用的商品ID(逗号分隔), 优先级高于available_products
    /// - available_products 可用的商品ID(逗号分隔)
    // - unavailable_product_cats 不可用的商品分类ID(逗号分隔)
    $create_coupons_sql = "CREATE TABLE $coupons_table (id int(11) NOT NULL auto_increment,coupon_code varchar(20) NOT NULL,coupon_type varchar(20) NOT NULL default 'once',coupon_status int(11) NOT NULL default 1,discount_value double(10,2) NOT NULL default 0.90,begin_date datetime NOT NULL default '0000-00-00 00:00:00',expire_date datetime NOT NULL default '0000-00-00 00:00:00',PRIMARY KEY (id),INDEX couponcode_index(coupon_code)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($coupons_table,$create_coupons_sql);
}
add_action( 'admin_init', 'tt_install_coupons_table' );


/**
 * 添加coupon
 *
 * @since 2.0.0
 * @param $code
 * @param string $type
 * @param float $discount
 * @param $begin_date
 * @param $expire_date
 * @return bool|int|WP_Error
 */
function tt_add_coupon($code, $type = 'once', $discount = 0.90, $begin_date, $expire_date/*, $unavailable_products = '', $unavailable_product_cats = ''*/){
    if(!current_user_can('edit_users')){
        return new WP_Error('no_permission', __('You do not have the permission to add a coupon', 'tt'));
    }
    //检查code重复
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    $exist = $wpdb->get_row(sprintf("SELECT * FROM $coupons_table WHERE `coupon_code`='%s'", $code));
    if($exist){
        return new WP_Error('exist_coupon', __('The coupon code is existed', 'tt'));
    }

    $begin_date = $begin_date ? : current_time('mysql');
    $expire_date = $expire_date ? : current_time('mysql'); //TODO 默认有效期天数
    //添加记录
    $insert = $wpdb->insert(
        $coupons_table,
        array(
            'coupon_code' => $code,
            'coupon_type' => $type,
            'discount_value' => $discount,
            'begin_date' => $begin_date,
            'expire_date' => $expire_date
            //'unavailable_products' => $unavailable_products,
            //'unavailable_product_cats' => $unavailable_product_cats
        ),
        array(
            '%s',
            '%s',
            '%f',
            '%s',
            '%s',
            //'%s',
            //'%s'
        )
    );
    if($insert) {
        return $wpdb->insert_id;
    }
    return false;
}


/**
 * 删除coupon记录
 *
 * @since 2.0.0
 * @param $id
 * @return bool
 */
function tt_delete_coupon($id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    $delete = $wpdb->delete(
        $coupons_table,
        array('id' => $id),
        array('%d')
    );
    return !!$delete;
}


/**
 * 更新coupon
 *
 * @since 2.0.0
 * @param $id
 * @param $data
 * @param $format
 * @return bool
 */
function tt_update_coupon($id, $data, $format){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    $update = $wpdb->update(
        $coupons_table,
        $data,
        array('id' => $id),
        $format,
        array('%d')
    );
    return !($update===false);
}

/**
 * 根据ID查询单个优惠码
 *
 * @since 2.0.0
 * @param $id
 * @return array|null|object|void
 */
function tt_get_coupon($id) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    $coupon = $wpdb->get_row(sprintf("SELECT * FROM $coupons_table WHERE `id`=%d", $id));
    return $coupon;
}

/**
 * 获取多条coupons
 *
 * @since 2.0.0
 * @param int $limit
 * @param int $offset
 * @param bool $in_effect
 * @return array|null|object
 */
function tt_get_coupons($limit = 20, $offset = 0, $in_effect = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    if($in_effect){
        $now = new DateTime();
        $sql = sprintf("SELECT * FROM $coupons_table WHERE `coupon_status`=1 AND `begin_date`<'%s' AND `expire_date`>'%s' ORDER BY id DESC LIMIT %d OFFSET %d", $now, $now, $limit, $offset);
    }else{
        $sql = sprintf("SELECT * FROM $coupons_table ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    }
    $results = $wpdb->get_results($sql);
    return $results;
}


/**
 * 统计优惠码数量
 *
 * @since 2.0.0
 * @param $in_effect
 * @return int
 */
function tt_count_coupons($in_effect = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    if($in_effect){
        $now = new DateTime();
        $sql = sprintf("SELECT COUNT(*) FROM $coupons_table WHERE `coupon_status`=1 AND `begin_date`<'%s' AND `expire_date`>'%s'", $now, $now);
    }else{
        $sql = "SELECT COUNT(*) FROM $coupons_table";
    }
    $count = $wpdb->get_var($sql);
    return $count;
}


/**
 * 检查优惠码有效性
 *
 * @since 2.0.0
 * @param $code
 * @return object|WP_Error
 */
function tt_check_coupon($code){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $coupons_table = $prefix . 'tt_coupons';
    $coupon = $wpdb->get_row(sprintf("SELECT * FROM $coupons_table WHERE `coupon_code`='%s'", $code));
    if(!$coupon){
        return new WP_Error('coupon_not_exist', __('The coupon is not existed', 'tt'), array( 'status' => 404 ));
    }
    if(!($coupon->coupon_status)){
        return new WP_Error('coupon_used', __('The coupon is used', 'tt'), array( 'status' => 404 ));
    }
    $timestamp = time();
    if($timestamp < strtotime($coupon->begin_date)){
        return new WP_Error('coupon_not_in_effect', __('The coupon have not taken in effect yet', 'tt'), array( 'status' => 404 ));
    }
    if($timestamp > strtotime($coupon->expire_date)){
        return new WP_Error('coupon_expired', __('The coupon is expired', 'tt'), array( 'status' => 404 ));
    }
    return $coupon;
}

// load_func('shop/func.Shop.Cart');
/**
 * 获取购物车内容
 *
 * @since 2.0.0
 * @param int $user_id
 * @param bool $rest
 * @return array|bool|WP_Error
 */
function tt_get_cart($user_id = 0, $rest = false) {
    if(!$user_id) {
        $user_id = get_current_user_id();
    }
    if(!$user_id) {
        return $rest ? new WP_Error('forbidden_anonymous_request', __('You need sign in to view the shopping cart', 'tt'), 401) : false;
    }
    $meta = get_user_meta($user_id, 'tt_shopping_cart', true);
    if(!$meta) {
        return array();
    }
    $cart_items = maybe_unserialize($meta); // $cart_item{id:xxx,name:xxx,price:xxx,quantity:xxx,date:xxx,thumb:xxx,permalink:xxx}
    return $cart_items;
}


/**
 * 新增内容更新购物车
 *
 * @param $product_id
 * @param int $quantity
 * @param bool $rest
 * @return array|bool|mixed|WP_Error
 */
function tt_add_cart($product_id, $quantity = 1, $rest = false) {
    $user_id = get_current_user_id();
    if(!$user_id) {
        return $rest ? new WP_Error('forbidden_anonymous_request', __('You need sign in to update shopping cart', 'tt'), 401) : false;
    }
    $meta = get_user_meta($user_id, 'tt_shopping_cart', true);
    $items = $meta ? maybe_unserialize($meta) : array();
    $old_quantity = 0;
    foreach ($items as $key=>$item) {
        if($item['id'] == $product_id) {
            $old_quantity = intval($item['quantity']);
            array_splice($items, $key, 1);
        }
    }

    $product = get_post($product_id);
    if(!$product || intval(get_post_meta($product_id, 'tt_product_quantity', true)) < $quantity){ //TODO
        return $rest ? new WP_Error('product_not_found', __('The product you are adding to cart is not found or available', 'tt'), 404) : false;
    }

    $add = array(
        'id' => $product->ID,
        'name' => $product->post_title,
        'price' => sprintf('%0.2f', get_post_meta($product->ID, 'tt_product_price', true)),
        'quantity' => $old_quantity + $quantity,
        'thumb' => tt_get_thumb($product, array(
            'width' => 100,
            'height' => 100,
            'str' => 'thumbnail'
        )),
        'permalink' => get_permalink($product),
        'time' => time()
    );

    array_push($items, $add);

    $update = update_user_meta($user_id, 'tt_shopping_cart', maybe_serialize($items));
    return $items;
}


/**
 * 删除购物车内容
 *
 * @since 2.0.0
 * @param $product_id
 * @param int $minus_quantity
 * @param bool $rest
 * @return array|bool|mixed|WP_Error
 */
function tt_delete_cart($product_id, $minus_quantity = 1, $rest = false) {
    $user_id = get_current_user_id();
    if(!$user_id) {
        return $rest ? new WP_Error('forbidden_anonymous_request', __('You need sign in to update shopping cart', 'tt'), 401) : false;
    }
    $meta = get_user_meta($user_id, 'tt_shopping_cart', true);
    $items = $meta ? maybe_unserialize($meta) : array();
    $new_quantity = 0;
    foreach ($items as $key=>$item) {
        if($item['id'] == $product_id) {
            $old_quantity = intval($item['quantity']);
            $new_quantity = $old_quantity - $minus_quantity;
            array_splice($items, $key, 1);
        }
    }

    if($new_quantity > 0){
        $product = get_post($product_id);
        if(!$product){
            return $rest ? new WP_Error('product_not_found', __('The product you are adding to cart is not found or available', 'tt'), 404) : false;
        }
        $add = array(
            'id' => $product->ID,
            'name' => $product->post_title,
            'price' => sprintf('%0.2f', get_post_meta($product->ID, 'tt_product_price', true)),
            'quantity' => $new_quantity,
            'thumb' => tt_get_thumb($product, array(
                'width' => 100,
                'height' => 100,
                'str' => 'thumbnail'
            )),
            'permalink' => get_permalink($product),
            'time' => time()
        );
        array_push($items, $add);
    }

    $update = update_user_meta($user_id, 'tt_shopping_cart', maybe_serialize($items));

    return $items;
}


/**
 * 清空购物车
 *
 * @since 2.0.0
 * @param bool $rest
 * @return array|bool|WP_Error
 */
function tt_clear_cart($rest = false) {
    $user_id = get_current_user_id();
    if(!$user_id) {
        return $rest ? new WP_Error('forbidden_anonymous_request', __('You need sign in to update shopping cart', 'tt'), 401) : false;
    }

    $update = update_user_meta($user_id, 'tt_shopping_cart', '');

    return $rest ? array() : true;
}

// load_func('shop/func.Shop.Address');
/**
 * 创建管理维护用户地址联系信息的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_addresses_table() {
    global $wpdb;
    include_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    if($wpdb->has_cap('collation')) {
        if(!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if(!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    // 订单数据表
    // - id 自增ID
    // - user_id 用户ID
    /// - user_name 用户名
    /// - user_email 用户邮箱
    /// - user_address 用户地址
    /// - user_zip 用户邮编
    //// - user_phone 用户电话
    /// - user_cellphone 用户手机
    $create_orders_sql = "CREATE TABLE $addresses_table (id int(11) NOT NULL auto_increment,user_id int(11) NOT NULL DEFAULT 0,user_name varchar(60),user_email varchar(100),user_address varchar(250),user_zip varchar(10),user_cellphone varchar(20),PRIMARY KEY (id),INDEX uid_index(user_id)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($addresses_table, $create_orders_sql);
}
add_action( 'admin_init', 'tt_install_addresses_table' );


/**
 * 根据地址ID获取地址记录
 *
 * @since 2.0.0
 * @param $address_id
 * @return array|null|object|void
 */
function tt_get_address($address_id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $addresses_table WHERE `id`=%d", $address_id));
    return $row;
}


/**
 * 添加地址记录
 *
 * @since 2.0.0
 * @param $name
 * @param $address
 * @param $cellphone
 * @param string $zip
 * @param string $email
 * @param int $user_id
 * @return bool|int
 */
function tt_add_address($name, $address, $cellphone, $zip = '', $email = '', $user_id = 0){
    $user = $user_id ? get_user_by('ID', $user_id) : wp_get_current_user();
    if(!$user->ID){
        return false;
    }
    $email = $email ? : $user->user_email;
    $name = $name ? : $user->display_name;
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    //$query = $wpdb->query(sprintf("INSERT INTO %s (user_id, user_name, user_email, user_address, user_zip, user_cellphone) VALUES (%d, %s, %s, %s, %s, %s)", $addresses_table, $user->ID, $name, $email, $address, $zip, $cellphone));
    $insert = $wpdb->insert(
        $addresses_table,
        array(
            'user_id' => $user->ID,
            'user_name' => $name,
            'user_email' => $email,
            'user_address' => $address,
            'user_zip' => $zip,
            'user_cellphone' => $cellphone
        ),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );
    if($insert) {
        return $wpdb->insert_id;
    }
    return false;
}


/**
 * 删除地址记录
 *
 * @since 2.0.0
 * @param $id
 * @return bool
 */
function tt_delete_address($id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    $delete = $wpdb->delete(
        $addresses_table,
        array('id' => $id),
        array('%d')
    );
    return !!$delete;
}


/**
 * 更新地址记录
 *
 * @since 2.0.0
 * @param $id
 * @param $data
 * @return bool
 */
function tt_update_address($id, $data){ // $data must be array( 'column1' => 'value1', 'column2' => 'value2') type
    $count = count($data);
    $format = array();
    for ($i=0; $i<$count; $i++){
        $format[] = '%s';
    }
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    $update = $wpdb->update(
        $addresses_table,
        $data,
        array('id' => $id),
        $format,
        array('%d')
    );
    return !($update===false);
}


/**
 * 获取用户的所有地址信息
 *
 * @since 2.0.0
 * @param int $user_id
 * @return array|null|object
 */
function tt_get_addresses($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    $results = $wpdb->get_results(sprintf("SELECT * FROM $addresses_table WHERE `user_id`=%d", $user_id));
    return $results;
}


/**
 * 获取默认地址
 *
 * @since 2.0.0
 * @param int $user_id
 * @return array|null|object|void
 */
function tt_get_default_address($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    $default_address_id = (int)get_user_meta($user_id, 'tt_default_address_id', true);
    global $wpdb;
    $prefix = $wpdb->prefix;
    $addresses_table = $prefix . 'tt_addresses';
    if($default_address_id){
        $row = $wpdb->get_row(sprintf("SELECT * FROM $addresses_table WHERE `id`=%d", $default_address_id));
    }else{
        $row = $wpdb->get_row(sprintf("SELECT * FROM $addresses_table WHERE `user_id`=%d ORDER BY `id` DESC LIMIT 1 OFFSET 0", $user_id));
    }
    return $row;
}

// load_func('shop/func.Shop.Schedule');
/**
 * 每天00:00检查过期订单
 *
 * @since 2.3.0
 */
function tt_orders_maintenance_setup_schedule() {
    if ( ! wp_next_scheduled( 'tt_orders_maintenance_daily_event' ) ) {
        wp_schedule_event( '1193875200', 'daily', 'tt_orders_maintenance_daily_event');
    }
}
add_action( 'wp', 'tt_orders_maintenance_setup_schedule' );

/**
 * 订单状态维护定时任务回调函数
 *
 * @since 2.3.0
 */
function tt_orders_maintenance_do_this_daily() {
    $maintain_days = (int)tt_get_option('tt_maintain_orders_deadline', 0);
    if ($maintain_days < 1) {
        return;
    }

    tt_maintain_orders($maintain_days);
}
add_action( 'tt_orders_maintenance_daily_event', 'tt_orders_maintenance_do_this_daily' );

load_func('shop/alipay/func.Alipay');
