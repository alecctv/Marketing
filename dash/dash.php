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

/* 后台编辑器预览样式 */
add_editor_style(THEME_ASSET.'/dash/css/editor-preview.css');

/* 后台编辑器强化 */
function tt_add_more_buttons($buttons){
	$buttons[] = 'fontsizeselect';
	$buttons[] = 'styleselect';
	$buttons[] = 'fontselect';
	$buttons[] = 'hr';
	$buttons[] = 'sub';
	$buttons[] = 'sup';
	$buttons[] = 'cleanup';
	$buttons[] = 'image';
	$buttons[] = 'code';
	$buttons[] = 'media';
	$buttons[] = 'backcolor';
	$buttons[] = 'visualaid';
	return $buttons;
}
add_filter("mce_buttons_3", "tt_add_more_buttons");


/**
 * 后台编辑器文本模式添加短代码快捷输入按钮
 */
function tt_editor_quicktags() {
    wp_enqueue_script('my_quicktags', THEME_ASSET . '/dash/js/my_quicktags.js', array('quicktags')); // TODO version
}
add_action('admin_print_scripts', 'tt_editor_quicktags');

/**
 * 添加Admin bar项目
 *
 * @since   2.0.0
 * @param   WP_Admin_Bar  $wp_admin_bar
 */
function tt_clear_cache_on_admin_menu_bar( $wp_admin_bar ) {
    if (!current_user_can('administrator')) {
        return;
    }
    $args = array(
        'id'    => 'tt_admin_menu_bar_clear_cache',
        'title' => __('Clear Cache', 'tt'),
        'parent' => false,
        'href'  => wp_nonce_url( admin_url( 'admin.php?page=options-framework&tint_cache_empty=1' ), 'tt_clear_cache', 'tt_clear_cache_nonce' ),
        'meta'  => array( 'class' => 'tt-clear-cache' )
    );
    $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'tt_clear_cache_on_admin_menu_bar', 999 );

function tt_clear_cache_callback_on_admin_menu_bar(){
    if(isset($_GET['tt_clear_cache_nonce']) && wp_verify_nonce($_GET['tt_clear_cache_nonce'], 'tt_clear_cache')) {
        if(isset($_GET['tint_cache_empty']) && $_GET['tint_cache_empty']==1) {
            tt_clear_all_cache();
            add_settings_error( 'options-framework', 'tt_clear_cache', __( 'All Cache Clear.', 'tt' ), 'updated fade' );
        }
    }
}
//add_action('optionsframework_after', 'tt_clear_cache_callback_on_admin_menu_bar', 999);
add_action('admin_init', 'tt_clear_cache_callback_on_admin_menu_bar', 999);

// 检测主题是否有更新
function tt_update_on_admin_menu_bar( $wp_admin_bar ) {
    if (!current_user_can('administrator')) {
        return;
    }
    $args = array(
        'id'    => 'tt_admin_menu_bar_clear_update',
        'title' => __('检测主题更新', 'tt'),
        'parent' => false,
        'href'  => wp_nonce_url( admin_url( 'admin.php?page=options-framework&tint_update_empty=1' ), 'tt_clear_update', 'tt_clear_update_nonce' ),
        'meta'  => array( 'class' => 'tt-clear-cache' )
    );
    $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'tt_update_on_admin_menu_bar', 999 );

function tt_clear_update_callback_on_admin_menu_bars(){
    if(isset($_GET['tt_clear_update_nonce']) && wp_verify_nonce($_GET['tt_clear_update_nonce'], 'tt_clear_update')) {
        if(isset($_GET['tint_update_empty']) && $_GET['tint_update_empty']==1) {

            if ( tt_new_theme_version() ) {
                add_settings_error( 'options-framework', 'tt_clear_update', __( '有新版本更新，请到<a target="_blank" href="https://yunsheji.cc/">官网</a>下载', 'tt' ), 'updated fade' );
            } else {
                add_settings_error( 'options-framework', 'tt_clear_update', __( '当前主题为最新版，请尽情享用！', 'tt' ), 'updated fade' );
            }
            
        }
    }
}
//add_action('optionsframework_after', 'tt_clear_cache_callback_on_admin_menu_bar', 999);
add_action('admin_init', 'tt_clear_update_callback_on_admin_menu_bars', 999);



/**
 * 后台用户列表显示昵称
 *
 * @since 2.0.0
 * @param $columns
 * @return mixed
 */
function tt_display_name_column( $columns ) {
    $columns['tt_display_name'] = __('Display Name', 'tt');
    unset($columns['name']);
    return $columns;
}
add_filter( 'manage_users_columns', 'tt_display_name_column' );

function tt_display_name_column_callback( $value, $column_name, $user_id ) {

    if( 'tt_display_name' == $column_name ){
        $user = get_user_by( 'id', $user_id );
        $value = ( $user->display_name ) ? $user->display_name : '';
    }

    return $value;
}
add_action( 'manage_users_custom_column', 'tt_display_name_column_callback', 10, 3 );


/**
 * 后台用户列表显示最近登录时间
 *
 * @since 2.0.0
 * @param $columns
 * @return mixed
 */
function tt_latest_login_column( $columns ) {
    $columns['tt_latest_login'] = __('Last Login', 'tt');
    return $columns;
}
add_filter( 'manage_users_columns', 'tt_latest_login_column' );

function tt_latest_login_column_callback( $value, $column_name, $user_id ) {
    if('tt_latest_login' == $column_name){
        $value = get_user_meta($user_id, 'tt_latest_login', true) ? : __('No Record','tt');
    }
    return $value;
}
add_action( 'manage_users_custom_column', 'tt_latest_login_column_callback', 10, 3 );


/**
 * 后台页脚
 *
 * @since 2.0.0
 * @param $text
 * @return string
 */
function left_admin_footer_text($text) {
    $text = sprintf(__('<span id="footer-thankyou">Thanks for using %s to help your creation, %s theme style your website</span>', 'tt'), '<a href=http://cn.wordpress.org/ >WordPress</a>', '<a href="https://yunsheji.cc">Marketing</a>');
    return $text;
}
add_filter('admin_footer_text','left_admin_footer_text');


/**
 * 增加用户资料字段
 *
 * @since 2.0.0
 * @param array $contactmethods
 * @return array
 */
function tt_add_contact_fields($contactmethods){
    $contactmethods['tt_qq'] = 'QQ';
    $contactmethods['tt_weibo'] = __('Sina Weibo','tt');
    $contactmethods['tt_weixin'] = __('Wechat','tt');
    $contactmethods['tt_twitter'] = __('Twitter','tt');
    $contactmethods['tt_facebook'] = 'Facebook';
    $contactmethods['tt_googleplus'] = 'Google+';
    $contactmethods['tt_alipay_email'] = __('Alipay Account','tt');
    $contactmethods['tt_alipay_pay_qr'] = __('Alipay Pay Qrcode','tt');
    $contactmethods['tt_wechat_pay_qr'] = __('Wechat Pay Qrcode','tt');

    // 删除无用字段
    unset($contactmethods['yim']);
    unset($contactmethods['aim']);
    unset($contactmethods['jabber']);

    return $contactmethods;
}
add_filter('user_contactmethods', 'tt_add_contact_fields');


/**
 * 对非管理员隐藏公告和商品的链接
 */
function tt_remove_menu_items() {
    if( !current_user_can( 'administrator' ) ) {
        remove_menu_page( 'edit.php?post_type=bulletin' );
        remove_menu_page( 'edit.php?post_type=product' );
    }
}
add_action( 'admin_menu', 'tt_remove_menu_items' );
/**
 * 查看评论限制，只有管理员和编辑才能够查看所有的评论
 */
function tt_get_comment_list_by_user($clauses) {
    if (is_admin()) {
        global $user_ID, $wpdb;
        $clauses['join'] = ", wp_posts";
        $clauses['where'] .= " AND wp_posts.post_author = ".$user_ID." AND wp_comments.comment_post_ID = wp_posts.ID";
    };
    return $clauses;
};
if(!current_user_can('edit_others_posts')) {
    add_filter('comments_clauses', 'tt_get_comment_list_by_user');
}

/**
 * 对于非管理员，定制的后台
 */
function tt_admin_bar() {
    if (current_user_can( 'publish_posts' ) && !current_user_can( 'publish_pages' )) {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('new-content'); // 移除“新建”
        $wp_admin_bar->remove_menu('search');  //移除搜索
        $wp_admin_bar->remove_menu('updates'); //移除升级通知
    }
}
add_action( 'wp_before_admin_bar_render', 'tt_admin_bar' );