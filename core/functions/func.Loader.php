<?php
/**
 *
 * @since 1.1.0
 * @package Marketing
 * @author 云设计
 * @date 2018/02/14 10:00
 * @link https://yunsheji.cc
 */
?>
<?php

/* 引入常量 */
require_once 'Constants.php';

/* 设置默认时区 */
date_default_timezone_set('PRC');

if (!function_exists('load_dash')) {
    function load_dash($path)
    {
        load_template(THEME_DIR . '/dash/' . $path . '.php');
    }
}

if (!function_exists('load_api')) {
    function load_api($path)
    {
        load_template(THEME_DIR . '/core/api/' . $path . '.php');
    }
}

if (!function_exists('load_class')) {
    function load_class($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/classes/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/classes/' . $path . '.php');
        }
    }
}

if (!function_exists('load_func')) {
    function load_func($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/functions/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/functions/' . $path . '.php');
        }
    }
}

if (!function_exists('load_mod')) {
    function load_mod($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/modules/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/modules/' . $path . '.php');
        }
    }
}

if (!function_exists('load_tpl')) {
    function load_tpl($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/templates/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/templates/' . $path . '.php');
        }
    }
}

if (!function_exists('load_widget')) {
    function load_widget($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/modules/widgets/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/modules/widgets/' . $path . '.php');
        }
    }
}

if (!function_exists('load_vm')) {
    function load_vm($path, $safe = false)
    {
        if ($safe) {
            @include_once (THEME_DIR . '/core/viewModels/' . $path . '.php');
        }
        else {
            load_template(THEME_DIR . '/core/viewModels/' . $path . '.php');
        }
    }
}

/* 载入option_framework */
load_dash('of_inc/options-framework');

/* 载入主题选项 */
load_dash('options');

defined('THEME_CDN_ASSET') || define('THEME_CDN_ASSET', of_get_option('tt_tint_static_cdn_path', THEME_ASSET));

/* 调试模式选项保存为全局变量 */
defined('TT_DEBUG') || define('TT_DEBUG', of_get_option('tt_theme_debug', false));
if (TT_DEBUG) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL);
}
else {
    ini_set("display_errors", "Off");
}

/* 载入后台相关处理逻辑 */
//if( is_admin() ){
load_dash('dash');
//}

/* 载入REST API功能控制函数 */
load_api('api.Config');

/* 载入功能函数 */

// load_func('func.L10n');
/**
 * 载入语言包
 *
 * @since 2.0.0
 */
function tt_load_languages(){
    load_theme_textdomain( 'tt', THEME_DIR . '/core/languages');
}
add_action( 'after_setup_theme', 'tt_load_languages');

/**
 * 选择本地化语言
 *
 * @since 2.0.0
 */
function tt_theme_l10n(){
    return tt_get_option( 'tt_l10n', 'zh_CN');
}
add_filter('locale','tt_theme_l10n');

// load_func('func.Account');
/**
 * 生成密码重置链接
 *
 * @since   2.0.0
 *
 * @param   string  $email
 * @param   int     $user_id
 * @return  string
 */
function tt_generate_reset_password_link($email, $user_id = 0) {
    $base_url = tt_url_for('resetpass');

    if(!$user_id){
        $user_id = get_user_by('email', $email)->ID;
    }

    $data = array(
        'id' => $user_id,
        'email' =>  $email
    );

    $key = base64_encode(tt_authdata($data, 'ENCODE', tt_get_option('tt_private_token'), 60*10)); // 10分钟有效期

    $link = add_query_arg('key', $key, $base_url);
    return $link;
}


/**
 * 验证密码重置链接包含的key
 *
 * @since   2.0.0
 *
 * @param   string  $key
 * @return  bool
 */
function tt_verify_reset_password_link($key) {
    if(empty($key)) return false;
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if(!$data || !is_array($data) || !isset($data['id']) || !isset($data['email'])){
        return false;
    }

    return true;
}


/**
 * 通过key进行密码重置
 *
 * @since   2.0.0
 *
 * @param   string  $key
 * @param   string  $new_pass
 * @return  WP_User | WP_Error
 */
function tt_reset_password_by_key($key, $new_pass) {
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if(!$data || !is_array($data) || !isset($data['id']) || !isset($data['email'])){
        return new WP_Error( 'invalid_key', __( 'The key is invalid.', 'tt' ), array( 'status' => 400 ) );
    }

    $user = get_user_by('id', (int)$data['id']);
    if(!$user){
        return new WP_Error( 'user_not_found', __( 'Sorry, the user was not found.', 'tt' ), array( 'status' => 404 ) );
    }

    reset_password($user, $new_pass);
    return $user;
}


/**
 * 生成包含注册信息的激活链接
 *
 * @since   2.0.0
 * @param   string  $username
 * @param   string  $email
 * @param   string  $password
 * @return  string
 */
function tt_generate_registration_activation_link ($username, $email, $password) {
    $base_url = tt_url_for('activate');

    $data = array(
        'username' => $username,
        'email' =>  $email,
        'password' => $password
    );

    $key = base64_encode(tt_authdata($data, 'ENCODE', tt_get_option('tt_private_token'), 60*10)); // 10分钟有效期

    $link = add_query_arg('key', $key, $base_url);

    return $link;
}


/**
 * 验证并激活注册信息的链接中包含的key
 *
 * @since   2.0.0
 *
 * @param   string  $key
 * @return  array | WP_Error
 */
function tt_activate_registration_from_link($key) {
    if(empty($key)) {
        return new WP_Error( 'invalid_key', __( 'The registration activation key is invalid.', 'tt' ), array( 'status' => 400 ) );
    }
    $data = tt_authdata(base64_decode($key), 'DECODE', tt_get_option('tt_private_token'));
    if(!$data || !is_array($data) || !isset($data['username']) || !isset($data['email']) || !isset($data['password'])){
        return new WP_Error( 'invalid_key', __( 'The registration activation key is invalid.', 'tt' ), array( 'status' => 400 ) );
    }

    // 开始激活(实际上在激活之前用户信息并没有插入到数据库中，为了防止恶意注册)
    $userdata = array(
        'user_login' => $data['username'],
        'user_email' => $data['email'],
        'user_pass' => $data['password']
    );
    $user_id = wp_insert_user($userdata);
    if(is_wp_error($user_id)) {
        return $user_id;
    }

    $result = array(
        'success' => 1,
        'message' => __('Activate the registration successfully', 'tt'),
        'data' => array(
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $user_id
        )
    );

    // 发送激活成功与注册欢迎信
    $blogname = get_bloginfo('name');
    // 给注册用户
    tt_async_mail('', $data['email'], sprintf(__('欢迎加入[%s]', 'tt'), $blogname), array('loginName' => $data['username'], 'password' => $data['password'], 'loginLink' => tt_url_for('signin')), 'register');
    // 给管理员
    tt_async_mail('', get_option('admin_email'), sprintf(__('您的站点「%s」有新用户注册 :', 'tt'), $blogname), array('loginName' => $data['username'], 'email' => $data['email'], 'ip' => $_SERVER['REMOTE_ADDR']), 'register-admin');

    return $result;
}


/**
 * 更改默认的登录链接
 *
 * @since   2.0.0
 *
 * @param   string  $login_url
 * @param   string  $redirect
 * @return  string
 */
function tt_filter_default_login_url($login_url, $redirect) {
    $login_url = tt_url_for('signin');

    if ( !empty($redirect) ) {
        $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }

    return $login_url;
}
add_filter('login_url', 'tt_filter_default_login_url', 10, 2);


/**
 * 更改默认的注销链接
 *
 * @since   2.0.0
 *
 * @param   string  $logout_url
 * @param   string  $redirect
 * @return  string
 */
function tt_filter_default_logout_url($logout_url, $redirect) {
    $logout_url = tt_url_for('signout');

    if ( !empty($redirect) ) {
        $logout_url = add_query_arg('redirect_to', urlencode($redirect), $logout_url);
    }

    return $logout_url;
}
add_filter('logout_url', 'tt_filter_default_logout_url', 10, 2);


/**
 * 更改默认的注册链接
 *
 * @since   2.0.0
 *
 * @return  string
 */
function tt_filter_default_register_url() {
    return tt_url_for('signup');
}
add_filter('register_url', 'tt_filter_default_register_url');


/**
 * 更改找回密码邮件中的内容
 *
 * @since 2.0.0
 * @param $message
 * @param $key
 * @return string
 */
function tt_reset_password_message( $message, $key ) {
    if ( strpos($_POST['user_login'], '@') ) {
        $user_data = get_user_by('email', trim($_POST['user_login']));
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    $user_login = $user_data->user_login;
    $reset_link = network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user_login), 'login') ;

    $templates = new League\Plates\Engine(THEME_TPL . '/plates/emails');
    return $templates->render('findpass', array('home' => home_url(), 'userLogin' => $user_login, 'resetPassLink' => $reset_link));
}
add_filter('retrieve_password_message', 'tt_reset_password_message', null, 2);


/**
 * 更新基本资料(个人设置)
 *
 * @since 2.0.0
 * @param $user_id
 * @param $avatar_type
 * @param $nickname
 * @param $site
 * @param $description
 * @return array|WP_Error
 */
function tt_update_basic_profiles($user_id, $avatar_type, $nickname, $site, $description){
    $data = array(
        'ID' => $user_id,
        'user_url' => $site, //可空
        'description' => $description // 可空
    );
    if(!empty($nickname)) {
        $data['nickname'] = $nickname;
        $data['display_name'] = $nickname;
    }
    $update = wp_update_user($data);//If successful, returns the user_id, otherwise returns a WP_Error object.

    if($update instanceof WP_Error) {
        return $update;
    }

    if(!in_array($avatar_type, Avatar::$_avatarTypes)) {
        $avatar_type = Avatar::LETTER_AVATAR;
    }
    update_user_meta($user_id, 'tt_avatar_type', $avatar_type);

    //删除缓存
    tt_clear_avatar_related_cache($user_id);

    return array(
        'success' => true,
        'message' => __('Update basic profiles successfully', 'tt')
    );
}


/**
 * 更新扩展资料
 *
 * @since 2.0.0
 * @param $data
 * @return array|int|WP_Error
 */
function tt_update_extended_profiles($data){
    $update = wp_update_user($data);//If successful, returns the user_id, otherwise returns a WP_Error object.

    if($update instanceof WP_Error) {
        return $update;
    }

    //删除VM缓存
    tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user' . $data['ID']);
    tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author_' . $data['ID']);

    return array(
        'success' => true,
        'message' => __('Update extended profiles successfully', 'tt')
    );
}


/**
 * 更新安全资料
 *
 * @since 2.0.0
 * @param $data
 * @return array|int|WP_Error
 */
function tt_update_security_profiles($data){
    $update = wp_update_user($data);//If successful, returns the user_id, otherwise returns a WP_Error object.

    if($update instanceof WP_Error) {
        return $update;
    }

    //删除VM缓存
    tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user' . $data['ID']);
    tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author_' . $data['ID']);

    return array(
        'success' => true,
        'message' => __('Update security profiles successfully', 'tt')
    );
}

// load_func('func.Avatar');
require_once THEME_CLASS . '/class.Avatar.php';
// require_once 'func.Cache.php';

/**
 * 获取头像
 *
 * @since   2.0.0
 * @param   int | string | object   $id_or_email    用户ID或Email或用户实例对象
 * @param   int | string    $size                   头像尺寸
 * @return  string
 */
function tt_get_avatar($id_or_email, $size='medium'){
//    $callback = function () use ($id_or_email, $size) {
//        return (new Avatar($id_or_email, $size))->getAvatar();
//    };
//    return tt_cached((new Avatar($id_or_email, $size))->cache_key, $callback, 'avatar', 60*60*24);
    $instance = new Avatar($id_or_email, $size);
    if($cache = get_transient($instance->cache_key)) {
        return $cache;
    }
    return $instance->getAvatar();
}


/**
 * 清理Avatar transient缓存
 *
 * @since   2.0.0
 * @return  void
 */
//function tt_daily_clear_avatar_cache(){
//    // transient的avatar缓存在get_transient函数调用时会自动判断过期并决定是否删除，对于每个avatar url查询请求执行两次delete_option操作
//    // 这里采用定时任务一次性删除多条隔天的过期缓存，减少delete_option操作
//    global $wpdb;
//    $wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_daily_avatar_%' OR `option_name` LIKE '_transient_timeout_tt_cache_daily_avatar_%'" );
//}
//add_action('tt_setup_common_daily_event', 'tt_daily_clear_avatar_cache');


/**
 * 删除头像缓存以及包含头像的多处缓存数据
 *
 * @since 2.0.0
 * @param $user_id
 */
function tt_clear_avatar_related_cache($user_id){
    //删除VM缓存
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    //删除头像缓存
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'small' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'medium' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'large' . Utils::getCurrentDateTimeStr('day')));
}

// load_func('func.Cache');
/**
 * Cache 封装
 *
 * @since   2.0.0
 *
 * @access  public
 * @param   string      $key        缓存键
 * @param   callable    $miss_cb    未命中缓存时的回调函数
 * @param   string      $group      缓存数据分组
 * @param   int         $expire     缓存时间，单位为秒
 * @return  mixed
 */
function tt_cached($key, $miss_cb, $group, $expire){
    // 无memcache等对象缓存组件时，使用临时的数据表缓存，只支持string|int内容 // 实际上get_transient|set_transient会自动判断有无wp-content下的object-cache.php， 但是直接使用该函数不支持group
    // https://core.trac.wordpress.org/browser/tags/4.5.3/src/wp-includes/option.php#L609
    if(tt_get_option('tt_object_cache', 'none')=='none' && !TT_DEBUG){
        $data = get_transient($key);
        if($data!==false){
            return $data;
        }
        if(is_callable($miss_cb)){
            $data = call_user_func($miss_cb);
            if(is_string($data) || is_int($data)) set_transient($key, $data, $expire);
            return $data;
        }
        return false;
    }
    // 使用memcache或redis内存对象缓存
    elseif(in_array(tt_get_option('tt_object_cache', 'none'), array('memcache', 'redis')) && !TT_DEBUG){
        $data = wp_cache_get($key, $group);
        if($data!==false){
            return $data;
        }
        if(is_callable($miss_cb)){
            $data = call_user_func($miss_cb);
            wp_cache_set($key, $data, $group, $expire);
            return $data;
        }
        return false;
    }
    return is_callable($miss_cb) ? call_user_func($miss_cb) : false;
}


/**
 * 定时清理大部分缓存(每小时)
 *
 * @since   2.0.0
 * @return  void
 */
function tt_cache_flush_hourly(){
    // Object Cache
    wp_cache_flush();

    // Transient cache
    // transient的缓存在get_transient函数调用时会自动判断过期并决定是否删除，对于每个查询请求执行两次delete_option操作
    // 这里采用定时任务一次性删除多条隔天的过期缓存，减少delete_option操作
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_hourly_%' OR `option_name` LIKE '_transient_timeout_tt_cache_hourly%'" );
}
add_action('tt_setup_common_hourly_event', 'tt_cache_flush_hourly');


/**
 * 定时清理大部分缓存(每天执行)
 *
 * @since   2.0.0
 * @return  void
 */
function tt_cache_flush_daily(){
    // Rewrite rules Cache
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    // Transient cache
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_daily_%' OR `option_name` LIKE '_transient_timeout_tt_cache_daily_%'" );
}
add_action('tt_setup_common_daily_event', 'tt_cache_flush_daily');


/**
 * 定时清理大部分缓存(每周)
 *
 * @since   2.0.0
 * @return  void
 */
function tt_cache_flush_weekly(){
    // Object Cache
    wp_cache_flush();

    // Transient cache
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_weekly_%' OR `option_name` LIKE '_transient_timeout_tt_cache_weekly%'" );
}
add_action('tt_setup_common_weekly_event', 'tt_cache_flush_weekly');  // TODO rest api cache


/**
 * 清除所有缓存
 *
 * @since   2.0.0
 * @return  void
 */
function tt_clear_all_cache() {
    // Object Cache
    wp_cache_flush();

    // Rewrite rules Cache
    global $wp_rewrite;
    $wp_rewrite->flush_rules();

    // Transient cache
    // transient的缓存在get_transient函数调用时会自动判断过期并决定是否删除，对于每个查询请求执行两次delete_option操作
    // 这里采用定时任务一次性删除多条隔天的过期缓存，减少delete_option操作
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_%' OR `option_name` LIKE '_transient_timeout_tt_cache_%'" );
}


/**
 * 模糊匹配键值删除transient的缓存
 *
 * @since 2.0.0
 * @param $key
 */
function tt_clear_cache_key_like($key) {
    if(wp_using_ext_object_cache()) {
        return; //object cache无法模糊匹配key
    }
    global $wpdb;
    $like1 = '_transient_' . $key . '%';
    $like2 = '_transient_timeout_' . $key . '%';
    $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` LIKE %s OR `option_name` LIKE %s", $like1, $like2) );
}


/**
 * 精确匹配键值删除transient的缓存(包括Object Cache)
 *
 * @since 2.0.0
 * @param $key
 */
function tt_clear_cache_by_key($key) { //use delete_transient
    if(wp_using_ext_object_cache()){
        wp_cache_delete($key, 'transient'); // object cache是由set_transient时设置的, group为transient
    }else{
        global $wpdb;
        $key1 = '_transient_' . $key;
        $key2 = '_transient_timeout_' . $key;
        $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` IN ('%s','%s')", $key1, $key2) );
    }
}



/**
 * 预读取菜单时寻找缓存
 *
 * @since   2.0.0
 * @param   string  $menu   导航菜单
 * @param   array   $args   菜单参数
 * @return  string
 */
function tt_cached_menu($menu, $args){
    if(TT_DEBUG) {
        return $menu;
    }

    global $wp_query;
    // 即使相同菜单位但是不同页面条件时菜单输出有细微区别，如当前active的子菜单, 利用$wp_query->query_vars_hash予以区分
    $cache_key = CACHE_PREFIX . '_hourly_nav_' . md5($args->theme_location . '_' . $wp_query->query_vars_hash);
    $cached_menu = get_transient($cache_key); //TODO： 尝试Object cache
    if($cached_menu !== false){
        return $cached_menu;
    }
    return $menu;
}
add_filter('pre_wp_nav_menu', 'tt_cached_menu', 10, 2);


/**
 * 读取菜单完成后设置缓存(缓存命中的菜单读取不会触发该动作)
 *
 * @since   2.0.0
 *
 * @param   string  $menu   导航菜单
 * @param   array   $args   菜单参数
 * @return  string
 */
function tt_cache_menu($menu, $args){
    if(TT_DEBUG) {
        return $menu;
    }

    global $wp_query;
    $cache_key = CACHE_PREFIX . '_hourly_nav_' . md5($args->theme_location . '_' . $wp_query->query_vars_hash);
    set_transient($cache_key, sprintf(__('<!-- Nav cached %s -->', 'tt'), current_time('mysql')) . $menu . __('<!-- Nav cache end -->', 'tt'), 3600);
    return $menu;
}
add_filter('wp_nav_menu', 'tt_cache_menu', 10 ,2);


/**
 * 设置更新菜单时主动删除缓存
 *
 */
function tt_delete_menu_cache(){
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE '_transient_tt_cache_hourly_nav_%' OR `option_name` LIKE '_transient_timeout_tt_cache_hourly_nav_%'");

    //TODO: 如果使用object cache则wp_cache_flush()
    if(wp_using_ext_object_cache()){
        wp_cache_flush();
    }
}
add_action('wp_update_nav_menu', 'tt_delete_menu_cache');

//TODO 其他工具利用apply_filters do_action add_filter add_action调用或生成或删除缓存

/**
 * 文章点赞或取消赞时删除对应缓存
 *
 * @since   2.0.0
 * @param   int $post_ID
 * @return  void
 */
function tt_clear_cache_for_stared_or_unstar_post($post_ID) {
    $cache_key = 'tt_cache_daily_vm_SinglePostVM_post' . $post_ID;
    delete_transient($cache_key);
}
add_action('tt_stared_post', 'tt_clear_cache_for_stared_or_unstar_post', 10 , 1);
add_action('tt_unstared_post', 'tt_clear_cache_for_stared_or_unstar_post', 10, 1);


/**
 * 文章点赞或取消赞时删除对应用户的UC Stars缓存
 *
 * @since   2.0.0
 * @param   int $post_ID
 * @param   int $author_id
 * @return  void
 */
function tt_clear_cache_for_uc_stars($post_ID, $author_id) {
    $cache_key = 'tt_cache_daily_vm_UCStarsVM_author' . $author_id . '_page'; //模糊键值
    //delete_transient($cache_key);
    tt_clear_cache_key_like($cache_key);
    tt_clear_cache_by_key($cache_key . '1');
}
add_action('tt_stared_post', 'tt_clear_cache_for_uc_stars', 10 , 2);
add_action('tt_unstared_post', 'tt_clear_cache_for_uc_stars', 10, 2);


/**
 * 订单状态变更时删除相关缓存
 *
 * @since   2.0.0
 * @param   int $order_id
 * @return  void
 */
function tt_clear_cache_for_order_relates($order_id) {
    $order = tt_get_order($order_id);
    if(!$order) return;

    //Product VM
    delete_transient(sprintf('tt_cache_daily_vm_ShopProductVM_product%1$s_user%2$s', $order->product_id, $order->user_id));
    //Order Detail VM
    delete_transient(sprintf('tt_cache_daily_vm_MeOrderVM_user%1$s_seq%2$s', $order->user_id, $order->id));
    //Orders VM
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typeall', $order->user_id));
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typecash', $order->user_id));
    delete_transient(sprintf('tt_cache_daily_vm_MeOrdersVM_user%1$s_typecredit', $order->user_id));
}
add_action('tt_order_status_change', 'tt_clear_cache_for_order_relates');


/**
 * 保存文章时清理相关缓存
 *
 * @since 2.0.0
 * @param $post_id
 * @return void
 */
function tt_clear_cache_for_post_relates($post_id) {
    $post_type = get_post_type($post_id);

    if($post_type == 'post'){
        // 文章本身
        delete_transient(sprintf('tt_cache_daily_vm_SinglePostVM_post%1$s', $post_id));
        // 文章列表
        delete_transient('tt_cache_daily_vm_HomeLatestVM_page1');
        // 分类列表
        // TODO
    }elseif($post_type == 'page'){
        // 页面本身
        delete_transient(sprintf('tt_cache_daily_vm_SinglePageVM_page%1$s', $post_id));
    }elseif($post_type == 'product'){
        // 商品本身 //与用户id相关的cache key 只能缩短缓存时间自动过期

        // 商品列表
        delete_transient('tt_cache_daily_vm_ShopHomeVM_page1_sort_latest');
        delete_transient('tt_cache_daily_vm_ShopHomeVM_page1_sort_popular');
    }
}
add_action('save_post', 'tt_clear_cache_for_post_relates');


/**
 * 输出小工具前尝试检索缓存
 *
 * @since 2.0.0
 * @param $value
 * @param $type
 * @return string|bool
 */
function tt_retrieve_widget_cache($value, $type) {
    if(tt_get_option('tt_theme_debug', false)) {
        return false;
    }

    $cache_key = CACHE_PREFIX . '_daily_widget_' . $type;
    $cache = get_transient($cache_key);
    return $cache;
}
add_filter('tt_widget_retrieve_cache', 'tt_retrieve_widget_cache', 10 ,2);


/**
 * 将查询获得的小工具的结果缓存
 *
 * @since 2.0.0
 * @param $value
 * @param $type
 * @param $expiration
 * @return void
 */
function tt_create_widget_cache($value, $type, $expiration = 21600) {  // 21600 = 3600*6
    $cache_key = CACHE_PREFIX . '_daily_widget_' . $type;
    $value = '<!-- Widget cached ' . current_time('mysql') . ' -->' . $value;
    set_transient($cache_key, $value, $expiration);
}
add_action('tt_widget_create_cache', 'tt_create_widget_cache', 10, 2);


/**
 * 配置Object Cache服务器
 *
 * @since 2.0.0
 */
function tt_init_object_cache_server(){
    if(of_get_option('tt_object_cache', 'none') == 'memcache') {
        global $memcached_servers;
        $host = of_get_option('tt_memcache_host', '127.0.0.1');
        $port = of_get_option('tt_memcache_port', 11211);
        $memcached_servers[] = $host . ':' . $port;
    }elseif(of_get_option('tt_object_cache', 'none') == 'redis') {
        global $redis_server;
        $redis_server['host'] = of_get_option('tt_redis_host', '127.0.0.1');
        $redis_server['port'] = of_get_option('tt_redis_port', 6379);
    }
}
tt_init_object_cache_server();


/**
 * 评论添加评论时间字段
 *
 * @since   2.0.0
 * @param   $comment_ID
 * @param   $comment_approved
 * @param   $commentdata
 * @return  void
 */
function tt_update_post_latest_reviewed_meta($comment_ID, $comment_approved, $commentdata){
    if(!$comment_approved) return;
    //$comment = get_comment($comment_ID);
    //$post_id = $comment->comment_post_ID;
    $post_id = (int)$commentdata['comment_post_ID'];
    update_post_meta($post_id,'tt_latest_reviewed',time());
}
add_action('comment_post','tt_update_post_latest_reviewed_meta', 10, 3);

// load_func('func.Comment');
/**
 * 评论列表输出callback
 *
 * @since   2.0.0
 * @param   $comment
 * @param   $args
 * @param   $depth
 */
function tt_comment($comment, $args, $depth) {
    global $postdata;
    if($postdata && property_exists($postdata, 'comment_status')) {
        $comment_open = $postdata->comment_status;
    }else{
        $comment_open = comments_open($comment->comment_post_ID);
    }
    $GLOBALS['comment'] = $comment;
    $author_user = get_user_by('ID', $comment->user_id);
    ?>
    <li <?php comment_class(); ?> id="comment-<?php echo $comment->comment_ID;//comment_ID() ?>" data-current-comment-id="<?php echo $comment->comment_ID; ?>" data-parent-comment-id="<?php echo $comment->comment_parent; ?>" data-member-id="<?php echo $comment->user_id; ?>">

    <div class="comment-left pull-left">
        <?php if($author_user) { ?>
            <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $author_user, 50 ); ?>">
            </a>
        <?php }else{ ?>
            <a rel="nofollow" href="javascript: void(0)">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $comment->comment_author, 50 ); ?>">
            </a>
        <?php } ?>
    </div>

    <div class="comment-body">
        <div class="comment-content">
            <?php if($author_user) { ?>
                <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>" class="name replyName"><?php echo $comment->comment_author; ?><?php echo tt_get_member_icon($comment->user_id); ?></a>
            <?php }else{ ?>
                <a rel="nofollow" href="javascript: void(0)" class="name replyName"><?php echo $comment->comment_author; ?></a>
            <?php } ?>
            <!--a class="xb type" href="http://fuli.leiphone.com/guide#module3" target="_blank"></a--><!-- //TODO vip/ip mark -->
            <!--                    --><?php //if(tt_get_option('comment_vip')=='on') get_author_class($comment->comment_author_email,$comment->user_id); ?>
            <!--                    --><?php //if(tt_get_option('comment_ua')=='on') echo outputbrowser($comment->comment_agent); ?>
            <!--                    --><?php //if(tt_get_option('comment_ip')=='on') { ?><!--<span class="comment_author_ip tooltip-trigger" title="--><?php //echo sprintf(__('来自%1$s','tt'), convertip(get_comment_author_ip())); ?><!--"><img class="ip_img" src="--><?php //echo THEME_URI.'/images/ua/ip.png'; ?><!--"></span>--><?php //} ?>
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <span class="pending-comment;"><?php $parent = $comment->comment_parent; if($parent != 0)echo '@'; comment_author_link($parent) ?><?php _e('Your comment is under review...','tt'); ?></span>
                <br />
            <?php endif; ?>
            <?php if ( $comment->comment_approved == '1' ) : ?>
                <?php echo get_comment_text($comment->comment_ID) ?>
            <?php endif; ?>
        </div>

        <span class="comment-time"><?php echo Utils::getTimeDiffString(get_comment_time('Y-m-d G:i:s', true)); ?></span>
        <div class="comment-meta">
            <?php if($comment_open) { ?><a href="javascript:;" class="respond-coin mr5" title="<?php _e('Reply', 'tt'); ?>"><i class="msg"></i><em><?php _e('Reply', 'tt'); ?></em></a><?php } ?>
            <span class="like"><i class="zan"></i><em class="like-count">(<?php echo (int)get_comment_meta($comment->comment_ID, 'tt_comment_likes', true); ?>)</em></span>
        </div>

        <!--        <ul class="csl-respond">-->
        <!--        </ul>-->

        <div class="respond-submit reply-form">
            <div class="text"><input id="<?php echo 'comment-replytext' . $comment->comment_ID; ?>" type="text"><div class="tip"><?php _e('Reply', 'tt'); ?><a><?php echo $comment->comment_author; ?></a>：</div></div>
            <div class="err text-danger"></div>
            <div class="submit-box clearfix">
                <span class="emotion-ico transition" data-emotion="0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="tico tico-smile-o"></i><?php _e('Emotion', 'tt'); ?></span>
                <button class="btn btn-danger pull-right reply-submit" type="submit" title="<?php _e('Reply', 'tt'); ?>" ><?php _e('Reply', 'tt'); ?></button>
                <div class="qqFace  dropdown-menu" data-inputbox-id="<?php echo 'comment-replytext' . $comment->comment_ID; ?>">
                </div>
            </div>
        </div>
    </div>
    <?php
}


/**
 * 评论列表输出callback(商店使用)
 *
 * @since   2.0.0
 * @param   $comment
 * @param   $args
 * @param   $depth
 */
function tt_shop_comment($comment, $args, $depth) {
    global $productdata;
    if($productdata && property_exists($productdata, 'comment_status')) {
        $comment_open = $productdata->comment_status;
    }else{
        $comment_open = comments_open($comment->comment_ID);
    }

    $GLOBALS['comment'] = $comment;
    $rating = (int)get_comment_meta($comment->comment_ID, 'tt_rating_product', true);
    $author_user = get_user_by('ID', $comment->user_id);
    ?>
<li <?php comment_class(); ?> id="comment-<?php echo $comment->comment_ID;//comment_ID() ?>" data-current-comment-id="<?php echo $comment->comment_ID; ?>" data-parent-comment-id="<?php echo $comment->comment_parent; ?>" data-member-id="<?php echo $comment->user_id; ?>">
    <div class="comment-left pull-left">
        <?php if($author_user) { ?>
            <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $author_user, 50 ); ?>">
            </a>
        <?php }else{ ?>
            <a rel="nofollow" href="javascript: void(0)">
                <img class="avatar lazy" src="<?php echo LAZY_PENDING_AVATAR; ?>" data-original="<?php echo tt_get_avatar( $comment->comment_author, 50 ); ?>">
            </a>
        <?php } ?>
    </div>
    <div class="comment-body">
        <div class="comment-content">
            <?php if($author_user) { ?>
                <a rel="nofollow" href="<?php echo get_author_posts_url($comment->user_id); ?>" class="name replyName"><?php echo $comment->comment_author; ?><?php echo tt_get_member_icon($comment->user_id); ?></a>
            <?php }else{ ?>
                <a rel="nofollow" href="javascript: void(0)" class="name replyName"><?php echo $comment->comment_author; ?></a>
            <?php } ?>
            <span class="comment-time"><?php echo ' - ' . Utils::getTimeDiffString(get_comment_time('Y-m-d G:i:s', true)); ?></span>
            <?php if ( $comment->comment_approved == '0' ) : ?>
                <span class="pending-comment;"><?php $parent = $comment->comment_parent; if($parent != 0)echo '@'; comment_author_link($parent) ?><?php _e('Your comment is under review...','tt'); ?></span>
                <br />
            <?php endif; ?>
            <?php if ( $comment->comment_approved == '1' ) : ?>
                <?php echo get_comment_text($comment->comment_ID) ?>
            <?php endif; ?>
        </div>
        <?php if($rating) { ?>
            <span itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating" class="star-rating tico-star-o" title="<?php printf(__('Rated %d out of 5', 'tt'), $rating); ?>">
            <span class="tico-star" style="<?php echo sprintf('width:%d', intval($rating*100/5)) . '%;'; ?>"></span>
        </span>
        <?php } ?>
        <div class="comment-meta">
            <?php if($comment_open) { ?><a href="javascript:;" class="respond-coin mr5" title="<?php _e('Reply', 'tt'); ?>"><i class="msg"></i><em><?php _e('Reply', 'tt'); ?></em></a><?php } ?>
        </div>

        <div class="respond-submit reply-form">
            <div class="text"><input id="<?php echo 'comment-replytext' . $comment->comment_ID; ?>" type="text"><div class="tip"><?php _e('Reply', 'tt'); ?><a><?php echo $comment->comment_author; ?></a>：</div></div>
            <div class="err text-danger"></div>
            <div class="submit-box clearfix">
                <span class="emotion-ico transition" data-emotion="0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="tico tico-smile-o"></i><?php _e('Emotion', 'tt'); ?></span>
                <button class="btn btn-danger pull-right reply-submit" type="submit" title="<?php _e('Reply', 'tt'); ?>" ><?php _e('Reply', 'tt'); ?></button>
                <div class="qqFace  dropdown-menu" data-inputbox-id="<?php echo 'comment-replytext' . $comment->comment_ID; ?>">
                </div>
            </div>
        </div>
    </div>
    <?php
}


function tt_end_comment() {
    echo '</li>';
}


/**
 * 输出评论时转换表情代码为图片
 *
 * @since 2.0.0
 * @param string $comment_text
 * @param WP_Comment $comment
 * @return string
 */
function tt_convert_comment_emotions ($comment_text, $comment = null) {
    $emotion_basepath = THEME_ASSET . '/img/qqFace/';
    $new_comment_text = preg_replace('/\[em_([0-9]+)\]/i', '<img class="em" src="' . $emotion_basepath . "$1" . '.gif">', $comment_text);
    return wpautop($new_comment_text);
}
add_filter( 'comment_text', 'tt_convert_comment_emotions', 10, 2);
add_filter( 'get_comment_text', 'tt_convert_comment_emotions', 10, 2);


/**
 * 插入新评论时清理对应文章评论的缓存
 *
 * @since 2.0.0
 * @param int $comment_ID
 * @param int $comment_approved
 * @param array $commentdata
 * @return void
 */
function tt_clear_post_comments_cache ($comment_ID, $comment_approved, $commentdata) {
    if(!$comment_approved) return;

    $comment_post_ID = $commentdata['comment_post_ID'];
    $cache_key = 'tt_cache_hourly_vm_PostCommentsVM_post' . $comment_post_ID . '_comments';
    delete_transient($cache_key);
}
add_action('comment_post', 'tt_clear_post_comments_cache', 10, 3);

// load_func('func.Init');
/**
 * 主题扩展
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_setup() {
    // 开启自动feed地址
    add_theme_support( 'automatic-feed-links' );

    // 开启缩略图
    add_theme_support( 'post-thumbnails' );

    // 增加文章形式
    add_theme_support( 'post-formats', array( 'audio', 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

    // 菜单区域
    $menus = array(
        'header-menu' => __('Top Menu', 'tt'), //顶部菜单
        'footer-menu' => __('Foot Menu', 'tt'), //底部菜单
        //'page-menu' => __('Pages Menu', 'tt') //页面合并菜单
    );
    $menus['shop-menu'] = __('网站右侧展开菜单', 'tt');
    register_nav_menus($menus);

    // 必须和推荐插件安装提醒
    function tt_register_required_plugins() {
        $plugins = array(
            // 浏览数统计
            array(
                'name' => 'WP-PostViews',
                'slug' => 'wp-postviews',
                'source' => 'https://downloads.wordpress.org/plugin/wp-postviews.1.73.zip',
                'required' => true,
                'version' => '1.73',
                'force_activation' => true,
                'force_deactivation' => false
            ),

            // // 代码高亮
            // array(
            //     'name' => 'Crayon-Syntax-Highlighter',
            //     'slug' => 'crayon-syntax-highlighter',
            //     'source' => 'https://downloads.wordpress.org/plugin/crayon-syntax-highlighter.zip',
            //     'required' => false,
            //     'version' => '2.8.4',
            //     'force_activation' => false,
            //     'force_deactivation' => false
            // ),
        );
        $config = array(
            'domain'       		=> 'tt',         	// Text domain - likely want to be the same as your theme.
            'default_path' 		=> THEME_DIR .'/dash/plugins',                         	// Default absolute path to pre-packaged plugins
            //'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug(deprecated)
            //'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug(deprecated)
            'menu'         		=> 'install-required-plugins', 	// Menu slug
            'has_notices'      	=> true,                       	// Show admin notices or not
            'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
            'message' 			=> '',							// Message to output right before the plugins table
            'strings'      		=> array(
                'page_title'                       			=> __( 'Install Required Plugins', 'tt' ),
                'menu_title'                       			=> __( 'Install Plugins', 'tt' ),
                'installing'                       			=> __( 'Installing: %s', 'tt' ), // %1$s = plugin name
                'oops'                             			=> __( 'There is a problem with the plugin API', 'tt' ),
                'notice_can_install_required'     			=> _n_noop( 'Tint require the plugin: %1$s.', 'Tint require these plugins: %1$s.', 'tt' ), // %1$s = plugin name(s)
                'notice_can_install_recommended'			=> _n_noop( 'Tint recommend the plugin: %1$s.', 'Tint recommend these plugins: %1$s.', 'tt' ), // %1$s = plugin name(s)
                'notice_cannot_install'  					=> _n_noop( 'Permission denied while installing %s plugin.', 'Permission denied while installing %s plugins.', 'tt' ),
                'notice_can_activate_required'    			=> _n_noop( 'The required plugin are not activated yet: %1$s', 'These required plugins are not activated yet: %1$s', 'tt' ),
                'notice_can_activate_recommended'			=> _n_noop( 'The recommended plugin are not activated yet: %1$s', 'These recommended plugins are not activated yet: %1$s', 'tt' ),
                'notice_cannot_activate' 					=> _n_noop( 'Permission denied while activating the %s plugin.', 'Permission denied while activating the %s plugins.', 'tt' ),
                'notice_ask_to_update' 						=> _n_noop( 'The plugin need update: %1$s.', 'These plugins need update: %1$s.', 'tt' ), // %1$s = plugin name(s)
                'notice_cannot_update' 						=> _n_noop( 'Permission denied while updating the %s plugin.', 'Permission denied while updating %s plugins.', 'tt' ),
                'install_link' 					  			=> _n_noop( 'Install the plugin', 'Install the plugins', 'tt' ),
                'activate_link' 				  			=> _n_noop( 'Activate the installed plugin', 'Activate the installed plugins', 'tt' ),
                'return'                           			=> __( 'return back', 'tt' ),
                'plugin_activated'                 			=> __( 'Plugin activated', 'tt' ),
                'complete' 									=> __( 'All plugins are installed and activated %s', 'tt' ), // %1$s = dashboard link
                'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
            )
        );
        tgmpa( $plugins, $config );
    }
    add_action( 'tgmpa_register', 'tt_register_required_plugins' );
}
add_action( 'after_setup_theme', 'tt_setup' );

// load_func('func.Install');
/**
 * 建立Avatar文件夹
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_add_avatar_folder() {
    //$upload = wp_upload_dir();
    //$upload_dir = $upload['basedir'];
    $upload_dir = WP_CONTENT_DIR . '/uploads';
    $avatar_dir = WP_CONTENT_DIR . '/uploads/avatars';
    if (! is_dir($avatar_dir)) {
        // TODO: safe mkdir and echo possible error info on DEBUG mode(option)
        try {
            mkdir( $upload_dir, 0755 );
            mkdir( $avatar_dir, 0755 );
        }catch (Exception $e){
            if(tt_get_option('tt_theme_debug', false)){
                $message = __('Create avatar upload folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt') . __('Caught exception: ', 'tt') . $e->getMessage() . '\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_add_avatar_folder');


/**
 * 建立上传图片的临时文件夹
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_add_upload_tmp_folder() {
    $tmp_dir = WP_CONTENT_DIR . '/uploads/tmp';
    if (! is_dir($tmp_dir)) {
        try {
            mkdir( $tmp_dir, 0755 );
        }catch (Exception $e){
            if(tt_get_option('tt_theme_debug', false)){
                $message = __('Create tmp upload folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt') . __('Caught exception: ', 'tt') . $e->getMessage() . '\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_add_upload_tmp_folder');


/**
 * 复制Object-cache.php到wp-content目录
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_copy_object_cache_plugin(){
    //TODO: maybe need check the file in wp-content is same with that in theme dir
    $object_cache_type = tt_get_option('tt_object_cache', 'none');
    if($object_cache_type == 'memcache' && !class_exists('Memcache')) {
        wp_die(__('You choose the memcache object cache, but the Memcache library is not installed', 'tt'), __('Copy plugin error', 'tt'));
    }
    if($object_cache_type == 'redis' && !class_exists('Redis')) {
        wp_die(__('You choose the redis object cache, but the Redis library is not installed', 'tt'), __('Copy plugin error', 'tt'));
    }
    //!file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ??
    $last_use_cache_type = get_option('tt_object_cache_type');
    if(in_array($object_cache_type, array('memcache', 'redis')) && $last_use_cache_type != $object_cache_type && file_exists( THEME_DIR . '/dash/plugins/' . $object_cache_type . '/object-cache.php')){
        try{
            copy(THEME_DIR . '/dash/plugins/' . $object_cache_type . '/object-cache.php', WP_CONTENT_DIR . '/object-cache.php');
            update_option('tt_object_cache_type', $object_cache_type);
        }catch (Exception $e){
            if(tt_get_option('tt_theme_debug', false)){
                $message = __('Can not copy `object-cache.php` to `wp-content` dir.\n', 'tt') . __('Caught exception: ', 'tt') . $e->getMessage() . '\n';
                $title = __('Copy plugin error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
//add_action('load-themes.php', 'tt_copy_object_cache_plugin');
add_action('admin_menu', 'tt_copy_object_cache_plugin');


/**
 * 复制Timthumb图片裁剪插件必须的缓存引导文件至指定目录
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_copy_timthumb_cache_base(){
    $cache_dir = WP_CONTENT_DIR . '/cache';
    if (! is_dir($cache_dir)) {
        try {
            mkdir( $cache_dir, 0755 );
            mkdir( $cache_dir . '/timthumb', 0755 );
        }catch (Exception $e){
            if(tt_get_option('tt_theme_debug', false)){
                $message = __('Create timthumb cache folder failed, maybe check your php.ini to enable `mkdir` function.\n', 'tt') . __('Caught exception: ', 'tt') . $e->getMessage() . '\n';
                $title = __('Create folder error', 'tt');
                wp_die($message, $title);
            }
        }
    }

    if(is_dir($cache_dir)){
        try{
            copy(THEME_DIR . '/dash/plugins/timthumb/index.html', WP_CONTENT_DIR . '/cache/timthumb/index.html');
            copy(THEME_DIR . '/dash/plugins/timthumb/timthumb_cacheLastCleanTime.touch', WP_CONTENT_DIR . '/cache/timthumb/timthumb_cacheLastCleanTime.touch');
        }catch (Exception $e){
            if(tt_get_option('tt_theme_debug', false)){
                $message = __('Can not copy `memcache object-cache.php` to `wp-content` dir.\n', 'tt') . __('Caught exception: ', 'tt') . $e->getMessage() . '\n';
                $title = __('WordPress internal error', 'tt');
                wp_die($message, $title);
            }
        }
    }
}
add_action('load-themes.php', 'tt_copy_timthumb_cache_base');


/**
 * 重置缩略图的默认尺寸
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_reset_image_size(){
    $enable = of_get_option('tt_enable_wp_crop', false);
    update_option( 'thumbnail_size_w', $enable ? 225 : 0 );
    update_option( 'thumbnail_size_h', $enable ? 150 : 0 );
    update_option( 'thumbnail_crop', 1 );
    update_option( 'medium_size_w', $enable ? 375 : 0 );
    update_option( 'medium_size_h', $enable ? 250 : 0 );
    update_option( 'large_size_w', $enable ? 960 : 0 );
    update_option( 'large_size_h', $enable ? 640 : 0 );
}
add_action('load-themes.php', 'tt_reset_image_size');

/* 建立数据表 */
//TODO: add tables

/**
 * 新建粉丝和关注所用的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_follow_table () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) :
        $sql = " CREATE TABLE `$table_name` (
			`id` int NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			INDEX uid_index(user_id),
			INDEX fuid_index(follow_user_id),
			`user_id` int,
			`follow_user_id` int,
			`follow_status` int,
			`follow_time` datetime
		) ENGINE = MyISAM CHARSET=utf8;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    endif;
}
add_action( 'load-themes.php', 'tt_install_follow_table' );


/**
 * 新建消息所用的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_message_table () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';
    if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) :
        $sql = " CREATE TABLE `$table_name` (
			`msg_id` int NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(msg_id),
			INDEX uid_index(user_id),
			INDEX sid_index(sender_id),
			INDEX mtype_index(msg_type),
			INDEX mdate_index(msg_date),
			INDEX mstatus_index(msg_read),
			`user_id` int,
			`sender_id` int,
			`sender`  varchar(50),
			`msg_type` varchar(20),
			`msg_date` datetime,
			`msg_title` text,
			`msg_content` text,
			`msg_read`  boolean DEFAULT 0,
			`msg_status`  varchar(20)
		) ENGINE = MyISAM CHARSET=utf8;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    endif;
}
add_action( 'load-themes.php', 'tt_install_message_table' );


/**
 * 新建会员所用的数据表
 *
 * @since 2.0.0
 * @return void
 */
function tt_install_membership_table(){
    global $wpdb;
    include_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $users_table = $prefix . 'tt_members';
    if($wpdb->has_cap('collation')) {
        if(!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if(!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $create_vip_users_sql = "CREATE TABLE $users_table (id int(11) NOT NULL auto_increment,user_id int(11) NOT NULL,user_type tinyint(4) NOT NULL default 0,startTime datetime NOT NULL default '0000-00-00 00:00:00',endTime datetime NOT NULL default '0000-00-00 00:00:00',endTimeStamp int NOT NULL default 0,PRIMARY KEY (id),INDEX uid_index(user_id),INDEX utype_index(user_type),INDEX endTime_index(user_id)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($users_table, $create_vip_users_sql);
}
add_action('load-themes.php', 'tt_install_membership_table');
//add_action('admin_menu', 'tt_install_membership_table');


/**
 * 新建现金充值券所用的数据表
 *
 * @since 2.2.0
 * @return void
 */
function tt_install_card_table(){
    global $wpdb;
    include_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $table_charset = '';
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    if($wpdb->has_cap('collation')) {
        if(!empty($wpdb->charset)) {
            $table_charset = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if(!empty($wpdb->collate)) {
            $table_charset .= " COLLATE $wpdb->collate";
        }
    }
    $create_cards_sql = "CREATE TABLE $cards_table (
		id int(11) NOT NULL auto_increment,
		denomination int NOT NULL DEFAULT 100,
		card_id VARCHAR(20) NOT NULL,
		card_secret VARCHAR(20) NOT NULL,
		create_time datetime NOT NULL default '0000-00-00 00:00:00',
		status SMALLINT NOT NULL DEFAULT 1,
		PRIMARY KEY (id),
		INDEX status_index(status),
		INDEX denomination_index(denomination)) ENGINE = MyISAM $table_charset;";
    maybe_create_table($cards_table, $create_cards_sql);
}
add_action('load-themes.php', 'tt_install_card_table');

// load_func('func.Kits');
/**
 * 不可归类工具
 */


/**
 * 根据name获取主题设置(of_get_option别名函数)
 *
 * @since   2.0.0
 *
 * @access  global
 * @param   string  $name     设置ID
 * @param   mixed   $default    默认值
 * @return  mixed   具体设置值
 */
function tt_get_option( $name, $default='' ){
    return of_get_option( $name, $default );
}

// TODO: Utils::function_name -> tt_function_name

// TODO: tt_url_for
/**
 * 获取各种Url
 *
 * @since   2.0.0
 *
 * @param   string  $key    待查找路径的关键字
 * @param   mixed   $arg    接受一个参数，用于动态链接(如一个订单号，一个用户昵称，一个用户id或者一个用户对象)
 * @param   bool    $relative   是否使用相对路径
 * @return  string | false
 */
function tt_url_for($key, $arg = null, $relative = false){
    $routes = (array)json_decode(SITE_ROUTES);
    if(array_key_exists($key, $routes)){
        return $relative ? '/' . $routes[$key] : home_url('/' . $routes[$key]);
    }

    // 输入参数$arg为user时获取其ID使用
    $get_uid = function($var){
        if($var instanceof WP_User){
            return $var->ID;
        }else{
            return intval($var);
        }
    };

    $endpoint = null;
    switch ($key){
        case 'my_order':
            $endpoint = 'me/order/' . (int)$arg;
            break;
        case 'uc_comments':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/comments';
            break;
        case 'uc_profile':
            $endpoint = 'u/' . call_user_func($get_uid, $arg);
            break;
        case 'uc_me':
            $endpoint = 'u/' . get_current_user_id();
            break;
        case 'uc_latest':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/latest';
            break;
        case 'uc_stars':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/stars';
            break;
        case 'uc_followers':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/followers';
            break;
        case 'uc_following':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/following';
            break;
        case 'uc_activities':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/activities';
            break;
        case 'uc_chat':
            $endpoint = 'u/' . call_user_func($get_uid, $arg) . '/chat';
            break;
        case 'manage_user':
            $endpoint = 'management/users/' . intval($arg);
            break;
        case 'manage_order':
            $endpoint = 'management/orders/' . intval($arg);
            break;
        case 'shop_archive':
            $endpoint = tt_get_option('tt_product_archives_slug', 'shop');
            break;
        case 'edit_post':
            $endpoint = 'me/editpost/' . absint($arg);
            break;
        case 'download':
            $endpoint = 'site/download?_=' . urlencode(rtrim(tt_encrypt($arg, tt_get_option('tt_private_token')), '='));
            break;
    }
    if($endpoint){
        return $relative ? '/' . $endpoint : home_url('/' . $endpoint);
    }
    return false;
}


/**
 * 获取当前页面url
 *
 * @since   2.0.0
 * @param   string  $method    获取方法，分别为PHP的$_SERVER对象获取(php)和WordPress的全局wp_query对象获取(wp)
 * @return  string
 */
function tt_get_current_url($method = 'php') {
    if($method === 'wp') {
        return Utils::getCurrentUrl();
    }
    return Utils::getPHPCurrentUrl();
}


/**
 * 登录的url
 *
 * @since   2.0.0
 *
 * @param   string  $redirect  重定向链接，未url encode
 * @return  string
 */
function tt_signin_url($redirect) {
    return tt_filter_default_login_url('', $redirect);
}


/**
 * 注册的url
 *
 * @since   2.0.0
 *
 * @param   string  $redirect  重定向链接，未url encode
 * @return  string
 */
function tt_signup_url($redirect) {
    $signup_url = tt_url_for('signup');

    if ( !empty($redirect) ) {
        $signup_url = add_query_arg('redirect_to', urlencode($redirect), $signup_url);
    }
    return $signup_url;
}


/**
 * 注销的url
 *
 * @since   2.0.0
 *
 * @param   string  $redirect  重定向链接，未url encode
 * @return  string
 */
function tt_signout_url($redirect = '') {
    if(empty($redirect)) {
        $redirect = home_url();
    }
    return tt_filter_default_logout_url('', $redirect);
}


/**
 * 为链接添加重定向链接
 *
 * @since   2.0.0
 * @param   string  $url
 * @param   string  $redirect
 * @return  string
 */
function tt_add_redirect($url, $redirect = '') {
    if($redirect) {
        return add_query_arg('redirect_to', urlencode($redirect), $url);
    }elseif(isset($_GET['redirect_to'])){
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect_to'])), $url);
    }elseif(isset($_GET['redirect'])){
        return add_query_arg('redirect_to', urlencode(esc_url_raw($_GET['redirect'])), $url);
    }
    return add_query_arg('redirect_to', urlencode(home_url()), $url);
}


/**
 * 可逆加密
 *
 * @since   2.0.0
 *
 * @param   mixed   $data   待加密数据
 * @param   string  $key    加密密钥
 * @return  string
 */
function tt_encrypt($data, $key) {
    if(is_numeric($data)){
        $data = strval($data);
    }else{
        $data = maybe_serialize($data);
    }
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}

/**
 * 解密
 *
 * @since   2.0.0
 *
 * @param   string  $data   待解密数据
 * @param   string  $key    密钥
 * @return  mixed
 */
function tt_decrypt($data, $key) {
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return maybe_unserialize($str);
}


/**
 * 加密解密数据
 *
 * @since   2.0.0
 *
 * @param   mixed   $data   待加密数据
 * @param   string  $operation  操作(加密|解密)
 * @param   string  $key    密钥
 * @param   int     $expire     过期时间
 * @return  string
 */
function tt_authdata($data, $operation = 'DECODE', $key = '', $expire = 0) {
    if($operation != 'DECODE'){
        $data = maybe_serialize($data);
    }
    $ckey_length = 4;
    $key = md5($key ? $key : 'null');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($data, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $data = $operation == 'DECODE' ? base64_decode(substr($data, $ckey_length)) : sprintf('%010d', $expire ? $expire + time() : 0) . substr(md5($data . $keyb), 0, 16) . $data;
    $string_length = strlen($data);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($data[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return maybe_unserialize(substr($result, 26));
        } else {
            return false;
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}


/**
 * 替换默认的wp_die处理函数
 *
 * @since   2.0.0
 *
 * @param   string | WP_Error  $message    错误消息
 * @param   string  $title      错误标题
 * @param   array   $args       其他参数
 */
function tt_wp_die_handler($message, $title = '', $args = array()) {
    $defaults = array( 'response' => 500 );
    $r = wp_parse_args($args, $defaults);

    if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
        if ( empty( $title ) ) {
            $error_data = $message->get_error_data();
            if ( is_array( $error_data ) && isset( $error_data['title'] ) )
                $title = $error_data['title'];
        }
        $errors = $message->get_error_messages();
        switch ( count( $errors ) ) {
            case 0 :
                $message = '';
                break;
            case 1 :
                $message = "{$errors[0]}";
                break;
            default :
                $message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
                break;
        }
    }

    if ( ! did_action( 'admin_head' ) ) :
        if ( !headers_sent() ) {
            status_header( $r['response'] );
            nocache_headers();
            header( 'Content-Type: text/html; charset=utf-8' );
        }

        if ( empty($title) )
            $title = __('WordPress &rsaquo; Error');

        $text_direction = 'ltr';
        if ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] )
            $text_direction = 'rtl';
        elseif ( function_exists( 'is_rtl' ) && is_rtl() )
            $text_direction = 'rtl';

        // 引入自定义模板
        global $wp_query;
        $wp_query->query_vars['die_title'] = $title;
        $wp_query->query_vars['die_msg'] = $message;
        include_once THEME_TPL . '/tpl.Error.php';
    endif;

    die();
}
function tt_wp_die_handler_switch(){
    return 'tt_wp_die_handler';
}
add_filter('wp_die_handler', 'tt_wp_die_handler_switch');


/**
 * 获取当前页面需要应用的样式链接
 *
 * @since   2.0.0
 * @param   string  $filename  文件名
 * @return  string
 */
function tt_get_css($filename = '') {
    if($filename) {
        return THEME_CDN_ASSET . '/css/' . $filename;
    }

    if(is_home()) {
        $filename = CSS_HOME;
    }elseif(is_single()) {
        $filename = get_post_type()==='product' ? CSS_PRODUCT : (get_post_type()==='bulletin' ? CSS_PAGE : CSS_SINGLE);
    }elseif((is_archive() && !is_author()) || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1)) {
        $filename = get_post_type()==='product' || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1) ? CSS_PRODUCT_ARCHIVE : CSS_ARCHIVE;
    }elseif(is_author()) {
        $filename = CSS_UC;
    }elseif(is_404()) {
        $filename = CSS_404;
    }elseif(get_query_var('is_me_route')) {
        $filename = CSS_ME;
    }elseif(get_query_var('action')) {
        $filename = CSS_ACTION;
    }elseif(is_front_page()) {
        $filename = CSS_FRONT_PAGE;
    }elseif(get_query_var('site_util')){
        $filename = CSS_SITE_UTILS;
    }elseif(get_query_var('oauth')){
        $filename = CSS_OAUTH;
    }elseif(get_query_var('is_manage_route')){
        $filename = CSS_MANAGE;
    }else{
        // is_page() ?
        $filename = CSS_PAGE;
    }
    return THEME_CDN_ASSET . '/css/' . $filename;
}

function tt_get_custom_css() {
    $ver = tt_get_option('tt_custom_css_cache_suffix');
    return add_query_arg('ver', $ver, tt_url_for('custom_css'));
}

/**
 * 条件判断类名
 *
 * @param $base_class
 * @param $condition
 * @param string $active_class
 * @return string
 */
function tt_conditional_class($base_class, $condition, $active_class = 'active') {
    if($condition) {
        return $base_class . ' ' . $active_class;
    }
    return $base_class;
}


/**
 * 二维码API
 *
 * @since 2.0.0
 * @param $text
 * @param $size
 * @return string
 */
function tt_qrcode($text, $size) {
    //TODO size
    return tt_url_for('qr') . '?text=' . $text;
}

/**
 * 页脚年份
 *
 * @since 2.0.0
 * @return string
 */
function tt_copyright_year(){
    $now_year = date('Y');
    $open_date = tt_get_option('tt_site_open_date', $now_year);
    $open_year = substr($open_date, 0, 4);

    return $open_year . '-' . $now_year . '&nbsp;&nbsp;';
}


/**
 * 生成推广链接
 *
 * @param int $user_id
 * @param string $base_link
 * @return string
 */
function tt_get_referral_link($user_id = 0, $base_link = ''){
    if(!$base_link) $base_link = home_url();
    if(!$user_id) $user_id = get_current_user_id();

    return add_query_arg(array('ref' => $user_id), $base_link);
}


/**
 * 获取GET方法http响应状态代码
 *
 * @since 2.0.0
 * @param $theURL
 * @return string
 */
function tt_get_http_response_code($theURL) {
    @$headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
}


/**
 * Curl GET方式获取url响应文档
 *
 * @param $url
 * @return mixed
 */
function tt_curl_get($url){
    $ch = curl_init ();
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt ($ch, CURLOPT_URL, $url );
    $result = curl_exec($ch);
    curl_close ($ch);
    return $result;
}


/**
 * Curl POST方式获取url响应文档
 *
 * @param $url
 * @param $data
 * @return mixed
 */
function tin_curl_post($url, $data){
    $post_data = http_build_query($data);
    $post_url= $url;
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_URL, $post_url );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $return = curl_exec($ch);
    if (curl_errno($ch)) {
        return '';
    }
    curl_close($ch);
    return $return;
}


/**
 * 过滤multicheck选项的设置值
 *
 * @since 2.0.5
 * @param $option
 * @return array
 */
function tt_filter_of_multicheck_option($option) {
    // 主题选项框架获得multicheck类型选项的值为 array(id => bool), 而我们需要的是bool为true的array(id)
    if(!is_array($option)) {
        return $option;
    }

    $new_option = array();
    foreach ($option as $key => $value) {
        if($value) {
            $new_option[] = $key;
        }
    }
    return $new_option;
}


/**
 * 分页
 *
 * @param $base
 * @param $current
 * @param $max
 */
function tt_default_pagination($base, $current, $max) {
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php $pagination = paginate_links(array(
                'base' => $base,
                'format' => '?paged=%#%',
                'current' => $current,
                'total' => $max,
                'type' => 'array',
                'prev_next' => true,
                'prev_text' => '<i class="tico tico-angle-left"></i>',
                'next_text' => '<i class="tico tico-angle-right"></i>'
            )); ?>
            <?php foreach ($pagination as $page_item) {
                echo '<li class="page-item">' . $page_item . '</li>';
            } ?>
        </ul>
        <div class="page-nums">
            <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $current); ?></span>
            <span class="separator">/</span>
            <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max); ?></span>
        </div>
    </nav>
    <?php
}


/**
 * 分页
 *
 * @param $base
 * @param $current
 * @param $max
 */
function tt_pagination($base, $current, $max) {
    ?>
    <nav class="pagination-new">
        <ul>
            <?php $pagination = paginate_links(array(
                'base' => $base,
                'format' => '?paged=%#%',
                'current' => $current,
                'total' => $max,
                'type' => 'array',
                'prev_next' => true,
                'prev_text' => '<span class="prev">' . __('PREV PAGE', 'tt') . '</span>',
                'next_text' => '<span class="next">' . __('NEXT PAGE', 'tt') . '</span>'
            )); ?>
            <?php foreach ($pagination as $page_item) {
                echo '<li class="page-item">' . $page_item . '</li>';
            } ?>
            <li class="page-item"><span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max); ?></span></li>
        </ul>
    </nav>
    <?php
}

// load_func('func.Mail');
/**
 * 根据用户设置选择邮件发送方式
 *
 * @since   2.0.0
 *
 * @param   object  $phpmailer  PHPMailer对象
 * @return  void
 */
function tt_switch_mailer($phpmailer){
    $mailer = tt_get_option('tt_default_mailer');
    if($mailer === 'smtp'){
        //$phpmailer->isSMTP();
        $phpmailer->Mailer = 'smtp';
        $phpmailer->Host = tt_get_option('tt_smtp_host');
        $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
        $phpmailer->Port = tt_get_option('tt_smtp_port');
        $phpmailer->Username = tt_get_option('tt_smtp_username');
        $phpmailer->Password = tt_get_option('tt_smtp_password');

        // Additional settings…
        $phpmailer->SMTPSecure = tt_get_option('tt_smtp_secure');
        $phpmailer->FromName = tt_get_option('tt_smtp_name');
        $phpmailer->From = $phpmailer->Username; // tt_get_option('tt_mail_custom_address'); // 多数SMTP提供商要求发信人与SMTP服务器匹配，自定义发件人地址可能无效
        $phpmailer->Sender = $phpmailer->From; //Return-Path
        $phpmailer->AddReplyTo($phpmailer->From,$phpmailer->FromName); //Reply-To
    }else{
        // when use php mail
        $phpmailer->FromName = tt_get_option('tt_mail_custom_sender');
        $phpmailer->From = tt_get_option('tt_mail_custom_address');
    }
}
add_action('phpmailer_init', 'tt_switch_mailer');


/**
 * 发送邮件
 *
 * @since 2.0.0
 *
 * @param string    $from   发件人
 * @param string    $to     收件人
 * @param string    $title  主题
 * @param string|array    $args    渲染内容所需的变量对象
 * @param string    $template   模板，例如评论回复邮件模板、新用户、找回密码、订阅信等模板
 * @return  void
 */
function tt_mail($from, $to, $title = '', $args = array(), $template = 'comment') {
    $title = $title ? trim($title) : tt_get_mail_title($template);
    $content = tt_mail_render($args, $template);
    $blog_name = get_bloginfo('name');
    $sender_name = tt_get_option('tt_mail_custom_sender') || tt_get_option('tt_smtp_name', $blog_name);
    if(empty($from)){
        $from = tt_get_option('tt_mail_custom_address', 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']))); //TODO: case e.g subdomain.domain.com
    }

    $fr = "From: \"" . $sender_name . "\" <$from>";
    $headers = "$fr\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
    wp_mail( $to, $title, $content, $headers );
}
add_action('tt_async_send_mail', 'tt_mail', 10, 5);

/**
 * 异步发送邮件
 *
 * @since 2.0.0
 * @param $from
 * @param $to
 * @param string $title
 * @param array $args
 * @param string $template
 */
function tt_async_mail($from, $to, $title = '', $args = array(), $template = 'comment'){
    if(is_array($args)) {
        $args = base64_encode(json_encode($args));
    }
    do_action('send_mail', $from, $to, $title, $args, $template);
}


/**
 * 邮件内容的模板选择处理
 *
 * @since   2.0.0
 *
 * @param   string  $content    未处理的邮件内容或者内容必要参数数组
 * @param   string  $template   渲染模板选择(reset_pass|..)
 * @return  string
 */
function tt_mail_render($content, $template = 'comment') {
    // 使用Plates模板渲染引擎
    $templates = new League\Plates\Engine(THEME_TPL . '/plates/emails');
    if (is_string($content)) {
        return $templates->render('pure', array('content' => $content));
    } elseif (is_array($content)) {
        return $templates->render($template, $content); // TODO confirm template exist
    }
    return '';
}

/**
 * 不同模板的邮件标题
 *
 * @since   2.0.0
 *
 * @param   string  $template   邮件模板
 * @return  string
 */
function tt_get_mail_title($template = 'comment') {
    $blog_name = get_bloginfo('name');
    switch ($template){
        case 'comment':
            return sprintf(__('New Comment Notification - %s', 'tt'), $blog_name);
            break;
        case 'comment-admin':
            return sprintf(__('New Comment In Your Blog - %s', 'tt'), $blog_name);
            break;
        case 'contribute-post':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        case 'download':
            return sprintf(__('The Files You Asking For In %s', 'tt'), $blog_name);
            break;
        case 'download-admin':
            return sprintf(__('New Download Request Handled In Your Blog %s', 'tt'), $blog_name);
            break;
        case 'findpass':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        case 'login':
            return sprintf(__('New Login Event Notification - %s', 'tt'), $blog_name);
            break;
        case 'login-fail':
            return sprintf(__('New Login Fail Event Notification - %s', 'tt'), $blog_name);
            break;
        case 'reply':
            return sprintf(__('New Comment Reply Notification - %s', 'tt'), $blog_name);
            break;
        //TODO more
        default:
            return sprintf(__('Site Internal Notification - %s', 'tt'), $blog_name);
    }
}


/**
 * 评论回复邮件
 *
 * @since 2.0.0
 * @param $comment_id
 * @param $comment_object
 * @return void
 */
function tt_comment_mail_notify($comment_id, $comment_object) {
    if(!tt_get_option('tt_comment_events_notify', false) || $comment_object->comment_approved != 1 || !empty($comment_object->comment_type) ) return;
    date_default_timezone_set ('Asia/Shanghai');
    $admin_notify = '1'; // admin 要不要收回复通知 ( '1'=要 ; '0'=不要 )
    $admin_email = get_bloginfo ('admin_email'); // $admin_email 可改为你指定的 e-mail.
    $comment = get_comment($comment_id);
    $comment_author = trim($comment->comment_author);
    $comment_date = trim($comment->comment_date);
    $comment_link = htmlspecialchars(get_comment_link($comment_id));
    $comment_content = nl2br($comment->comment_content);
    $comment_author_email = trim($comment->comment_author_email);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $parent_comment = !empty($parent_id) ? get_comment($parent_id) : null;
    $parent_email = $parent_comment ? trim($parent_comment->comment_author_email) : '';
    $post = get_post($comment_object->comment_post_ID);
    $post_author_email = get_user_by( 'id' , $post->post_author)->user_email;

//    global $wpdb;
//    if ($wpdb->query("Describe {$wpdb->comments} comment_mail_notify") == '')
//        $wpdb->query("ALTER TABLE {$wpdb->comments} ADD COLUMN comment_mail_notify TINYINT NOT NULL DEFAULT 0;");
//    if (isset($_POST['comment_mail_notify']))
//        $wpdb->query("UPDATE {$wpdb->comments} SET comment_mail_notify='1' WHERE comment_ID='$comment_id'");
    //$notify = $parent_id ? $parent_comment->comment_mail_notify : '0';
    $notify = 1; // 默认全部提醒
    $spam_confirmed = $comment->comment_approved;
    //给父级评论提醒
    if ($parent_id != '' && $spam_confirmed != 'spam' && $notify == '1' && $parent_email != $comment_author_email) {
        $parent_author = trim($parent_comment->comment_author);
        $parent_comment_date = trim($parent_comment->comment_date);
        $parent_comment_content = nl2br($parent_comment->comment_content);
        $args = array(
            'parentAuthor' => $parent_author,
            'parentCommentDate' => $parent_comment_date,
            'parentCommentContent' => $parent_comment_content,
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentDate' => $comment_date,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link
        );
        if(filter_var( $post_author_email, FILTER_VALIDATE_EMAIL)){
            tt_mail('', $parent_email, sprintf( __('%1$s在%2$s中回复你', 'tt'), $comment_object->comment_author, $post->post_title ), $args, 'reply');
        }
        if($parent_comment->user_id){
            tt_create_message($parent_comment->user_id, $comment->user_id, $comment_author, 'notification', sprintf( __('我在%1$s中回复了你', 'tt'), $post->post_title ), $comment_content);
        }
    }

    //给文章作者的通知
    if($post_author_email != $comment_author_email && $post_author_email != $parent_email){
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link
        );
        if(filter_var( $post_author_email, FILTER_VALIDATE_EMAIL)){
            tt_mail('', $post_author_email, sprintf( __('%1$s在%2$s中回复你', 'tt'), $comment_author, $post->post_title ), $args, 'comment');
        }
        tt_create_message($post->post_author, 0, 'System', 'notification', sprintf( __('%1$s在%2$s中回复你', 'tt'), $comment_author, $post->post_title ), $comment_content);
    }

    //给管理员通知
    if($post_author_email != $admin_email && $parent_id != $admin_email && $admin_notify == '1'){
        $args = array(
            'postTitle' => $post->post_title,
            'commentAuthor' => $comment_author,
            'commentContent' => $comment_content,
            'commentLink' => $comment_link
        );
        tt_mail('', $admin_email, sprintf( __('%1$s上的文章有了新的回复', 'tt'), get_bloginfo('name') ), $args, 'comment-admin');
        //tt_create_message() //TODO
    }
}
//add_action('comment_post', 'tt_comment_mail_notify');
add_action('wp_insert_comment', 'tt_comment_mail_notify' , 99, 2 );


/**
 * WP登录提醒
 *
 * @since 2.0.0
 * @param string $user_login
 * @return void
 */
function tt_wp_login_notify($user_login){
    if(!tt_get_option('tt_login_success_notify')){
        return ;
    }
    date_default_timezone_set ('Asia/Shanghai');
    $admin_email = get_bloginfo ('admin_email');
    $subject = __('你的博客空间登录提醒', 'tt');
    $args = array(
        'loginName' => $user_login,
        'ip' => $_SERVER['REMOTE_ADDR']
    );
    tt_async_mail('', $admin_email, $subject, $args, 'login');
    //tt_mail('', $admin_email, $subject, $args, 'login');
}
add_action('wp_login', 'tt_wp_login_notify', 10, 1);

/**
 * WP登录错误提醒
 *
 * @since 2.0.0
 * @param string $login_name
 * @return void
 */
function tt_wp_login_failure_notify($login_name){
    if(!tt_get_option('tt_login_failure_notify')){
        return ;
    }
    date_default_timezone_set ('Asia/Shanghai');
    $admin_email = get_bloginfo ('admin_email');
    $subject = __('你的博客空间登录错误警告', 'tt');
    $args = array(
        'loginName' => $login_name,
        'ip' => $_SERVER['REMOTE_ADDR']
    );
    tt_async_mail('', $admin_email, $subject, $args, 'login-fail');
}
add_action('wp_login_failed', 'tt_wp_login_failure_notify', 10, 1);


/**
 * 投稿文章发表时给作者添加积分和发送邮件通知
 *
 * @since 2.0.0
 * @param $post
 * @return void
 */
function tt_pending_to_publish( $post ) {
    $rec_post_num = (int)tt_get_option('tt_rec_post_num', '5');
    $rec_post_credit = (int)tt_get_option('tt_rec_post_credit','50');
    $rec_post = (int)get_user_meta( $post->post_author, 'tt_rec_post', true );
    if( $rec_post<$rec_post_num && $rec_post_credit ){
        //添加积分
        tt_update_user_credit($post->post_author, $rec_post_credit, sprintf(__('获得文章投稿奖励%1$s积分', 'tt'), $rec_post_credit), false);
        //发送邮件
        $user = get_user_by( 'id', $post->post_author );
        $user_email = $user->user_email;
        if( filter_var( $user_email , FILTER_VALIDATE_EMAIL)){
            $subject = sprintf(__('你在%1$s上有新的文章发表', 'tt'), get_bloginfo('name'));
            $args = array(
                'postAuthor' => $user->display_name,
                'postLink' => get_permalink($post->ID),
                'postTitle' => $post->post_title
            );
            tt_async_mail('', $user_email, $subject, $args, 'contribute-post');
        }
    }
    update_user_meta( $post->post_author, 'tt_rec_post', $rec_post+1);
}
add_action( 'pending_to_publish',  'tt_pending_to_publish', 10, 1 );
add_action( 'tt_immediate_to_publish',  'tt_pending_to_publish', 10, 1 );


/**
 * 开通或续费会员后发送邮件
 *
 * @since 2.0.0
 * @param $user_id
 * @param $type
 * @param $start_time
 * @param $end_time
 */
function tt_open_vip_email($user_id, $type, $start_time, $end_time){
    $user = get_user_by( 'id', $user_id );
    if(!$user){
        return;
    }
    $user_email = $user->user_email;
    $subject = __('会员状态变更提醒', 'tt');
    $vip_type_des = tt_get_member_type_string($type);
    $args = array(
        'adminEmail' => get_option('admin_email'),
        'vipType' => $vip_type_des,
        'startTime' => $start_time,
        'endTime' => $end_time
    );
    tt_async_mail('', $user_email, $subject, $args, 'open-vip');
}


/**
 * 管理员手动提升会员后发送邮件
 *
 * @since 2.0.0
 * @param $user_id
 * @param $type
 * @param $start_time
 * @param $end_time
 */
function tt_promote_vip_email($user_id, $type, $start_time, $end_time){
    $user = get_user_by( 'id', $user_id );
    if(!$user){
        return;
    }
    $user_email = $user->user_email;
    $subject = __('会员状态变更提醒', 'tt');
    $vip_type_des = tt_get_member_type_string($type);
    $args = array(
        'adminEmail' => get_option('admin_email'),
        'vipType' => $vip_type_des,
        'startTime' => $start_time,
        'endTime' => $end_time
    );
    tt_async_mail('', $user_email, $subject, $args, 'promote-vip');
}

// load_func('func.Metabox');
function tt_add_metaboxes() {
    // 嵌入商品
    add_meta_box(
        'tt_post_embed_product',
        __( 'Post Embed Product', 'tt' ),
        'tt_post_embed_product_callback',
        'post',
        'normal','high'
    );
    // 转载信息
    add_meta_box(
        'tt_copyright_content',
        __( 'Post Copyright Info', 'tt' ),
        'tt_post_copyright_callback',
        'post',
        'normal','high'
    );
    // 文章内嵌下载资源
    add_meta_box(
        'tt_dload_metabox',
        __( '普通与积分收费下载', 'tt' ),
        'tt_download_metabox_callback',
        'post',
        'normal','high'
    );
    // 文章普通资源下载信息
    add_meta_box(
        'tt_postmeta_metabox',
        __( '文章普通资源下载信息', 'tt' ),
        'tt_postmeta_metabox_callback',
        'post',
        'normal','high'
    );
    // 页面关键词与描述
    add_meta_box(
        'tt_keywords_description',
        __( '页面关键词与描述', 'tt' ),
        'tt_keywords_description_callback',
        'page',
        'normal','high'
    );
    // 商品信息输入框
    add_meta_box(
        'tt_product_info',
        __( '商品信息', 'tt' ),
        'tt_product_info_callback',
        'product',
        'normal','high'
    );
}
add_action( 'add_meta_boxes', 'tt_add_metaboxes' );


/**
 * 文章内嵌入商品
 *
 * @since 2.0.0
 * @param $post
 * @return void
 */
function tt_post_embed_product_callback($post) {
    $embed_product = (int)get_post_meta( $post->ID, 'tt_embed_product', true );
    ?>
    <p style="width:100%;">
        <?php _e( 'Embed Product ID', 'tt' );?>
        <input name="tt_embed_product" class="small-text code" value="<?php echo $embed_product;?>" style="width:80px;height: 28px;">
        <?php _e( '(Leave empty or zero to disable)', 'tt' );?>
    </p>
    <?php
}

/**
 * 文章普通资源下载按钮信息
 *
 * @since   2.0.0
 * @param   WP_Post    $post
 * @return  void
 */
function tt_postmeta_metabox_callback($post) {
    $postmeta_demo = get_post_meta( $post->ID, 'tt_postmeta_demo', true ); //演示地址
    $postmeta_ver = get_post_meta( $post->ID, 'tt_postmeta_ver', true );  // 版本
    $postmeta_type = get_post_meta( $post->ID, 'tt_postmeta_type', true ); // 文件类型
    $postmeta_size = get_post_meta( $post->ID, 'tt_postmeta_size', true ); // 大小
    $postmeta_view = absint(get_post_meta( $post->ID, 'views', true )); // 查看量
    ?>
    <p><?php _e( '演示地址:', 'tt' );?></p>
    <input name="tt_postmeta_demo" rows="1" class="large-text code" value="<?php echo $postmeta_demo;?>" style="width:50%;height: 28px;"></input>
    <p><?php _e( '当前版本:', 'tt' );?></p>
    <input name="tt_postmeta_ver" rows="1" class="large-text code" value="<?php echo $postmeta_ver;?>" style="width:50%;height: 28px;"></input>
    <p><?php _e( '文件类型:', 'tt' );?></p>
    <input name="tt_postmeta_type" rows="1" class="large-text code" value="<?php echo $postmeta_type;?>" style="width:50%;height: 28px;"></input>
    <p><?php _e( '文件大小:', 'tt' );?></p>
    <input name="tt_postmeta_size" rows="1" class="large-text code" value="<?php echo $postmeta_size;?>" style="width:50%;height: 28px;"></input>
    <?php
}


/**
 * 文章转载信息
 *
 * @since   2.0.0
 * @param   WP_Post    $post
 * @return  void
 */
function tt_post_copyright_callback($post) {
    $cc = get_post_meta( $post->ID, 'tt_post_copyright', true );
    $cc = $cc ? maybe_unserialize($cc) : array('source_title' => '', 'source_link' => '');
    ?>
    <p><?php _e( 'Post Source Title', 'tt' );?></p>
    <textarea name="tt_source_title" rows="1" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($cc['source_title']));?></textarea>
    <p><?php _e( 'Post Source Link, leaving empty means the post is original article', 'tt' );?></p>
    <textarea name="tt_source_link" rows="1" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($cc['source_link']));?></textarea>
    <?php
}




/**
 * 普通与积分下载Metabox
 *
 * @since 2.0.0
 * @param $post
 * @return void
 */
function tt_download_metabox_callback( $post ) {

    //免费下载资源
    $free_dl = get_post_meta( $post->ID, 'tt_free_dl', true ) ? : '';
    //积分下载资源
    $sale_dl = get_post_meta( $post->ID, 'tt_sale_dl', true ) ? : '';
    //付费下载资源
    $sale_dl2 = get_post_meta( $post-> ID, 'tt_sale_dl2', true) ? : '';
    ?>
    <p><?php _e( '普通下载资源，格式为 资源1名称|资源1url|下载密码,资源2名称|资源2url|下载密码 资源名称与url用|隔开，一行一个资源记录，url请添加http://头，如提供百度网盘加密下载可以填写密码，也可以留空', 'tt' );?></p>
    <textarea name="tt_free_dl" rows="5" cols="50" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($free_dl));?></textarea>
    <p><?php _e( '注:收费资源下载区用于替代上述积分资源下载区,以支持内嵌资源的收费币种类型定义,对接商店的订单系统和多个备用下载地址', 'tt' );?></p>
    <p><?php _e( '收费下载资源，格式为 资源名称|资源下载url1__密码1,资源下载url2__密码2|资源价格|币种(cash或credit)，一行一个资源记录', 'tt' );?></p>
    <textarea name="tt_sale_dl2" rows="5" cols="50" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($sale_dl2));?></textarea>
    <?php
}


/**
 * 页面关键词与描述
 *
 * @since 2.0.0
 * @param $post
 * @return void
 */
function tt_keywords_description_callback($post){
    $keywords = get_post_meta( $post->ID, 'tt_keywords', true );
    $description = get_post_meta($post->ID, 'tt_description', true);
    ?>
    <p><?php _e( '页面关键词', 'tt' );?></p>
    <textarea name="tt_keywords" rows="2" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($keywords));?></textarea>
    <p><?php _e( '页面描述', 'tt' );?></p>
    <textarea name="tt_description" rows="5" class="large-text code"><?php echo stripcslashes(htmlspecialchars_decode($description));?></textarea>

    <?php
}


/**
 * 商品信息
 *
 * @since 2.0.0
 * @param $post
 * @return void
 */
function tt_product_info_callback($post){
    $currency = get_post_meta($post->ID, 'tt_pay_currency', true); // 0 - credit 1 - cash
    $channel = get_post_meta($post->ID, 'tt_buy_channel', true) == 'taobao' ? 'taobao' : 'instation';
    $price = get_post_meta($post->ID, 'tt_product_price', true);
    $amount = get_post_meta($post->ID, 'tt_product_quantity', true);

    $taobao_link_raw = get_post_meta($post->ID, 'tt_taobao_link', true);
    $taobao_link = $taobao_link_raw ? esc_url($taobao_link_raw) : '';

    // 注意，折扣保存的是百分数的数值部分
    $discount_summary = tt_get_product_discount_array($post->ID); // 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣
    $site_discount = $discount_summary[0];
    $monthly_vip_discount = $discount_summary[1];
    $annual_vip_discount = $discount_summary[2];
    $permanent_vip_discount = $discount_summary[3];

    //$promote_code_support = get_post_meta($post->ID, 'tt_promote_code_support', true) ? (int)get_post_meta($post->ID, 'tt_promote_code_support', true) : 0;
    //$promote_discount = get_post_meta($post->ID,'product_promote_discount',true);
    //$promote_discount = empty($promote_discount) ? 1 : $promote_discount;;
    //$discount_begin_date = get_post_meta($post->ID,'product_discount_begin_date',true);
    //$discount_period = get_post_meta($post->ID,'product_discount_period',true);
    $download_links = get_post_meta($post->ID, 'tt_product_download_links', true);
    $pay_content = get_post_meta($post->ID,'tt_product_pay_content',true);
    $buyer_emails_string = tt_get_buyer_emails($post->ID);
    $buyer_emails = is_array($buyer_emails_string) ? implode(';', $buyer_emails_string) : '';
    ?>
    <p style="clear:both;font-weight:bold;">
        <?php echo sprintf(__('此商品购买按钮快捷插入短代码为[product id="%1$s"][/product]', 'tt'), $post->ID); ?>
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('基本信息', 'tt'); ?>
    </p>
    <p style="width:20%;float:left;"><?php _e( '选择支付币种', 'tt' );?>
        <select name="tt_pay_currency">
            <option value="0" <?php if( $currency!=1) echo 'selected="selected"';?>><?php _e( '积分', 'tt' );?></option>
            <option value="1" <?php if( $currency==1) echo 'selected="selected"';?>><?php _e( '人民币', 'tt' );?></option>
        </select>
    </p>
    <p style="width:20%;float:left;"><?php _e( '选择购买渠道', 'tt' );?>
        <select name="tt_buy_channel">
            <option value="instation" <?php if( $channel!='taobao') echo 'selected="selected"';?>><?php _e( '站内购买', 'tt' );?></option>
            <option value="taobao" <?php if( $channel=='taobao') echo 'selected="selected"';?>><?php _e( '淘宝链接', 'tt' );?></option>
        </select>
    </p>
    <p style="width:20%;float:left;"><?php _e( '商品售价 ', 'tt' );?>
        <input name="tt_product_price" class="small-text code" value="<?php echo sprintf('%0.2f', $price);?>" style="width:80px;height: 28px;">
    </p>
    <p style="width:20%;float:left;"><?php _e( '商品数量 ', 'tt' );?>
        <input name="tt_product_quantity" class="small-text code" value="<?php echo (int)$amount;?>" style="width:80px;height: 28px;">
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('VIP会员折扣百分数(100代表原价)', 'tt'); ?>
    </p>
    <p style="width:33%;float:left;clear:left;"><?php _e( 'VIP月费会员折扣 ', 'tt' );?>
        <input name="tt_monthly_vip_discount" class="small-text code" value="<?php echo $monthly_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="width:33%;float:left;"><?php _e( 'VIP年费会员折扣 ', 'tt' );?>
        <input name="tt_annual_vip_discount" class="small-text code" value="<?php echo $annual_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="width:33%;float:left;"><?php _e( 'VIP永久会员折扣 ', 'tt' );?>
        <input name="tt_permanent_vip_discount" class="small-text code" value="<?php echo $permanent_vip_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('促销信息', 'tt'); ?>
    </p>
    <p style="width:20%;clear:both;"><?php _e( '优惠促销折扣(100代表原价)', 'tt' );?>
        <input name="tt_product_promote_discount" class="small-text code" value="<?php echo $site_discount; ?>" style="width:80px;height: 28px;"> %
    </p>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('淘宝链接', 'tt'); ?>
    </p>
    <p style="clear:both;"><?php _e( '购买渠道为淘宝时，请务必填写该项', 'tt' );?></p>
    <textarea name="tt_taobao_link" rows="2" class="large-text code"><?php echo $taobao_link;?></textarea>
    <p style="clear:both;font-weight:bold;border-bottom:1px solid #ddd;padding-bottom:8px;">
        <?php _e('付费内容', 'tt'); ?>
    </p>
    <p style="clear:both;"><?php _e( '付费查看下载链接,一行一个,每个资源格式为资源名|资源下载链接|密码', 'tt' );?></p>
    <textarea name="tt_product_download_links" rows="5" class="large-text code"><?php echo $download_links;?></textarea>
    <p style="clear:both;"><?php _e( '付费查看的内容信息', 'tt' );?></p>
    <textarea name="tt_product_pay_content" rows="5" class="large-text code"><?php echo $pay_content;?></textarea>

    <p style="clear:both;"><?php _e( '当前购买的用户邮箱', 'tt' );?></p>
    <textarea name="tt_product_buyer_emails" rows="6" class="large-text code"><?php echo $buyer_emails;?></textarea>

    <?php
}


/**
 * 保存文章时保存自定义数据
 *
 * @since 2.0.0
 * @param $post_id
 * @return void
 */
function tt_save_meta_box_data( $post_id ) {
    // 检查安全字段验证
//    if ( ! isset( $_POST['tt_meta_box_nonce'] ) ) {
//        return;
//    }
    // 检查安全字段的值
//    if ( ! wp_verify_nonce( $_POST['tt_meta_box_nonce'], 'tt_meta_box' ) ) {
//        return;
//    }
    // 检查是否自动保存，自动保存则跳出
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // 检查用户权限
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
    // 检查和更新字段
    if(isset($_POST['tt_embed_product'])) {
        update_post_meta($post_id, 'tt_embed_product', absint($_POST['tt_embed_product']));
    }

    if(isset($_POST['tt_source_title']) && isset($_POST['tt_source_link'])) {
        $cc = array(
            'source_title' => trim($_POST['tt_source_title']),
            'source_link' => trim($_POST['tt_source_link'])
        );
        update_post_meta($post_id, 'tt_post_copyright', maybe_serialize($cc));
    }

    if(isset($_POST['tt_free_dl'])/* && !empty($_POST['tt_free_dl'])*/) {
        update_post_meta($post_id, 'tt_free_dl', trim($_POST['tt_free_dl']));
    }

    if(isset($_POST['tt_sale_dl'])/* && !empty($_POST['tt_sale_dl'])*/) {
        update_post_meta($post_id, 'tt_sale_dl', trim($_POST['tt_sale_dl']));
    }

    if(isset($_POST['tt_sale_dl2'])/* && !empty($_POST['tt_sale_dl'])*/) {
        update_post_meta($post_id, 'tt_sale_dl2', trim($_POST['tt_sale_dl2']));
    }

    if(isset($_POST['tt_keywords']) && !empty($_POST['tt_keywords'])) {
        update_post_meta($post_id, 'tt_keywords', trim($_POST['tt_keywords']));
    }

    if(isset($_POST['tt_description']) && !empty($_POST['tt_description'])) {
        update_post_meta($post_id, 'tt_description', trim($_POST['tt_description']));
    }

    if(isset($_POST['tt_pay_currency'])){
        $currency = (int)$_POST['tt_pay_currency'] == 1 ? 1 : 0;
        update_post_meta($post_id, 'tt_pay_currency', $currency);
    }

    if(isset($_POST['tt_buy_channel'])){
        $channel = trim($_POST['tt_buy_channel']) == 'taobao' ? 'taobao' : 'instation';
        update_post_meta($post_id, 'tt_buy_channel', $channel);
    }

    if(isset($_POST['tt_taobao_link'])){
        update_post_meta($post_id, 'tt_taobao_link', esc_url($_POST['tt_taobao_link']));
    }

    // 文章下载信息保存更新
    // tt_postmeta_demo
    // tt_postmeta_ver
    // tt_postmeta_type
    // tt_postmeta_size
    // 
    if(isset($_POST['tt_postmeta_demo'])){
        update_post_meta($post_id, 'tt_postmeta_demo', esc_url($_POST['tt_postmeta_demo']));
    }

    if(isset($_POST['tt_postmeta_ver'])){
        update_post_meta($post_id, 'tt_postmeta_ver', trim($_POST['tt_postmeta_ver']));
    }

    if(isset($_POST['tt_postmeta_type'])){
        update_post_meta($post_id, 'tt_postmeta_type', trim($_POST['tt_postmeta_type']));
    }

    if(isset($_POST['tt_postmeta_size'])){
        update_post_meta($post_id, 'tt_postmeta_size', trim($_POST['tt_postmeta_size']));
    }



    if(isset($_POST['tt_product_price'])){
        update_post_meta($post_id, 'tt_product_price', abs($_POST['tt_product_price']));
    }

    if(isset($_POST['tt_product_quantity'])){
        update_post_meta($post_id, 'tt_product_quantity', absint($_POST['tt_product_quantity']));
    }

    if(isset($_POST['tt_product_promote_discount']) && isset($_POST['tt_monthly_vip_discount']) && isset($_POST['tt_annual_vip_discount']) && isset($_POST['tt_permanent_vip_discount'])) {
        $discount_summary = array(
            absint($_POST['tt_product_promote_discount']),
            absint($_POST['tt_monthly_vip_discount']),
            absint($_POST['tt_annual_vip_discount']),
            absint($_POST['tt_permanent_vip_discount'])
        );
        update_post_meta($post_id, 'tt_product_discount', maybe_serialize($discount_summary));
    }

    if(isset($_POST['tt_product_download_links'])){
        update_post_meta($post_id, 'tt_product_download_links', trim($_POST['tt_product_download_links']));
    }

    if(isset($_POST['tt_product_pay_content'])){
        update_post_meta($post_id, 'tt_product_pay_content', trim($_POST['tt_product_pay_content']));
    }
}
add_action( 'save_post', 'tt_save_meta_box_data' );

// load_func('func.Module');
/**
 * 加载header模板
 *
 * @since 2.0.0
 *
 * @param string $name 特殊header的名字
 */
function tt_get_header( $name = null ) {
    do_action( 'get_header', $name );

    $templates = array();
    $name = (string) $name;
    if ( '' !== $name ) {
        $templates[] = 'core/modules/mod.Header.' . ucfirst($name) . '.php';
    }

    $templates[] = 'core/modules/mod.Header.php';

    locate_template( $templates, true );
}


/**
 * 加载footer模板
 *
 * @since 2.0.0
 *
 * @param string $name 特殊footer的名字
 */
function tt_get_footer( $name = null ) {
    do_action( 'get_footer', $name );

    $templates = array();
    $name = (string) $name;
    if ( '' !== $name ) {
        $templates[] = 'core/modules/mod.Footer.' . ucfirst($name) . '.php';
    }

    $templates[] = 'core/modules/mod.Footer.php';

    locate_template( $templates, true );
}


/**
 * 加载自定义路径下的Sidebar
 *
 * @since   2.0.0
 *
 * @param   string  $name  特定Sidebar名
 * @return  void
 */
function tt_get_sidebar( $name = null ) {
    do_action( 'get_sidebar', $name );

    $templates = array();
    $name = (string) $name;
    if ( '' !== $name )
        $templates[] = 'core/modules/mod.Sidebar' . ucfirst($name) . '.php';

    $templates[] = 'core/modules/mod.Sidebar.php';

    locate_template( $templates, true );
}

// load_func('func.Optimization');
/* WordPress 后台禁用Google Open Sans字体，加速网站 */
function tt_remove_open_sans() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}
add_action( 'init', 'tt_remove_open_sans' );

/* 移除头部多余信息 */
function tt_remove_wp_version(){
    return;
}
add_filter('the_generator', 'tt_remove_wp_version'); //WordPress的版本号

remove_action('wp_head', 'feed_links', 2); //包含文章和评论的feed
remove_action('wp_head','index_rel_link'); //当前文章的索引
remove_action('wp_head', 'feed_links_extra', 3); //额外的feed,例如category, tag页
remove_action('wp_head', 'start_post_rel_link', 10); //开始篇
remove_action('wp_head', 'parent_post_rel_link', 10); //父篇
remove_action('wp_head', 'adjacent_posts_rel_link', 10); //上、下篇.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10); //rel=pre
remove_action('wp_head', 'wp_shortlink_wp_head', 10); //rel=shortlink
//remove_action('wp_head', 'rel_canonical' );

/* 阻止站内文章Pingback */
function tt_no_self_ping( &$links ) {
    $home = get_option('home');
    foreach ( $links as $key => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$key]);
}
add_action('pre_ping','tt_no_self_ping');

/* 添加链接功能 */
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

/* 登录用户浏览站点时不显示工具栏 */
add_filter('show_admin_bar', '__return_false');

/* 移除emoji相关脚本 */
remove_action( 'admin_print_scripts', 'print_emoji_detection_script');
remove_action( 'admin_print_styles', 'print_emoji_styles');
remove_action( 'wp_head', 'print_emoji_detection_script', 7);
remove_action( 'wp_print_styles', 'print_emoji_styles');
remove_action('embed_head',	'print_emoji_detection_script');
remove_filter( 'the_content_feed', 'wp_staticize_emoji');
remove_filter( 'comment_text_rss', 'wp_staticize_emoji');
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email');

function tt_disable_emoji_tiny_mce_plugin($plugins){
    return array_diff( $plugins, array( 'wpemoji' ) );
}
add_filter( 'tiny_mce_plugins', 'tt_disable_emoji_tiny_mce_plugin' );

/* 移除wp-embed等相关功能 */
//function tt_deregister_wp_embed_scripts(){
//    wp_deregister_script( 'wp-embed' );
//}
//add_action( 'wp_footer', 'tt_deregister_wp_embed_scripts' );
/**
 * Disable embeds on init.
 *
 * - Removes the needed query vars.
 * - Disables oEmbed discovery.
 * - Completely removes the related JavaScript.
 *
 * @since 1.0.0
 */
function tt_disable_embeds_init() {
    /* @var WP $wp */
    global $wp;

    // Remove the embed query var.
    $wp->public_query_vars = array_diff( $wp->public_query_vars, array(
        'embed',
    ) );

    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );

    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'tt_disable_embeds_tiny_mce_plugin' );

    // Remove all embeds rewrite rules.
    add_filter( 'rewrite_rules_array', 'tt_disable_embeds_rewrites' );

    // Remove filter of the oEmbed result before any HTTP requests are made.
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
}

add_action( 'init', 'tt_disable_embeds_init', 9999 );

/**
 * Removes the 'wpembed' TinyMCE plugin.
 *
 * @since 1.0.0
 *
 * @param array $plugins List of TinyMCE plugins.
 * @return array The modified list.
 */
function tt_disable_embeds_tiny_mce_plugin( $plugins ) {
    return array_diff( $plugins, array( 'wpembed' ) );
}
add_action('load-themes.php', 'tt_the_theme_versions');
/**
 * Remove all rewrite rules related to embeds.
 *
 * @since 1.2.0
 *
 * @param array $rules WordPress rewrite rules.
 * @return array Rewrite rules without embeds rules.
 */
function tt_disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos( $rewrite, 'embed=true' ) ) {
            unset( $rules[ $rule ] );
        }
    }

    return $rules;
}

/**
 * update theme version .
 *
 * @since 1.2.0
 */
function tt_new_theme_version() {
    $the_v = floatval (wp_get_theme()->get('Version'));
    $body = array();
    $url='https://api.ylit.cc/marketing/update.php';
    $request = new WP_Http;
    $result = $request->request( $url, array( 'method' => 'POST', 'body' => $body) );
    $jsondata = $result['body'];
    //判断是否有更新
    if ($jsondata > $the_v) {
        return true;
    }
}

function tt_the_theme_versions() {
    $the_v = floatval (wp_get_theme()->get('Version'));
    $body = array( 'name' => get_bloginfo('name'), 'version' => $the_v, 'domain' => get_bloginfo('url'), 'email' => get_bloginfo('admin_email') );
    $url='https://api.ylit.cc/marketing/update.php';
    $request = new WP_Http;
    $result = $request->request( $url, array( 'method' => 'POST', 'body' => $body) );
    $jsondata = $result['body'];
    return $jsondata;
}


/**
 * Remove embeds rewrite rules on theme activation.
 *
 * @since 1.2.0
 */
function tt_disable_embeds_remove_rewrite_rules() {
    add_filter( 'rewrite_rules_array', 'tt_disable_embeds_rewrites' );
    flush_rewrite_rules();
}
add_action('load-themes.php', 'tt_disable_embeds_remove_rewrite_rules');


/**
 * Flush rewrite rules on theme deactivation.
 *
 * @since 1.2.0
 */
function tt_disable_embeds_flush_rewrite_rules() {
    remove_filter( 'rewrite_rules_array', 'tt_disable_embeds_rewrites' );
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'tt_disable_embeds_flush_rewrite_rules');


/**
 * 搜索结果排除页面(商店的搜素结果不处理)
 *
 * @since 2.0.0
 * @param WP_Query $query
 * @return WP_Query
 */
function tt_search_filter_page($query) {
    if ($query->is_search) {
        if(isset($query->query['post_type']) && $query->query['post_type'] == 'product') return $query;
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts','tt_search_filter_page');


/**
 * 摘要长度
 *
 * @since 2.0.0
 * @param $length
 * @return mixed
 */
function tt_excerpt_length( $length ) {
    return tt_get_option('tt_excerpt_length', $length);
}
add_filter( 'excerpt_length', 'tt_excerpt_length', 999 );

/* 去除正文P标签包裹 */
//remove_filter( 'the_content', 'wpautop' );

/* 去除摘要P标签包裹 */
remove_filter( 'the_excerpt', 'wpautop' );

/* HTML转义 */
//取消内容转义
remove_filter('the_content', 'wptexturize');
//取消摘要转义
//remove_filter('the_excerpt', 'wptexturize');
//取消评论转义
//remove_filter('comment_text', 'wptexturize');

/* 在文本小工具不自动添加P标签 */
add_filter( 'widget_text', 'shortcode_unautop' );
/* 在文本小工具也执行短代码 */
add_filter( 'widget_text', 'do_shortcode' );


/* 找回上传图片路径设置 */
if(get_option('upload_path') == 'wp-content/uploads' || get_option('upload_path') == null){
    update_option('upload_path','wp-content/uploads');
}

/**
 * 中文名文件上传改名
 *
 * @since 2.0.0
 * @param $file
 * @return mixed
 */
function tt_custom_upload_name($file){
    if(preg_match('/[一-龥]/u',$file['name'])):
        $ext=ltrim(strrchr($file['name'],'.'),'.');
        $file['name'] = preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '_' . date('Y-m-d_H-i-s') . '.' . $ext;
    endif;
    return $file;
}
add_filter('wp_handle_upload_prefilter','tt_custom_upload_name', 5, 1);


/**
 * 替换文章或评论内容外链为内链
 *
 * @since 2.0.0
 * @param $content
 * @return mixed
 */
function tt_convert_to_internal_links($content){
    if(!tt_get_option('tt_disable_external_links', false)) {
        return $content;
    }
    preg_match_all('/\shref=(\'|\")(http[^\'\"#]*?)(\'|\")([\s]?)/', $content, $matches);
    if($matches){
        $home = home_url();
        $white_list = trim(tt_get_option('tt_external_link_whitelist', ''));
        $white_links = !empty($white_list) ? explode(PHP_EOL, $white_list) : array();
        array_push($white_links, $home);
        foreach($matches[2] as $val){
            $external = true;
            foreach ($white_links as $white_link) {
                if(strpos($val, $white_link)!==false) {
                    $external = false;
                    break;
                }
            }
            if($external===true){
                $rep = $matches[1][0].$val.$matches[3][0];
                $new = '"'. $home . '/redirect/' . base64_encode($val). '" target="_blank"';
                $content = str_replace("$rep", "$new", $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'tt_convert_to_internal_links', 99);
add_filter('comment_text', 'tt_convert_to_internal_links', 99);
add_filter('get_comment_author_link', 'tt_convert_to_internal_links', 99);


/**
 * WordPress文字标签关键词自动内链
 *
 * @since 2.0.0
 * @param $content
 * @return mixed
 */
function tt_tag_link($content){
    $match_num_from = 1;		//一篇文章中同一個標籤少於幾次不自動鏈接
    $match_num_to = 4;		//一篇文章中同一個標籤最多自動鏈接幾次
    $post_tags = get_the_tags();
    if (tt_get_option('tt_enable_k_post_tag_link', true) && $post_tags) {
        $sort_func = function($a, $b){
            if ( $a->name == $b->name ) return 0;
            return ( strlen($a->name) > strlen($b->name) ) ? -1 : 1;
        };
        usort($post_tags, $sort_func);
        $ex_word = '';
        $case = '';
        foreach($post_tags as $tag) {
            $link = get_tag_link($tag->term_id);
            $keyword = $tag->name;
            $cleankeyword = stripslashes($keyword);
            $url = "<a href=\"$link\" class=\"tag-tooltip\" data-toggle=\"tooltip\" title=\"" . str_replace('%s', addcslashes($cleankeyword, '$'),__('查看更多关于 %s 的文章', 'tt'))."\"";
            $url .= ' target="_blank"';
            $url .= ">".addcslashes($cleankeyword, '$')."</a>";
            $limit = rand($match_num_from,$match_num_to);
            $content = preg_replace( '|(<a[^>]+>)(.*)<pre.*?>('.$ex_word.')(.*)<\/pre>(</a[^>]*>)|U'.$case, '$1$2$4$5', $content);
            $content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2$4$5', $content);
            $cleankeyword = preg_quote($cleankeyword,'\'');
            $regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
            $content = preg_replace($regEx,$url,$content,$limit);
            $content = str_replace( '', stripslashes($ex_word), $content);
        }
    }
    return $content;
}
add_filter('the_content','tt_tag_link', 12, 1);


/**
 * 转换为内链的外链跳转处理
 *
 * @return bool
 */
function tt_handle_external_links_redirect() {
    $base_url = home_url('/redirect/');
    $request_url = Utils::getPHPCurrentUrl();
    if (substr($request_url, 0, strlen($base_url)) != $base_url) {
        return false;
    }
    $key = str_ireplace($base_url, '', $request_url);
    if (!empty($key)) {
        $external_url = base64_decode($key);
        wp_redirect( $external_url, 302 );
        exit;
    }
    return false;
}
add_action('template_redirect', 'tt_handle_external_links_redirect');


/**
 * 删除文章时删除自定义字段
 *
 * @since 2.0.0
 * @param $post_ID
 * @return void
 */
function tt_delete_custom_meta_fields($post_ID) {
    if(!wp_is_post_revision($post_ID)) {
        delete_post_meta($post_ID, 'tt_post_star_users');
        delete_post_meta($post_ID, 'tt_sidebar');
        delete_post_meta($post_ID, 'tt_latest_reviewed');
        delete_post_meta($post_ID, 'tt_keywords'); // page
        delete_post_meta($post_ID, 'tt_description'); // page
        delete_post_meta($post_ID, 'tt_product_price'); // product //TODO more
        delete_post_meta($post_ID, 'tt_product_quantity');
        delete_post_meta($post_ID, 'tt_pay_currency');
        delete_post_meta($post_ID, 'tt_product_sales');
        delete_post_meta($post_ID, 'tt_product_discount');
        delete_post_meta($post_ID, 'tt_buy_channel');
        delete_post_meta($post_ID, 'tt_taobao_link');
        delete_post_meta($post_ID, 'tt_latest_rated');
    }
    // TODO optimization: use sql to delete all at once
}
add_action('delete_post', 'tt_delete_custom_meta_fields');


/**
 * 删除文章时删除相关附件
 *
 * @since 2.0.0
 * @param $post_ID
 * @return void
 */
function tt_delete_post_and_attachments($post_ID) {
    global $wpdb;
    //删除特色图片
    $thumbnails = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID" );
    foreach ( $thumbnails as $thumbnail ) {
        wp_delete_attachment( $thumbnail->meta_value, true );
    }
    //删除图片附件
    $attachments = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_parent = $post_ID AND post_type = 'attachment'" );
    foreach ( $attachments as $attachment ) {
        wp_delete_attachment( $attachment->ID, true );
    }
    $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id = $post_ID" );
}
add_action('before_delete_post', 'tt_delete_post_and_attachments');

// load_func('func.Page');
/**
 * 获取页面模板，由于Marketing的模板文件存放目录不位于主题根目录，需要重写`get_page_templates`方法以获取
 *
 * @since   2.0.0
 *
 * @param   WP_Post|null    $post   当前编辑的页面实例，用于提供上下文环境
 * @return  array                   页面模板数组
 */
function tt_get_page_templates( $post = null ) {
    $theme = wp_get_theme();

    if ( $theme->errors() && $theme->errors()->get_error_codes() !== array( 'theme_parent_invalid' ) )
        return array();

    $page_templates = wp_cache_get( 'page_templates-' . md5('Marketing'), 'themes' );

    if ( ! is_array( $page_templates ) ) {
        $page_templates = array();
        $files = (array) Utils::scandir( THEME_TPL . '/page', 'php', 0 ); // Note: 主要这里重新定义扫描模板的文件夹/core/templates/page
        foreach ( $files as $file => $full_path ) {
            if ( ! preg_match( '|Template Name:(.*)$|mi', file_get_contents( $full_path ), $header ) )
                continue;
            $page_templates[ $file ] = _cleanup_header_comment( $header[1] );
        }
        wp_cache_add( 'page_templates-' . md5('Marketing'), $page_templates, 'themes', 1800 );
    }

    if ( $theme->load_textdomain() ) {
        foreach ( $page_templates as &$page_template ) {
            $page_template = translate( $page_template, 'tt' );
        }
    }

    $templates = (array) apply_filters( 'theme_page_templates', $page_templates, $theme, $post );

    return array_flip( $templates );
}


/**
 * Page编辑页面的页面属性meta_box内容回调，重写了`page_attributes_meta_box`，以支持自定义页面模板的路径和可用模板选项
 *
 * @since   2.0.0
 *
 * @param   WP_Post   $post   页面实例
 * @return  void
 */
function tt_page_attributes_meta_box($post) {
    $post_type_object = get_post_type_object($post->post_type);
    if ( $post_type_object->hierarchical ) {
        $dropdown_args = array(
            'post_type'        => $post->post_type,
            'exclude_tree'     => $post->ID,
            'selected'         => $post->post_parent,
            'name'             => 'parent_id',
            'show_option_none' => __('(no parent)'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );

        $dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
        $pages = wp_dropdown_pages( $dropdown_args );
        if ( ! empty($pages) ) {
            ?>
            <p><strong><?php _e('Parent', 'tt') ?></strong></p>
            <label class="screen-reader-text" for="parent_id"><?php _e('Parent', 'tt') ?></label>
            <?php echo $pages; ?>
            <?php
        }
    }

    if ( 'page' == $post->post_type && 0 != count( tt_get_page_templates( $post ) ) && get_option( 'page_for_posts' ) != $post->ID ) {
        $template = !empty($post->page_template) ? $post->page_template : false;
        ?>
        <p><strong><?php _e('Template', 'tt') ?></strong><?php
            do_action( 'page_attributes_meta_box_template', $template, $post );
            ?></p>
        <label class="screen-reader-text" for="page_template"><?php _e('Page Template', 'tt') ?></label><select name="tt_page_template" id="page_template">
            <?php
            $default_title = apply_filters( 'default_page_template_title',  __( 'Default Template', 'tt' ), 'meta-box' );
            ?>
            <option value="default"><?php echo esc_html( $default_title ); ?></option>
            <?php tt_page_template_dropdown($template); ?>
        </select>
        <?php
    } ?>
    <p><strong><?php _e('Order', 'tt') ?></strong></p>
    <p><label class="screen-reader-text" for="menu_order"><?php _e('Order', 'tt') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
    <?php if ( 'page' == $post->post_type && get_current_screen()->get_help_tabs() ) { ?>
        <p><?php _e( 'Need help? Use the Help tab in the upper right of your screen.', 'tt' ); ?></p>
        <?php
    }
}


/**
 * 移除默认并添加改写的Page编辑页面的页面属性meta_box，以支持自定义页面模板的路径和可用模板选项
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_replace_page_attributes_meta_box(){
    remove_meta_box('pageparentdiv', 'page', 'side');
    add_meta_box('tt_pageparentdiv', __('Page Attributes', 'tt'), 'tt_page_attributes_meta_box', 'page', 'side', 'low');
}
add_action('admin_init', 'tt_replace_page_attributes_meta_box');


/**
 * Page编辑页面的页面属性meta_box内页面模板下拉选项内容
 *
 * @since   2.0.0
 *
 * @param   string  $default    模板文件名
 * @return  string              Html代码
 */
function tt_page_template_dropdown( $default = '' ) {
    $templates = tt_get_page_templates( get_post() );
    ksort( $templates );
    foreach ( array_keys( $templates ) as $template ) {
        $full_path = 'core/templates/page/' . $templates[ $template ];
        $selected = selected( $default, $full_path, false );
        echo "\n\t<option value='" . $full_path . "' $selected>$template</option>";
    }
    return '';
}


/**
 * 保存页面时，保存模板的选择值
 *
 * @since   2.0.0
 * @param   int     $post_id    即将保存的文章ID
 * @return  void
 */
function tt_save_meta_box_page_template_data( $post_id ) {
    $post_id = intval($post_id);
    // 检查是否自动保存，自动保存则跳出
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // 检查用户权限
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }
    // 是否页面
    if ( !isset( $_POST['post_type'] ) || 'page' != trim($_POST['post_type']) ) {
        return;
    }

    if ( ! empty( $_POST['tt_page_template'] )) {
        $template = sanitize_text_field($_POST['tt_page_template']);
        $post = get_post($post_id);
        $post->page_template = $template;
        $page_templates = array_flip(tt_get_page_templates( $post ));
        if ( 'default' != $template && ! isset( $page_templates[ basename($template) ] ) ) {
            if ( tt_get_option('tt_theme_debug', false) ) {
                wp_die(__('The page template is invalid', 'tt'), __('Invalid Page Template', 'tt'));
            }
            update_post_meta( $post_id, '_wp_page_template', 'default' );
        } else {
            update_post_meta( $post_id, '_wp_page_template', $template );
        }
    }
}
add_action( 'save_post', 'tt_save_meta_box_page_template_data' );


/**
 * 给Body添加额外的class(部分自定义页面无法使用wp的body_class函数生成独特的class)
 *
 * @since 2.0.0
 * @param $classes
 * @return array
 */
function tt_modify_body_classes($classes) {
    if($query_var = get_query_var('site_util')) {
        $classes[] = 'site_util-' . $query_var;
    }elseif($query_var = get_query_var('me')) {
        $classes[] = 'me-' . $query_var;
    }elseif($query_var = get_query_var('uctab')) {
        $classes[] = 'uc-' . $query_var;
    }elseif($query_var = get_query_var('uc')) {
        $classes[] = 'uc-profile';
    }elseif($query_var = get_query_var('action')) {
        $classes[] = 'action-' . $query_var;
    }elseif($query_var = get_query_var('me_child_route')){
        $classes[] = 'me me-' . $query_var;
    }elseif($query_var = get_query_var('manage_child_route')){
        $query_var = get_query_var('manage_grandchild_route') ? substr($query_var, -2) : $query_var;
        $classes[] = 'manage manage-' . $query_var;
    }


    //TODO more
    return $classes;
}
add_filter('body_class', 'tt_modify_body_classes');

// load_func('func.PostMeta');
/**
 * 保存文章时添加最近变动字段
 *
 * @since   2.0.0
 * @param   $post_ID
 * @return  void
 */
function tt_add_post_review_fields($post_ID) {
    if(!wp_is_post_revision($post_ID)) {
        update_post_meta($post_ID, 'tt_latest_reviewed', time());
    }
}
add_action('save_post', 'tt_add_post_review_fields');

/**
 * 删除文章时删除最近变动字段
 *
 * @since   2.0.0
 * @param   $post_ID
 * @return  void
 */
function tt_delete_post_review_fields($post_ID) {
    if(!wp_is_post_revision($post_ID)) {
        delete_post_meta($post_ID, 'tt_latest_reviewed');
    }
}
add_action('delete_post', 'tt_delete_post_review_fields');

// load_func('func.Rewrite');
/**
 * Rewrite/Permalink/Routes
 */

/**
 * 强制使用伪静态
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_force_permalink(){
    if(!get_option('permalink_structure')){
        update_option('permalink_structure', '/%postname%.html');
        // TODO: 添加后台消息提示已更改默认固定链接，并请配置伪静态(伪静态教程等)
    }
}
add_action('load-themes.php', 'tt_force_permalink');


/**
 * 短链接
 *
 * @since   2.0.0
 *
 * @return  void | false
 */
function tt_rewrite_short_link(){
    // 短链接前缀, 如https://webapproach.net/go/xxx中的go，为了便于识别短链接
    $prefix = tt_get_option('tt_short_link_prefix', 'go');
    //$url = Utils::getCurrentUrl(); //该方法需要利用wp的query
    $url = Utils::getPHPCurrentUrl();
    preg_match('/\/' . $prefix . '\/([0-9A-Za-z]*)/i', $url, $matches);
    if(!$matches){
        return false;
    }
    $token = strtolower($matches[1]);
    $target_url = '';
    $records = tt_get_option('tt_short_link_records');
    $records = explode(PHP_EOL, $records);
    foreach ($records as $record){
        $record = explode('|', $record);
        if(count($record) < 2) continue;
        if(strtolower(trim($record[0])) === $token){
            $target_url = trim($record[1]);
            break;
        }
    }

    if($target_url){
        wp_redirect(esc_url_raw($target_url), 302);
        exit;
    }

    return false;
}
add_action('template_redirect','tt_rewrite_short_link');


/* Route : UCenter - e.g /@nickname/latest */

/**
 * 用户页路由(非默认作者页)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_set_user_page_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        // TODO: 用户链接前缀 `u` 是否可以自定义
        // Note: 用户名必须数字或字母组成，不区分大小写
//        if(stripos($ps, '%postname%') !== false){
//            // 默认为profile tab，但是链接不显示profile
//            $new_rules['@([一-龥a-zA-Z0-9]+)$'] = 'index.php?author_name=$matches[1]&uc=1';
//            // ucenter tabs
//            $new_rules['@([一-龥a-zA-Z0-9]+)/([A-Za-z]+)$'] = 'index.php?author_name=$matches[1]&uctab=$matches[2]&uc=1';
//            // 分页
//            $new_rules['@([一-龥a-zA-Z0-9]+)/([A-Za-z]+)/page/([0-9]{1,})$'] = 'index.php?author_name=$matches[1]&uctab=$matches[2]&uc=1&paged=$matches[3]';
//        }else{
        $new_rules['u/([0-9]{1,})$'] = 'index.php?author=$matches[1]&uc=1';
        $new_rules['u/([0-9]{1,})/([A-Za-z]+)$'] = 'index.php?author=$matches[1]&uctab=$matches[2]&uc=1';
        $new_rules['u/([0-9]{1,})/([A-Za-z]+)/page/([0-9]{1,})$'] = 'index.php?author=$matches[1]&uctab=$matches[2]&uc=1&tt_paged=$matches[3]';
//        }
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_set_user_page_rewrite_rules'); // filter `rewrite_rules_array` 也可用.


/**
 * 为自定义的用户页添加query_var白名单，用于识别和区分用户页及作者页
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_user_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'uc'; // 添加参数白名单uc，代表是用户中心页，采用用户模板而非作者模板
        $public_query_vars[] = 'uctab'; // 添加参数白名单uc，代表是用户中心页，采用用户模板而非作者模板
        $public_query_vars[] = 'tt_paged';
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_user_page_query_vars');


/**
 * 自定义作者页链接
 *
 * @since   2.0.0
 *
 * @param   string  $link   原始链接
 * @param   int     $author_id  作者ID
 * @return  string
 */
function tt_custom_author_link($link, $author_id){
    $ps = get_option('permalink_structure');
    if(!$ps){
        return $link;
    }
//    if(stripos($ps, '%postname%') !== false){
//        $nickname = get_user_meta($author_id, 'nickname', true);
//        // TODO: 解决nickname重复问题，用户保存资料时发出消息要求更改重复的名字，否则改为login_name，使用 `profile_update` action
//        return home_url('/@' . $nickname);
//    }
    return home_url('/u/' . strval($author_id));
}
add_filter('author_link', 'tt_custom_author_link', 10, 2);


/**
 * 用户链接解析Rewrite规则时正确匹配字段
 * // author_name传递的实际是nickname，而wp默认将其做login_name处理，需要修复
 * 同时对使用原始默认作者页链接的重定向至新的自定义链接
 *
 * @since   2.0.0
 *
 * @param   array   $query_vars   全局查询变量
 * @return  array
 */
function tt_match_author_link_field($query_vars){
    if (is_admin()) {
        return $query_vars;
    }
    if(array_key_exists('author_name', $query_vars)){
        $nickname = $query_vars['author_name'];
        global $wpdb;
        $author_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE `meta_key` = 'nickname' AND `meta_value` = %s ORDER BY user_id ASC LIMIT 1", sanitize_text_field($nickname)));
        $logged_user_id = get_current_user_id();

        // 如果是原始author链接访问，重定向至新的自定义链接 /author/nickname -> /@nickname
        if(!array_key_exists('uc', $query_vars)){
            //wp_redirect(home_url('/@' . $nickname), 301);
            wp_redirect(get_author_posts_url($author_id), 301);
            exit;
        }

        // 对不不合法的/@nickname/xxx子路由，直接drop `author_name` 变量以引向404
        if(array_key_exists('uctab', $query_vars) && $uc_tab = $query_vars['uctab']){
            if($uc_tab === 'profile'){
                // @see func.Template.php - tt_get_user_template
                //wp_redirect(home_url('/@' . $nickname), 301);
                wp_redirect(get_author_posts_url($author_id), 301);
                exit;
            }elseif(!in_array($uc_tab, (array)json_decode(ALLOWED_UC_TABS)) || ($uc_tab === 'chat' && $logged_user_id == $author_id)){
                unset($query_vars['author_name']);
                unset($query_vars['uctab']);
                unset($query_vars['uc']);
                $query_vars['error'] = '404';
                return $query_vars;
            }elseif($uc_tab === 'chat' && !$logged_user_id){
                // 用户未登录, 跳转至登录页面
                wp_redirect(tt_add_redirect(tt_url_for('signin'), get_author_posts_url($author_id) . '/chat'), 302);
                exit;
            }
        }

        // 新链接访问时 /@nickname
        if($author_id){
            $query_vars['author'] = $author_id;
            unset($query_vars['author_name']);
        }
        // 找不对匹配nickname的用户id则将nickname当作display_name解析 // TODO: 是否需要按此解析，可能导致不可预见的错误
        return $query_vars;
    }elseif(array_key_exists('author', $query_vars)){
        $logged_user_id = get_current_user_id();
        $author_id = $query_vars['author'];
        // 如果是原始author链接访问，重定向至新的自定义链接 /author/nickname -> /u/57
        if(!array_key_exists('uc', $query_vars)){
            wp_redirect(get_author_posts_url($author_id), 301);
            exit;
        }

        // 对不不合法的/u/57/xxx子路由，引向404
        if(array_key_exists('uctab', $query_vars) && $uc_tab = $query_vars['uctab']){
            if($uc_tab === 'profile'){
                wp_redirect(get_author_posts_url($author_id), 301);
                exit;
            }elseif(!in_array($uc_tab, (array)json_decode(ALLOWED_UC_TABS)) || ($uc_tab === 'chat' && $logged_user_id == $author_id)){
                unset($query_vars['author_name']);
                unset($query_vars['author']);
                unset($query_vars['uctab']);
                unset($query_vars['uc']);
                $query_vars['error'] = '404';
                return $query_vars;
            }elseif($uc_tab === 'chat' && !$logged_user_id){
                // 用户未登录, 跳转至登录页面
                wp_redirect(tt_add_redirect(tt_url_for('signin'), get_author_posts_url($author_id) . '/chat'), 302);
                exit;
            }
        }
        return $query_vars;
    }
    return $query_vars;
}
add_filter('request', 'tt_match_author_link_field', 10, 1);


/* Route : Me - e.g /me/notifications/all */

/**
 * /me主路由处理
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_redirect_me_main_route(){
    if(preg_match('/^\/me([^\/]*)$/i', $_SERVER['REQUEST_URI'])){
        if($user_id = get_current_user_id()){
            //$nickname = get_user_meta(get_current_user_id(), 'nickname', true);
            wp_redirect(get_author_posts_url($user_id), 302);
        }else{
            wp_redirect(tt_signin_url(tt_get_current_url()), 302);
        }
        exit;
    }
}
add_action('init', 'tt_redirect_me_main_route'); //the `init` hook is typically used by plugins to initialize. The current user is already authenticated by this time.


/**
 * /me子路由处理 - Rewrite
 *
 * @since   2.0.0
 *
 * @param   object   $wp_rewrite   WP_Rewrite
 * @return  object
 */
function tt_handle_me_child_routes_rewrite($wp_rewrite){
    if(get_option('permalink_structure')){
        // Note: me子路由与孙路由必须字母组成，不区分大小写
        $new_rules['me/([a-zA-Z]+)$'] = 'index.php?me_child_route=$matches[1]&is_me_route=1';
        $new_rules['me/([a-zA-Z]+)/([a-zA-Z]+)$'] = 'index.php?me_child_route=$matches[1]&me_grandchild_route=$matches[2]&is_me_route=1';
        $new_rules['me/order/([0-9]{1,})$'] = 'index.php?me_child_route=order&me_grandchild_route=$matches[1]&is_me_route=1'; // 我的单个订单详情
        $new_rules['me/editpost/([0-9]{1,})$'] = 'index.php?me_child_route=editpost&me_grandchild_route=$matches[1]&is_me_route=1'; // 编辑文章
        // 分页
        $new_rules['me/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?me_child_route=$matches[1]&is_me_route=1&paged=$matches[2]';
        $new_rules['me/([a-zA-Z]+)/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?me_child_route=$matches[1]&me_grandchild_route=$matches[2]&is_me_route=1&paged=$matches[3]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_me_child_routes_rewrite');


/**
 * /me子路由处理 - Template
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_handle_me_child_routes_template(){
    $is_me_route = strtolower(get_query_var('is_me_route'));
    $me_child_route = strtolower(get_query_var('me_child_route'));
    $me_grandchild_route = strtolower(get_query_var('me_grandchild_route'));
    if($is_me_route && $me_child_route){
        global $wp_query;
        if($wp_query->is_404()) {
            return;
        }

        //非Home
        $wp_query->is_home = false;

        //未登录的跳转到登录页
        if(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }

        $allow_routes = (array)json_decode(ALLOWED_ME_ROUTES);
        $allow_child = array_keys($allow_routes);
        // 非法的子路由处理
        if(!in_array($me_child_route, $allow_child)){
            Utils::set404();
            return;
        }
        // 对于order/8单个我的订单详情路由，孙路由必须是数字
        // 对于editpost/8路由，孙路由必须是数字
        if($me_child_route === 'order' && (!$me_grandchild_route || !preg_match('/([0-9]{1,})/', $me_grandchild_route))){
            Utils::set404();
            return;
        }
        if($me_child_route === 'editpost' && (!$me_grandchild_route || !preg_match('/([0-9]{1,})/', $me_grandchild_route))){
            Utils::set404();
            return;
        }
        if($me_child_route !== 'order' && $me_child_route !== 'editpost'){
            $allow_grandchild = $allow_routes[$me_child_route];
            // 对于可以有孙路由的一般不允许直接子路由，必须访问孙路由，比如/me/notifications 必须跳转至/me/notifications/all
            if(empty($me_grandchild_route) && is_array($allow_grandchild)){
                wp_redirect(home_url('/me/' . $me_child_route . '/' . $allow_grandchild[0]), 302);
                exit;
            }
            // 非法孙路由处理
            if(is_array($allow_grandchild) && !in_array($me_grandchild_route, $allow_grandchild)) {
                Utils::set404();
                return;
            }
        };
        $template = THEME_TPL . '/me/tpl.Me.' . ucfirst($me_child_route) . '.php';
        load_template($template);
        exit;
    }
}
add_action('template_redirect', 'tt_handle_me_child_routes_template', 5);


/**
 * 为自定义的当前用户页(Me)添加query_var白名单
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_me_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'is_me_route';
        $public_query_vars[] = 'me_child_route';
        $public_query_vars[] = 'me_grandchild_route';
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_me_page_query_vars');


/* Route : Action - e.g /m/signin */

/**
 * 登录/注册/注销等动作页路由(/m)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_handle_action_page_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        //action (signin|signup|signout|refresh)
        // m->move(action)
        $new_rules['m/([A-Za-z_-]+)$'] = 'index.php?action=$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_action_page_rewrite_rules');


/**
 * 为自定义的Action页添加query_var白名单
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_action_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'action'; // 添加参数白名单action，代表是各种动作页
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_action_page_query_vars');


/**
 * 登录/注册/注销等动作页模板
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_handle_action_page_template(){
    $action = strtolower(get_query_var('action'));
    $allowed_actions = (array)json_decode(ALLOWED_M_ACTIONS);
    if($action && in_array($action, array_keys($allowed_actions))){
        global $wp_query;
        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
        $template = THEME_TPL . '/actions/tpl.M.' . ucfirst($allowed_actions[$action]) . '.php';
        load_template($template);
        exit;
    }elseif($action && !in_array($action, array_keys($allowed_actions))){
        // 非法路由处理
        Utils::set404();
        return;
    }
}
add_action('template_redirect', 'tt_handle_action_page_template', 5);


/* Route : OAuth - e.g /oauth/qq */

/**
 * OAuth登录处理页路由(/oauth)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_handle_oauth_page_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        //oauth (qq|weibo|weixin|...)
        $new_rules['oauth/([A-Za-z]+)$'] = 'index.php?oauth=$matches[1]';
        $new_rules['oauth/([A-Za-z]+)/last$'] = 'index.php?oauth=$matches[1]&oauth_last=1';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_oauth_page_rewrite_rules');


/**
 * 为自定义的Action页添加query_var白名单
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_oauth_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'oauth'; // 添加参数白名单oauth，代表是各种OAuth登录处理页
        $public_query_vars[] = 'oauth_last'; // OAuth登录最后一步，整合WP账户，自定义用户名
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_oauth_page_query_vars');


/**
 * OAuth登录处理页模板
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_handle_oauth_page_template(){
    $oauth = strtolower(get_query_var('oauth'));
    $oauth_last = get_query_var('oauth_last');
    if($oauth){
        if(in_array($oauth, (array)json_decode(ALLOWED_OAUTH_TYPES))):
            global $wp_query;
            $wp_query->is_home = false;
            $wp_query->is_page = true; //将该模板改为页面属性，而非首页
            $template = $oauth_last ? THEME_TPL . '/oauth/tpl.OAuth.Last.php' : THEME_TPL . '/oauth/tpl.OAuth.php';
            load_template($template);
            exit;
        else:
            // 非法路由处理
            Utils::set404();
            return;
        endif;
    }
}
add_action('template_redirect', 'tt_handle_oauth_page_template', 5);


/* Route : Site - e.g /site/upgradebrowser */

/**
 * 网站级工具页路由(如浏览器升级提示、全站通告等)(/site)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_handle_site_util_page_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        //site_util (upgradeBrowser)
        $new_rules['site/([A-Za-z_-]+)$'] = 'index.php?site_util=$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'tt_handle_site_util_page_rewrite_rules');


/**
 * 为自定义的Site Util页添加query_var白名单
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_site_util_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'site_util'; // site_util，代表是网站级别的工具页面
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_site_util_page_query_vars');


/**
 * 网站级工具页模板
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_handle_site_util_page_template(){
    $util = get_query_var('site_util');
    $allowed_utils = (array)json_decode(ALLOWED_SITE_UTILS);
    if($util && in_array(strtolower($util), array_keys($allowed_utils))){
        global $wp_query;

//        if($wp_query->is_404()) {
//            return;
//        }

        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
        $template = THEME_TPL . '/site/tpl.' . ucfirst($allowed_utils[$util]) . '.php';
        load_template($template);
        exit;
    } elseif ($util) {
        // 非法路由处理
        Utils::set404();
        return;
    }
}
add_action('template_redirect', 'tt_handle_site_util_page_template', 5);


/* Route : Static - e.g /static/css/main.css */

/**
 * 静态路由，去除静态文件链接中的wp-content等字样(/static)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_handle_static_file_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        $explode_path = explode('/themes/', THEME_DIR);
        $theme_name = next($explode_path);
        //static files route
        $new_rules = array(
            'static/(.*)' => 'wp-content/themes/' . $theme_name . '/assets/$1'
        );
        $wp_rewrite->non_wp_rules = $new_rules + $wp_rewrite->non_wp_rules;
    }
}
//add_action('generate_rewrite_rules', 'tt_handle_static_file_rewrite_rules');  // TODO: 需要Apache支持，或者同样Nginx对应方法


/* Route : API - e.g /api/post/1 */

/**
 * REST API路由，wp-json路由别名(/api)
 *
 * @since   2.0.0
 *
 * @param   object  $wp_rewrite  WP_Rewrite
 * @return  void
 */
function tt_handle_api_rewrite_rules($wp_rewrite){
    if($ps = get_option('permalink_structure')){
        $new_rules = array();
        $new_rules['^api/?$'] = 'index.php?rest_route=/';
        $new_rules['^api/(.*)?'] = 'index.php?rest_route=/$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
//add_action('generate_rewrite_rules', 'tt_handle_api_rewrite_rules'); //直接用 `rest_url_prefix` 更改wp-json至api @see core/api/api.Config.php


/* Route : Management - e.g /management/users */

/**
 * /management主路由处理
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_redirect_management_main_route(){
    if(preg_match('/^\/management([^\/]*)$/i', $_SERVER['REQUEST_URI'])){
        if(current_user_can('administrator')){
            //$nickname = get_user_meta(get_current_user_id(), 'nickname', true);
            wp_redirect(tt_url_for('manage_status'), 302);
        }elseif(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }elseif(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }else{
            Utils::set404();
            return;
        }
        exit;
    }
    if(preg_match('/^\/management\/orders$/i', $_SERVER['REQUEST_URI'])){
        if(current_user_can('administrator')){
            wp_redirect(tt_url_for('manage_orders'), 302); // /management/orders -> management/orders/all
        }elseif(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }elseif(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }else{
            Utils::set404();
            return;
        }
        exit;
    }
}
add_action('init', 'tt_redirect_management_main_route'); //the `init` hook is typically used by plugins to initialize. The current user is already authenticated by this time.


/**
 * /management子路由处理 - Rewrite
 *
 * @since   2.0.0
 *
 * @param   object   $wp_rewrite   WP_Rewrite
 * @return  object
 */
function tt_handle_management_child_routes_rewrite($wp_rewrite){
    if(get_option('permalink_structure')){
        // Note: management子路由与孙路由必须字母组成，不区分大小写
        $new_rules['management/([a-zA-Z]+)$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1';
        //$new_rules['management/([a-zA-Z]+)/([a-zA-Z]+)$'] = 'index.php?manage_child_route=$matches[1]&manage_grandchild_route=$matches[2]&is_manage_route=1';
        $new_rules['management/orders/([a-zA-Z0-9]+)$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1';
        $new_rules['management/users/([a-zA-Z0-9]+)$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1';
        // 分页
        $new_rules['management/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $new_rules['management/orders/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $new_rules['management/users/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
    return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'tt_handle_management_child_routes_rewrite');


/**
 * /management子路由处理 - Template
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_handle_manage_child_routes_template(){
    $is_manage_route = strtolower(get_query_var('is_manage_route'));
    $manage_child_route = strtolower(get_query_var('manage_child_route'));
    $manage_grandchild_route = strtolower(get_query_var('manage_grandchild_route'));
    if($is_manage_route && $manage_child_route){
        //非Home
        global $wp_query;
        $wp_query->is_home = false;

        if($wp_query->is_404()) {
            return;
        }

       //未登录的跳转到登录页
        if(!is_user_logged_in()) {
            wp_redirect(tt_add_redirect(tt_url_for('signin'), tt_get_current_url()), 302);
            exit;
        }

        //非管理员403处理
        if(!current_user_can('edit_users')) {
            wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
        }


        $allow_routes = (array)json_decode(ALLOWED_MANAGE_ROUTES);
        $allow_child = array_keys($allow_routes);
        // 非法的子路由处理
        if(!in_array($manage_child_route, $allow_child)){
            Utils::set404();
            return;
        }

        if($manage_child_route === 'orders' && $manage_grandchild_route){
            if(preg_match('/([0-9]{1,})/', $manage_grandchild_route)){ // 对于orders/8单个订单详情路由，孙路由必须是数字
                $template = THEME_TPL . '/management/tpl.Manage.Order.php';
                load_template($template);
                exit;
            }elseif(in_array($manage_grandchild_route, $allow_routes['orders'])){ // 对于orders/all 指定类型订单列表路由，孙路由是all/cash/credit之中
                $template = THEME_TPL . '/management/tpl.Manage.Orders.php';
                load_template($template);
                exit;
            }
            Utils::set404();
            return;
        }
        if($manage_child_route === 'users' && $manage_grandchild_route){
            if(preg_match('/([0-9]{1,})/', $manage_grandchild_route)){ // 对于users/57单个订单详情路由，孙路由必须是数字
                $template = THEME_TPL . '/management/tpl.Manage.User.php';
                load_template($template);
                exit;
            }elseif(in_array($manage_grandchild_route, $allow_routes['users'])){ // 对于users/all 指定类型订单列表路由，孙路由是all/administrator/editor/author/contributor/subscriber之中
                $template = THEME_TPL . '/management/tpl.Manage.Users.php';
                load_template($template);
                exit;
            }
            Utils::set404();
            return;
        }
        if($manage_child_route !== 'orders' && $manage_child_route !== 'users'){
            // 除orders/users外不允许有孙路由
            if($manage_grandchild_route) {
                Utils::set404();
                return;
            }
        };
        $template_id = ucfirst($manage_child_route);
        $template = THEME_TPL . '/management/tpl.Manage.' . $template_id . '.php';
        load_template($template);
        exit;
    }
}
add_action('template_redirect', 'tt_handle_manage_child_routes_template', 5);


/**
 * 为自定义的管理页添加query_var白名单
 *
 * @since   2.0.0
 *
 * @param   object  $public_query_vars  公共全局query_vars
 * @return  object
 */
function tt_add_manage_page_query_vars($public_query_vars) {
    if(!is_admin()){
        $public_query_vars[] = 'is_manage_route';
        $public_query_vars[] = 'manage_child_route';
        $public_query_vars[] = 'manage_grandchild_route';
    }
    return $public_query_vars;
}
add_filter('query_vars', 'tt_add_manage_page_query_vars');


/**
 * 刷新固定链接缓存
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_refresh_rewrite(){
    // 如果启用了memcache等对象缓存，固定链接的重写规则缓存对应清除
    wp_cache_flush();

    // 刷新固定链接
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

// load_func('func.Robots');
/**
 * 对于部分链接，拒绝搜索引擎索引
 *
 * @since   2.0.0
 *
 * @param   string  $output    Robots.txt内容
 * @param   bool    $public
 * @return  string
 */
function tt_robots_modification( $output, $public ){
    $output .= "\nDisallow: /oauth";
    $output .= "\nDisallow: /m";
    $output .= "\nDisallow: /me";
    return $output;
}
add_filter( 'robots_txt', 'tt_robots_modification', 10, 2 );


/**
 * 为部分页面添加noindex的meta标签
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_add_noindex_meta(){
    if(get_query_var('is_uc') || get_query_var('action') || get_query_var('site_util') || get_query_var('is_me_route')){
        wp_no_robots();
    }
}
add_action('wp_head', 'tt_add_noindex_meta');

// load_func('func.Schedule');
/**
 * 添加周循环的定时任务周期选项
 *
 * @since   2.0.0
 *
 * @param   array   $schedules
 * @return  array
 */
function tt_cron_add_weekly( $schedules ){
    $schedules['weekly'] = array(
        'interval' => 604800, // 1周 = 60秒 * 60分钟 * 24小时 * 7天
        'display' => __('Weekly','tt')
    );
    return $schedules;
}
add_filter('cron_schedules', 'tt_cron_add_weekly');


/**
 * 每小时执行的定时任务
 *
 * @since   2.0.0
 * @return  void
 */
function tt_setup_common_hourly_schedule() {
    if ( ! wp_next_scheduled( 'tt_setup_common_hourly_event' ) ) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event( 1471708800, 'hourly', 'tt_setup_common_hourly_event');
    }
}
add_action( 'wp', 'tt_setup_common_hourly_schedule' );


/**
 * 每天执行的定时任务
 *
 * @since   2.0.0
 * @return  void
 */
function tt_setup_common_daily_schedule() {
    if ( ! wp_next_scheduled( 'tt_setup_common_daily_event' ) ) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event( 1471708800, 'daily', 'tt_setup_common_daily_event');
    }
}
add_action( 'wp', 'tt_setup_common_daily_schedule' );


/**
 * 每两天执行的定时任务
 *
 * @since   2.0.0
 * @return  void
 */
function tt_setup_common_twicedaily_schedule() {
    if ( ! wp_next_scheduled( 'tt_setup_common_twicedaily_event' ) ) {
        // 1471708800是北京2016年8月21日00:00:00时间戳
        wp_schedule_event( 1471708800, 'twicedaily', 'tt_setup_common_twicedaily_event');
    }
}
add_action( 'wp', 'tt_setup_common_twicedaily_schedule' );


/**
 * 每周执行的定时任务
 *
 * @since   2.0.0
 * @return  void
 */
function tt_setup_common_weekly_schedule() {
    if ( ! wp_next_scheduled( 'tt_setup_common_weekly_event' ) ) {
        // 1471795200是北京2016年8月22日 星期一 00:00:00时间戳
        wp_schedule_event( 1471795200, 'twicedaily', 'tt_setup_common_weekly_event');
    }
}
add_action( 'wp', 'tt_setup_common_weekly_schedule' );

// load_func('func.Script');
/**
 * 注册Scripts
 *
 * @since   2.0.0
 *
 * @return  void
 */
function tt_register_scripts() {
    $jquery_url = json_decode(JQUERY_SOURCES)->{tt_get_option('tt_jquery', 'local_1')};
    wp_register_script( 'tt_jquery', $jquery_url, array(), null, tt_get_option('tt_foot_jquery', false) );
    //wp_register_script( 'tt_common', THEME_CDN_ASSET . '/js/' . JS_COMMON, array(), null, true );
    wp_register_script( 'tt_home', THEME_CDN_ASSET . '/js/' . JS_HOME, array(), null, true );
    wp_register_script( 'tt_front_page', THEME_CDN_ASSET . '/js/' . JS_FRONT_PAGE, array(), null, true );
    wp_register_script( 'tt_single_post', THEME_CDN_ASSET . '/js/' . JS_SINGLE, array(), null, true );
    wp_register_script( 'tt_single_page', THEME_CDN_ASSET . '/js/' . JS_PAGE, array(), null, true );
    wp_register_script( 'tt_archive_page', THEME_CDN_ASSET . '/js/' . JS_ARCHIVE, array(), null, true );
    wp_register_script( 'tt_product_page', THEME_CDN_ASSET . '/js/' . JS_PRODUCT, array(), null, true );
    wp_register_script( 'tt_products_page', THEME_CDN_ASSET . '/js/' . JS_PRODUCT_ARCHIVE, array(), null, true );
    wp_register_script( 'tt_uc_page', THEME_CDN_ASSET . '/js/' . JS_UC, array(), null, true );
    wp_register_script( 'tt_me_page', THEME_CDN_ASSET . '/js/' . JS_ME, array(), null, true );
    wp_register_script( 'tt_action_page', THEME_CDN_ASSET . '/js/' . JS_ACTION, array(), null, true );
    wp_register_script( 'tt_404_page', THEME_CDN_ASSET . '/js/' . JS_404, array(), null, true );
    wp_register_script( 'tt_site_utils', THEME_CDN_ASSET . '/js/' . JS_SITE_UTILS, array(), null, true);
    wp_register_script( 'tt_oauth_page', THEME_CDN_ASSET . '/js/' . JS_OAUTH, array(), null, true);
    wp_register_script( 'tt_manage_page', THEME_CDN_ASSET . '/js/' . JS_MANAGE, array(), null, true);

    global $tt_auth_config;
    $data = array(
        'debug'             => tt_get_option('tt_theme_debug', false),
        'uid'               => get_current_user_id(),
        'language'          => get_option('WPLANG', 'zh_CN'),
        'apiRoot'           => esc_url_raw( get_rest_url() ),
        '_wpnonce'          => wp_create_nonce( 'wp_rest' ), // REST_API服务验证该nonce, 如果不提供将清除登录用户信息  @see rest-api.php `rest_cookie_check_errors`
        'home'              => esc_url_raw( home_url() ),
        'themeRoot'         => THEME_URI,
        'isHome'            => is_home(),
        'commentsPerPage'   => tt_get_option('tt_comments_per_page', 20),
        'sessionApiTail'    => tt_get_option('tt_session_api', 'session'),
        'contributePostWordsMin' => absint(tt_get_option('tt_contribute_post_words_min', 100)),
        'o'                 => $tt_auth_config['order'],
        'e'                 => get_bloginfo ('admin_email'),
        'v'                 => trim(wp_get_theme()->get('Version')),
        'yzApi'             => tt_get_option('tt_youzan_util_api', '')
    );
    if(is_single()) {
        $data['isSingle'] = true;
        $data['pid'] = get_queried_object_id();
    }
    //wp_localize_script( 'tt_common', 'TT', $data );
    wp_enqueue_script( 'tt_jquery' );
    //wp_enqueue_script( 'tt_common' );
    $script = '';
    if(is_home()) {
        $script = 'tt_home';
    }elseif(is_single()) {
        $script = get_post_type()==='product' ? 'tt_product_page' : (get_post_type()==='bulletin' ? 'tt_single_page' : 'tt_single_post');
    }elseif((is_archive() && !is_author()) || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1)) {
        $script = get_post_type()==='product' || (is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1) ? 'tt_products_page' : 'tt_archive_page';
    }elseif(is_author()) {
        $script = 'tt_uc_page';
    }elseif(is_404()) {
        $script = 'tt_404_page';
    }elseif(get_query_var('is_me_route')) {
        $script = 'tt_me_page';
    }elseif(get_query_var('action')) {
        $script = 'tt_action_page';
    }elseif(is_front_page()) {
        $script = 'tt_front_page';
    }elseif(get_query_var('site_util')){
        $script = 'tt_site_utils';
    }elseif(get_query_var('oauth')){
        $script = 'tt_oauth_page';
    }elseif(get_query_var('is_manage_route')){
        $script = 'tt_manage_page';
    }else{
        // is_page() ?
        $script = 'tt_single_page';
    }

    if($script) {
        wp_localize_script( $script, 'TT', $data );
        wp_enqueue_script( $script );
    }
}
add_action( 'wp_enqueue_scripts', 'tt_register_scripts' );

// load_func('func.Seo');
/**
 * 根据页面输出相应标题
 *
 * @since   2.0.0
 * @return  string
 */
function tt_get_page_title() {
    $title = '';
    if($action = get_query_var('action')) {
        switch ($action) {
            case 'signin':
                $title = __('Sign In', 'tt');
                break;
            case 'signup':
                $title = __('Sign Up', 'tt');
                break;
            case 'activate':
                $title = __('Activate Registration', 'tt');
                break;
            case 'signout':
                $title = __('Sign Out', 'tt');
                break;
            case 'findpass':
                $title = __('Find Password', 'tt');
                break;
            case 'resetpass':
                $title = __('Reset Password', 'tt');
                break;
        }
        return $title . ' - ' . get_bloginfo('name');
    }
    if($me_route = get_query_var('me_child_route')) {
        switch ($me_route) {
            case 'settings':
                $title = __('My Settings', 'tt');
                break;
            case 'notifications':
                $title = __('My Notifications', 'tt');
                break;
            case 'messages': //TODO grandchild route in/out msgbox
                $title = __('My Messages', 'tt');
                break;
            case 'stars':
                $title = __('My Stars', 'tt');
                break;
            case 'credits':
                $title = __('My Credits', 'tt');
                break;
            case 'cash':
                $title = __('My Cash', 'tt');
                break;
            case 'orders':
                $title = __('My Orders', 'tt');
                break;
            case 'order':
                $title = __('My Order', 'tt');
                break;
            case 'drafts':
                $title = __('My Drafts', 'tt');
                break;
            case 'newpost':
                $title = __('New Post', 'tt');
                break;
            case 'editpost':
                $title = __('Edit Post', 'tt');
                break;
            case 'membership':
                $title = __('My Membership', 'tt');
                break;
        }
        return $title . ' - ' . get_bloginfo('name');
    }
    if($site_util = get_query_var('site_util')) {
        switch ($site_util){
            case 'checkout':
                $title = __('Check Out Orders', 'tt');
                break;
            case 'payresult':
                $title = __('Payment Result', 'tt');
                break;
            case 'qrpay':
            case 'youzanpay':
                $title = __('Do Payment', 'tt');
                break;
            case 'download':
                global $origin_post;
                $title = __('Resources Download:', 'tt') . $origin_post->post_title;
                break;
            case 'privacy-policies-and-terms':
                $title = __('Privacy Policies and Terms', 'tt');
                break;
            // TODO more
        }
        return $title . ' - ' . get_bloginfo('name');
    }
    if($oauth = get_query_var('oauth') && get_query_var('oauth_last')) {
        switch ($oauth) {
            case 'qq':
                $title = __('Complete Account Info - QQ Connect', 'tt');
                break;
            case 'weibo':
                $title = __('Complete Account Info - Weibo Connect', 'tt');
                break;
            case 'weixin':
                $title = __('Complete Account Info - Weixin Connect', 'tt');
                break;
        }
        return $title . ' - ' . get_bloginfo('name');
    }
    if($site_manage = get_query_var('manage_child_route')) {
        switch ($site_manage) {
            case 'status':
                $title = __('Site Statistic', 'tt');
                break;
            case 'posts':
                $title = __('Posts Management', 'tt');
                break;
            case 'comments':
                $title = __('Comments Management', 'tt');
                break;
            case 'users':
                $title = __('Users Management', 'tt');
                break;
            case 'orders':
                $title = __('Orders Management', 'tt');
                break;
            case 'coupons':
                $title = __('Coupons Management', 'tt');
                break;
            case 'cards':
                $title = __('Cards Management', 'tt');
                break;
            case 'members':
                $title = __('Members Management', 'tt');
                break;
            case 'products':
                $title = __('Products Management', 'tt');
                break;
        }
        return $title . ' - ' . get_bloginfo('name');
    }
    if(is_home() || is_front_page()) {
        $title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
    }elseif(is_single()&&get_post_type() != 'product') {
        $title = trim(wp_title('', false));
        if($page = get_query_var('page') && get_query_var('page') > 1){
            $title .= sprintf(__(' - Page %d','tt'), $page);
        }
        $title .= ' - ' . get_bloginfo('name');
    }elseif(is_page()){
        $title = trim(wp_title('', false)) . ' - ' . get_bloginfo('name');
    }elseif(is_category()) {
        $category = get_queried_object();
        $des = $category->description ? $category->description . ' - ' : '';
        $title = $category->cat_name . ' - ' . $des . get_bloginfo('name');
    }elseif(is_author()){
        // TODO more tab titles
        $author = get_queried_object();
        $name = $author->data->display_name;
        $title = sprintf(__('%s\'s Home Page', 'tt'), $name) . ' - ' . get_bloginfo('name');
    }elseif(get_post_type() == 'product'){
        if(is_archive()){
            if(tt_is_product_category()) {
                $title = get_queried_object()->name . ' - ' . __('Product Category', 'tt');
            }elseif(tt_is_product_tag()){
                $title = get_queried_object()->name . ' - ' . __('Product Tag', 'tt');
            }else{
                $title = __('Market', 'tt') . ' - ' . get_bloginfo('name');
            }
        }else{
            $title = trim(wp_title('', false)) . ' - ' . __('Market', 'tt');
        }
    }elseif(is_search()){
        $title = __('Search', 'tt') . get_search_query() . ' - ' . get_bloginfo('name');
    }elseif(is_year()){
        $title = get_the_time(__('Y','tt')) . __('posts archive', 'tt') . ' - ' . get_bloginfo('name');
    }elseif(is_month()){
        $title = get_the_time(__('Y.n','tt')) . __('posts archive', 'tt') . ' - ' . get_bloginfo('name');
    }elseif(is_day()){
        $title = get_the_time(__('Y.n.j','tt')) . __('posts archive', 'tt') . ' - ' . get_bloginfo('name');
    }elseif(is_tag()){
        $title = __('Tag: ', 'tt') . get_queried_object()->name . ' - ' . get_bloginfo('name');
    }elseif(is_404()){
        $title = __('Page Not Found', 'tt') . ' - ' . get_bloginfo('name');
    }

    // paged
    if($paged = get_query_var('paged') && get_query_var('paged') > 1){
        $title .= sprintf(__(' - Page %d ','tt'), get_query_var('paged'));
    }

    return $title;
}


/**
 * 获取关键词和描述
 *
 * @since 2.0.0
 * @return array
 */
function tt_get_keywords_and_description() {
    $keywords = '';
    $description = '';
    if($action = get_query_var('action')) {
        switch ($action) {
            case 'signin':
                $keywords = __('Sign In', 'tt');
                break;
            case 'signup':
                $keywords = __('Sign Up', 'tt');
                break;
            case 'activate':
                $keywords = __('Activate Registration', 'tt');
                break;
            case 'signout':
                $keywords = __('Sign Out', 'tt');
                break;
            case 'findpass':
                $keywords = __('Find Password', 'tt');
                break;
        }
        $description = __('由Marketing主题驱动', 'tt');
        return array(
            'keywords' => $keywords,
            'description' => $description
        );
    }
    if(is_home() || is_front_page()) {
        $keywords = tt_get_option('tt_home_keywords');
        $description = tt_get_option('tt_home_description');
    }elseif(is_single() && get_post_type() != 'product') {
        $tags = get_the_tags();
        $tag_names = array();
        if($tags){
            foreach ($tags as $tag){
                $tag_names[] = $tag->name;
            }
            $keywords = implode(',', $tag_names);
        }
        global $post;
        setup_postdata($post);
        $description = mb_substr(strip_tags(get_the_excerpt($post)), 0, 100);
    }elseif(is_page()){
        global $post;
        if($post->ID){
            $keywords = get_post_meta($post->ID, 'tt_keywords', true);
            $description = get_post_meta($post->ID, 'tt_description', true);
        }
    }elseif(is_category()) {
        $category = get_queried_object();
        $keywords = $category->name;
        $description = strip_tags($category->description);
    }elseif(is_author()){
        // TODO more tab titles
        $author = get_queried_object();
        $name = $author->data->display_name;
        $keywords = $name . ',' . __('Ucenter', 'tt') . __('Marketing主题用户中心和商店系统', 'tt');
        $description = sprintf(__('%s\'s Home Page', 'tt'), $name) . __('由Marketing主题驱动', 'tt');
    }elseif(get_post_type() == 'product'){
        if(is_archive()){
            if(tt_is_product_category()) {
                $category = get_queried_object();
                $keywords = $category->name;
                $description = strip_tags($category->description);
            }elseif(tt_is_product_tag()){
                $tag = get_queried_object();
                $keywords = $tag->name;
                $description = strip_tags($tag->description);
            }else{
                $keywords = tt_get_option('tt_shop_keywords', __('Market', 'tt')) . ',' . __('商店,Marketing主题用户中心和商店系统', 'tt');
                $banner_title = tt_get_option('tt_shop_title', '商店');
                $banner_subtitle = tt_get_option('tt_shop_sub_title', 'Themes - Plugins - Services');
                $description = $banner_title . ', ' . $banner_subtitle . ', ' . __('由Marketing主题驱动, 一个功能强大的内嵌用户中心和商店系统的WordPress主题)', 'tt');
            }
        }else{
            global $post;
            $tags = array();
            if($post->ID){
                $tags = wp_get_post_terms($post->ID, 'product_tag');
            }
            $tag_names = array();
            foreach ($tags as $tag){
                $tag_names[] = $tag->name;
            }
            $keywords = implode(',', $tag_names);
            $description = strip_tags(get_the_excerpt());
        }
    }elseif(is_search()){
        //TODO
    }elseif(is_year()){
        //TODO
    }elseif(is_month()){
        //TODO
    }elseif(is_day()){
        //TODO
    }elseif(is_tag()){
        $tag = get_queried_object();
        $keywords = $tag->name;
        $description = strip_tags($tag->description);
    }elseif(is_404()){
        //TODO
    }

    return array(
        'keywords' => $keywords,
        'description' => $description
    );
}

// load_func('func.Sidebar');
/**
 * 动态边栏
 *
 * @since   2.0.0
 * @return  string
 */
function tt_dynamic_sidebar(){
    // 默认通用边栏
    $sidebar = 'sidebar_common';

    // 根据页面选择边栏
    if ( is_home() && $option = tt_get_option('tt_home_sidebar') ) $sidebar = $option;
    if ( is_single() && $option = tt_get_option('tt_single_sidebar') ) $sidebar = $option;
    if ( is_archive() && $option = tt_get_option('tt_archive_sidebar') ) $sidebar = $option;
    if ( is_category() && $option = tt_get_option('tt_category_sidebar') ) $sidebar = $option;
    if ( is_search() && $option = tt_get_option('tt_search_sidebar') ) $sidebar = $option;
    if ( is_404() && $option = tt_get_option('tt_404_sidebar') ) $sidebar = $option;
    if ( is_page() && $option = tt_get_option('tt_page_sidebar') ) $sidebar = $option;
    if (get_query_var('site_util') == 'download' && $option = tt_get_option('tt_download_sidebar')) $sidebar = $option;

    // 检查一个页面或文章是否有特指边栏
    if ( is_singular() ) {
        wp_reset_postdata();
        global $post;
        $meta = get_post_meta($post->ID,'tt_sidebar',true);  //TODO: add post meta box for `tt_sidebar`
        if ( $meta ) {
            $sidebar = $meta;
        }
    }

    return $sidebar;
}


/**
 * 根据用户设置注册边栏
 *
 * @since   2.0.0
 * @return  void
 */
function tt_register_sidebars(){
    $sidebars = (array)tt_get_option('tt_register_sidebars', array('sidebar_common'=>true));
    $titles = array(
        'sidebar_common'    =>    __('Common Sidebar', 'tt'),
        'sidebar_home'      =>    __('Home Sidebar', 'tt'),
        'sidebar_single'    =>    __('Single Sidebar', 'tt'),
        //'sidebar_archive'   =>    __('Archive Sidebar', 'tt'),
        //'sidebar_category'  =>    __('Category Sidebar', 'tt'),
        'sidebar_search'    =>    __('Search Sidebar', 'tt'),
        //'sidebar_404'       =>    __('404 Sidebar', 'tt'),
        'sidebar_page'      =>    __('Page Sidebar', 'tt'),
        'sidebar_download'  =>    __('Download Page Sidebar', 'tt')
    );
    foreach ($sidebars as $key => $value){
        if(!$value) continue;
        $title = array_key_exists($key, $titles) ? $titles[$key] : $value;
        register_sidebar(
            array(
                'name' => $title,
                'id' => $key,
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="widget-title"><span>',
                'after_title' => '</span></h3>'
            )
        );
    }

    // 注册浮动小工具容器边栏
    // register_sidebar(
    //     array(
    //         'name' => __('Float Widgets Container', 'tt'),
    //         'id' => 'sidebar_float',
    //         'description' => __("A container for placing some widgets, it will be float once exceed the vision", 'tt'),
    //         'before_widget' => '<div id="%1$s" class="widget %2$s">',
    //         'after_widget' => '</div>',
    //         'before_title' => '<h3 class=widget-title><span>',
    //         'after_title' => '</span></h3>'
    //     )
    // );
}
add_action('widgets_init', 'tt_register_sidebars');

// load_func('func.Template');
/**
 * 重新定义文章、页面(非自定义模板页面)、分类、作者、归档、404等模板位置
 * https://developer.wordpress.org/themes/basics/template-hierarchy/
 * https://developer.wordpress.org/files/2014/10/template-hierarchy.png 了解WordPress模板系统
 */

/**
 * 自定义Index模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_index_template($template){
    //TODO: if(tt_get_option('layout')=='xxx') -> index-xxx.php
    unset($template);
    return THEME_TPL . '/tpl.Index.php';
}
add_filter('index_template', 'tt_get_index_template', 10, 1);


/**
 * 自定义Home文章列表模板，优先级高于Index
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_home_template($template){
    unset($template);
    return THEME_TPL . '/tpl.Home.php';
}
add_filter('home_template', 'tt_get_home_template', 10, 1);


/**
 * 自定义首页静态页面模板，基于后台选项首页展示方式，与Index同级
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_front_page_template($template){
    unset($template);
    return locate_template(array('core/templates/tpl.FrontPage.php', 'core/templates/tpl.Home.php', 'core/templates/tpl.Index.php'));
}
add_filter('front_page_template', 'tt_get_front_page_template', 10, 1);


/**
 * 自定义404模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_404_template($template){
    unset($template);
    return THEME_TPL . '/tpl.404.php';
}
add_filter('404_template', 'tt_get_404_template', 10, 1);


/**
 * 自定义归档模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_archive_template($template){
    unset($template);
    return THEME_TPL . '/tax/tpl.Archive.php';
}
add_filter('archive_template', 'tt_get_archive_template', 10, 1);


/**
 * 自定义作者模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  array               自定义模板路径数组
 */
function tt_get_author_template($template){
    unset($template);

    // 为不同角色用户定义不同模板
    // https://developer.wordpress.org/themes/basics/template-hierarchy/#example
    $author = get_queried_object();
    $role = count($author->roles) ? $author->roles[0] : 'subscriber';

    // 判断是否用户中心页(因为用户中心页和默认的作者页采用了相同的wp_query_object)
    if(get_query_var('uc') && intval(get_query_var('uc'))===1){
        $template = apply_filters('user_template', $author);
        if($template === 'header-404') return '';
        if($template) return $template;
    }

    $template = 'core/templates/tpl.Author.php'; // TODO: 是否废弃 tpl.Author类似模板，Author已合并至UC
    return locate_template( array( 'core/templates/tpl.Author.' . ucfirst($role) . '.php', $template ) );
}
add_filter('author_template', 'tt_get_author_template', 10, 1);


/**
 * 获取用户页模板
 * // 主题将用户与作者相区分，作者页沿用默认的WP设计，展示作者的文章列表，用户页重新设计为用户的各种信息以及前台用户中心 //TODO 现在合并了, 废弃WP原有作者文章列表页(基础版无UC时才有)
 *
 * @since   2.0.0
 *
 * @param   object  $user   WP_User对象
 * @return  string
 */
function tt_get_user_template($user) {
    $templates = array();

    if ( $user instanceof WP_User ) {
        if($uc_tab = get_query_var('uctab')){
            // 由于profile tab是默认tab，直接使用/@nickname主路由，对于/@nickname/profile的链接会重定向处理，因此不放至允许的tabs中
            $allow_tabs = (array)json_decode(ALLOWED_UC_TABS);
            if(!in_array($uc_tab, $allow_tabs)) return 'header-404';
            $templates[] = 'core/templates/uc/tpl.UC.' . ucfirst(strtolower($uc_tab)) . '.php';
        }else{
            //$role = $user->roles[0];
            $templates[] = 'core/templates/uc/tpl.UC.Profile.php';
            // Maybe dropped
            // TODO: maybe add membership templates
        }
    }
    $templates[] = 'core/templates/uc/tpl.UC.php';
    return locate_template($templates);
}
add_filter('user_template', 'tt_get_user_template', 10, 1);


/**
 * 自定义分类模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_category_template($template){
    unset($template);
    // TODO: add category slug support
    return locate_template(array('core/templates/tax/tpl.Category.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('category_template', 'tt_get_category_template', 10, 1);


/**
 * 自定义标签模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径数组
 */
function tt_get_tag_template($template){
    unset($template);
    return locate_template(array('core/templates/tax/tpl.Tag.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('tag_template', 'tt_get_tag_template', 10, 1);


/**
 * 自定义Taxonomy模板，Category/Tag均属于Taxonomy，可做备选模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_taxonomy_template($template){
    unset($template);
    return locate_template(array('core/templates/tax/tpl.Taxonomy.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('taxonomy_template', 'tt_get_taxonomy_template', 10, 1);


/**
 * 自定义时间归档模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_date_template($template){
    unset($template);
    return locate_template(array('core/templates/tax/tpl.Date.php', 'core/templates/tax/tpl.Archive.php'));
}
add_filter('date_template', 'tt_get_date_template', 10, 1);


/**
 * 自定义默认页面模板(区别于自定义页面模板)
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_page_template($template){
    if(!empty($template)) return $template;
    unset($template);
    return locate_template(array('core/templates/page/tpl.Page.php'));
}
add_filter('page_template', 'tt_get_page_template', 10, 1);


/**
 * 自定义搜素结果页模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_search_template($template){
    unset($template);
    if(isset($_GET['in_shop']) && $_GET['in_shop'] == 1) {
        return locate_template(array('core/templates/shop/tpl.Product.Search.php'));
    }
    return locate_template(array('core/templates/tpl.Search.php'));
}
add_filter('search_template', 'tt_get_search_template', 10, 1);


/**
 * 自定义文章页模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_single_template($template){
    unset($template);
    $single = get_queried_object();
    return locate_template(array('core/templates/single/tpl.Single.' . $single->slug . '.php', 'core/templates/single/tpl.Single.' . $single->ID . '.php', 'core/templates/single/tpl.Single.php'));
}
add_filter('single_template', 'tt_get_single_template', 10, 1);


/**
 * 自定义附件页模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_attachment_template($template){
    unset($template);
    return locate_template(array('core/templates/attachments/tpl.Attachment.php'));
}
add_filter('attachment_template', 'tt_get_attachment_template', 10, 1);


/**
 * 自定义[Plain] Text附件模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  array               自定义模板路径数组
 */
function tt_get_text_template($template){
    //TODO: other MIME types, e.g `video`
    unset($template);
    return locate_template(array('core/templates/attachments/tpl.MIMEText.php', 'core/templates/attachments/tpl.Attachment.php'));
}
add_filter('text_template', 'tt_get_text_template', 10, 1);
add_filter('plain_template', 'tt_get_text_template', 10, 1);
add_filter('text_plain_template', 'tt_get_text_template', 10, 1);


/**
 * 自定义弹出评论模板
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_comments_popup_template($template){
    unset($template);
    return THEME_TPL . '/tpl.CommentPopup.php';
}
add_filter('comments_popup', 'tt_get_comments_popup_template', 10, 1);


/**
 * 自定义嵌入式文章模板
 * WordPress 4.4新功能
 * https://make.wordpress.org/core/2015/10/28/new-embeds-feature-in-wordpress-4-4/
 *
 * @since   2.0.0
 *
 * @param   string  $template   默认模板路径
 * @return  string              自定义模板路径
 */
function tt_get_embed_template($template){
    unset($template);
    return THEME_TPL . '/tpl.Embed.php';
}
add_filter('embed_template', 'tt_get_embed_template', 10, 1);


/**
 * CMS首页各分类使用的模板
 *
 * @param $cat_id
 * @return string
 */
function tt_get_cms_cat_template ($cat_id) {
    $default = 'Style_0';
    $key = sprintf('tt_cms_home_cat_style_%d', $cat_id);
    $option = tt_get_option($key, $default);
    if (in_array($option, array('Style_0', 'Style_1', 'Style_2', 'Style_3', 'Style_4', 'Style_5', 'Style_6'))) {
        return $option;
    }
    return $default;
}

// load_func('func.Thumb');
/**
 * 获取文章缩略图
 *
 * @since   2.0.0
 *
 * @param   int | object    文章id或WP_Post对象
 * @param   string | array  $size   图片尺寸
 * @return  string
 */
function tt_get_thumb($post = null, $size = 'thumbnail'){
    if (is_numeric($post) && $post <= 0) {
        $specifiedImage = '';
        if ($post == -4) {
            // 充值积分
            $specifiedImage = THEME_URI . '/assets/img/credit-thumb.png';
        } else if ($post == -1 || $post == -2 ) {
            // 月度/季度会员
            $specifiedImage = THEME_URI . '/assets/img/membership-thumb.png';
        } else if ($post == -3) {
            // 年付会员
            $specifiedImage = THEME_URI . '/assets/img/annual-membership-thumb.png';
        }
        return PostImage::getOptimizedImageUrl($specifiedImage, $size);
    }

    if(!$post){
        global $post;
    }
    $post = get_post($post);

    // 优先利用缓存
    $callback = function () use ($post, $size) {
        $instance = new PostImage($post, $size);
        return $instance->getThumb();
    };
    $instance = new PostImage($post, $size);
    return tt_cached($instance->cache_key, $callback, 'thumb', 60*60*24*7);
}

// load_func('func.User');
/**
 * 获取用户权限描述字符
 *
 * @since 2.0.0
 * @param $user_id
 * @return string
 */
function tt_get_user_cap_string ($user_id) {
    if(user_can($user_id,'install_plugins')) {
        return __('Site Manager', 'tt');
    }
    if(user_can($user_id,'edit_others_posts')) {
        return __('Editor', 'tt');
    }
    if(user_can($user_id,'publish_posts')) {
        return __('Author', 'tt');
    }
    if(user_can($user_id,'edit_posts')) {
        return __('Contributor', 'tt');
    }
    return __('Reader', 'tt');
}


/**
 * 获取用户的封面
 *
 * @since 2.0.0
 * @param $user_id
 * @param $size
 * @param $default
 * @return string
 */
function tt_get_user_cover ($user_id, $size = 'full', $default = '') {
    if(!in_array($size, array('full', 'mini'))) {
        $size = 'full';
    }
    if($cover = get_user_meta($user_id, 'tt_user_cover', true)) {
        return $cover; // TODO size
    }
    return $default ? $default : THEME_ASSET . '/img/user-default-cover-' . $size . '.jpg';
}


/**
 * 获取用户正在关注的人数
 *
 * @since 2.0.0
 * @param $user_id
 * @return int
 */
function tt_count_user_following ($user_id) {
    return tt_count_following($user_id);
}

/**
 * 获取用户的粉丝数量
 *
 * @since 2.0.0
 * @param $user_id
 * @return int
 */
function tt_count_user_followers ($user_id) {
    return tt_count_followers($user_id);
}


/**
 * 获取作者的文章被浏览总数
 *
 * @since 2.0.0
 * @param $user_id
 * @param $view_key
 * @return int
 */
function tt_count_author_posts_views ($user_id, $view_key = 'views') {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT SUM(meta_value) FROM $wpdb->postmeta RIGHT JOIN $wpdb->posts ON $wpdb->postmeta.meta_key='%s' AND $wpdb->posts.post_author=%d AND $wpdb->postmeta.post_id=$wpdb->posts.ID", $view_key, $user_id);
    $count = $wpdb->get_var($sql);

    return $count;
}


/**
 * 统计某个作者的文章被赞的总次数
 *
 * @since 2.0.0
 * @param $user_id
 * @return null|string
 */
function tt_count_author_posts_stars ($user_id) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta  WHERE meta_key='%s' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_author=%d)", 'tt_post_star_users', $user_id);
    $count = $wpdb->get_var($sql);

    return $count;
}


/**
 * 获取用户点赞的所有文章ID
 *
 * @since 2.0.0
 * @param $user_id
 * @return array
 */
function tt_get_user_star_post_ids ($user_id) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT `post_id` FROM $wpdb->postmeta  WHERE `meta_key`='%s' AND `meta_value`=%d", 'tt_post_star_users', $user_id);
    $results = $wpdb->get_results($sql);
    //ARRAY_A -> array(3) { [0]=> array(1) { [0]=> string(4) "1420" } [1]=> array(1) { [0]=> string(3) "242" } [2]=> array(1) { [0]=> string(4) "1545" } }
    //OBJECT -> array(3) { [0]=> object(stdClass)#3862 (1) { ["post_id"]=> string(4) "1420" } [1]=> object(stdClass)#3863 (1) { ["post_id"]=> string(3) "242" } [2]=> object(stdClass)#3864 (1) { ["post_id"]=> string(4) "1545" } }
    $ids = array();
    foreach ($results as $result) {
        $ids[] = intval($result->post_id);
    }
    $ids = array_unique($ids);
    rsort($ids); //从大到小排序
    return $ids;
}


/**
 * 统计用户点赞(收藏)的文章数
 *
 * @since 2.0.0
 * @param $user_id
 * @return int
 */
function tt_count_user_star_posts($user_id) {
    return count(tt_get_user_star_post_ids($user_id));
}


/**
 * 获取一定数量特定角色用户
 *
 * @since 2.0.0
 * @param $role
 * @param $offset
 * @param $limit
 * @return array
 */
function tt_get_users_with_role ($role, $offset = 0, $limit = 20) {
    // TODO $role 过滤
    $user_query = new WP_User_Query(
        array(
            'role' => $role,
            'orderby' => 'ID',
            'order' => 'ASC',
            'number' => $limit,
            'offset' => $offset
        )
    );
    $users = $user_query->get_results();
    if (!empty($users)) {
        return $users;
    }
    return array();
}


/**
 * 获取管理员用户的ID
 *
 * @since 2.0.0
 * @return array
 */
function tt_get_administrator_ids () {
    $ids = array();
    $administrators = tt_get_users_with_role('Administrator');
    foreach ($administrators as $administrator) {
        $ids[] = $administrator->ID;
    }
    return $ids;
}


/**
 * 获取用户私信对话地址
 *
 * @since 2.0.0
 * @param $user_id
 * @return string
 */
function tt_get_user_chat_url($user_id) {
    return get_author_posts_url($user_id) . '/chat';
}


/**
 * 将用户的资料编辑页面链接改至前台
 *
 * @since 2.0.0
 * @param $url
 * @return mixed
 */
function tt_custom_profile_edit_link( $url ) {
    return is_admin() ? $url : tt_url_for('my_settings');
}
add_filter( 'edit_profile_url', 'tt_custom_profile_edit_link' );


/**
 * 将普通用户的文章编辑链接改至前台
 *
 * @since 2.0.0
 * @param $url
 * @param $post_id
 * @return string
 */
function tt_frontend_edit_post_link($url, $post_id){
    if( !current_user_can('publish_posts') ){
        $url = tt_url_for('edit_post', $post_id);
    }
    return $url;
}
add_filter('get_edit_post_link', 'tt_frontend_edit_post_link', 10, 2);


/**
 * 拒绝普通用户访问后台
 *
 * @since 2.0.0
 * @return void
 */
function tt_redirect_wp_admin(){
    if( is_admin() && is_user_logged_in() && !current_user_can('publish_posts') && ( !defined('DOING_AJAX') || !DOING_AJAX )  ){
        wp_redirect( tt_url_for('my_settings') );
        exit;
    }
}
add_action( 'init', 'tt_redirect_wp_admin' );


/**
 * 记录用户登录时间、IP等信息
 *
 * @since 2.0.0
 * @param $login
 * @param $user
 */
function tt_update_user_latest_login( $login, $user ) {
    if(!$user) $user = get_user_by( 'login', $login );
    $latest_login = get_user_meta( $user->ID, 'tt_latest_login', true );
    $latest_login_ip = get_user_meta( $user->ID, 'tt_latest_login_ip', true );
    update_user_meta( $user->ID, 'tt_latest_login_before', $latest_login );
    update_user_meta( $user->ID, 'tt_latest_login', current_time( 'mysql' ) );
    update_user_meta( $user->ID, 'tt_latest_ip_before', $latest_login_ip );
    update_user_meta( $user->ID, 'tt_latest_login_ip', $_SERVER['REMOTE_ADDR'] );
}
add_action( 'wp_login', 'tt_update_user_latest_login', 10, 2 );


/**
 * 获取用户的真实IP
 *
 * @since 2.0.0
 * @return void
 */
function tt_get_true_ip(){
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realIP = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
            $realIP = $realIP[0];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realIP = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realIP = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realIP = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realIP = getenv("HTTP_CLIENT_IP");
        } else {
            $realIP = getenv("REMOTE_ADDR");
        }
    }
    $_SERVER['REMOTE_ADDR'] = $realIP;
}
add_action( 'init', 'tt_get_true_ip' );


/**
 * 对封禁账户处理
 *
 * @since   2.0.0
 * @return  void
 */
function tt_handle_banned_user(){
    if($user_id = get_current_user_id()) {
        if (current_user_can('administrator')) {
            return;
        }
        $ban_status = get_user_meta($user_id, 'tt_banned', true);
        if($ban_status) {
            wp_die(sprintf(__('Your account is banned for reason: %s', 'tt'), get_user_meta($user_id, 'tt_banned_reason', true)), __('Account Banned', 'tt'), 404); //TODO add banned time
        }
    }
}
add_action('template_redirect', 'tt_handle_banned_user');
add_action('admin_menu', 'tt_handle_banned_user');


/**
 * 获取用户账户状态
 *
 * @since 2.0.0
 * @param $user_id
 * @param $return
 * @return array|bool
 */
function tt_get_account_status($user_id, $return = 'bool') {
    $ban = get_user_meta($user_id, 'tt_banned', true);
    if($ban) {
        if($return == 'bool') {
            return true;
        }
        $reason = get_user_meta($user_id, 'tt_banned_reason', true);
        $time = get_user_meta($user_id, 'tt_banned_time', true);
        return array(
            'banned' => true,
            'banned_reason' => strval($reason),
            'banned_time' => strval($time)
        );
    }
    return $return == 'bool' ? false : array(
        'banned' => false
    );
}


/**
 * 封禁用户
 *
 * @since 2.0.0
 * @param $user_id
 * @param string $reason
 * @param string $return
 * @return array|bool
 */
function tt_ban_user($user_id, $reason = '', $return = 'bool') {
    $user = get_user_by('ID', $user_id);
    if(!$user) {
        return $return == 'bool' ? false : array(
            'success' => false,
            'message' => __('The specified user is not existed', 'tt')
        );
    }
    if(update_user_meta($user_id, 'tt_banned', 1)) {
        update_user_meta($user_id, 'tt_banned_reason', $reason);
        update_user_meta($user_id, 'tt_banned_time', current_time('mysql'));
        // 清理Profile缓存
        // tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM');
        tt_clear_cache_by_key('tt_cache_daily_vm_UCProfileVM_author' . $user_id);

        return $return == 'bool' ? true : array(
            'success' => true,
            'message' => __('The specified user is banned', 'tt')
        );
    }
    return $return == 'bool' ? false : array(
        'success' => false,
        'message' => __('Error occurs when banning the user', 'tt')
    );
}


/**
 * 解禁用户
 *
 * @since 2.0.0
 * @param $user_id
 * @param string $return
 * @return array|bool
 */
function tt_unban_user($user_id, $return = 'bool') {
    $user = get_user_by('ID', $user_id);
    if(!$user) {
        return $return == 'bool' ? false : array(
            'success' => false,
            'message' => __('The specified user is not existed', 'tt')
        );
    }
    if(update_user_meta($user_id, 'tt_banned', 0)) {
        //update_user_meta($user_id, 'tt_banned_reason', '');
        //update_user_meta($user_id, 'tt_banned_time', '');
        // 清理Profile缓存
        // tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM');
        tt_clear_cache_by_key('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
        return $return == 'bool' ? true : array(
            'success' => true,
            'message' => __('The specified user is unlocked', 'tt')
        );
    }
    return $return == 'bool' ? false : array(
        'success' => false,
        'message' => __('Error occurs when unlock the user', 'tt')
    );
}


/**
 * 输出UC小工具中登录后的内容
 *
 * @since 2.0.0
 * @return void
 */
function tt_uc_widget_content() {
    $user = wp_get_current_user();
    ?>
    <li class="login-info"><img class="avatar" src="<?php echo tt_get_avatar($user->ID); ?>"><span><?php printf(__('Log User <a href="%1$s">%2$s</a>', 'tt'), tt_url_for('my_settings'), $user->display_name); ?></span><span><?php printf(__('<a href="%1$s" title="Log Out">Log Out &raquo;</a>', 'tt'), tt_signout_url()); ?></span></li>
    <?php if(!filter_var($user->user_email, FILTER_VALIDATE_EMAIL)) { ?>
        <li><?php printf(__('<a href="%1$s#securityInfo">Please add correct email for safety of your account.</a>', 'tt'), tt_url_for('my_settings')); ?></li>
    <?php } ?>
    <?php
    $links = array();
    $links[] = array(
        'title' => __('My HomePage', 'tt'),
        'url' => get_author_posts_url($user->ID)
    );
    if( current_user_can( 'manage_options' ) ) {
        $links[] = array(
            'title' => __('Manage Dashboard', 'tt'),
            'url' => admin_url()
        );
    }
    $links[] = array(
        'title' => __('Add New Post', 'tt'),
        'url' => tt_url_for('new_post')
    );
    ?>
    <li class="active">
        <?php foreach($links as $link) { ?>
            <a href="<?php echo $link['url']; ?>"><?php echo $link['url'] . ' &raquo;'; ?></a>
        <?php } ?>
    </li>
    <?php
    $credit = tt_get_user_credit($user->ID);
    $credit_void = tt_get_user_consumed_credit($user->ID);
    $unread_count = tt_count_messages('chat', 0);
    $stared_count = tt_count_user_star_posts($user->ID);

    $statistic_info = array(
        array(
            'title' => __('Posts', 'tt'),
            'url' => tt_url_for('uc_latest', $user->ID),
            'count' => count_user_posts($user->ID)
        ),
        array(
            'title' => __('Comments', 'tt'),
            'url' => tt_url_for('uc_comments', $user->ID),
            'count' => get_comments( array('status' => '1', 'user_id'=>$user->ID, 'count' => true) )
        ),
        array(
            'title' => __('Stars', 'tt'),
            'url' => tt_url_for('uc_stars', $user->ID),
            'count' => $stared_count
        ),
    );
    if($unread_count) {
        $statistic_info[] = array(
            'title' => __('Unread Messages', 'tt'),
            'url' => tt_url_for('in_msg'),
            'count' => $unread_count
        );
    }
    $statistic_info[] = array(
        'title' => __('Credits', 'tt'),
        'url' => tt_url_for('my_credits'),
        'count' => $credit
    );
    ?>
    <li>
        <?php
        foreach ($statistic_info as $info_item) { ?>
            <span><?php printf('%1$s<a href="%2$s">%3$s</a>', $info_item['title'], $info_item['url'], $info_item['count']); ?></span>
        <?php } ?>
        <?php echo tt_daily_sign_anchor($user->ID); ?>
    </li>
    <li>
        <div class="input-group">
            <span class="input-group-addon"><?php _e('Ref url for this page', 'tt'); ?></span>
            <input class="tin_aff_url form-control" type="text" class="form-control" value="<?php echo add_query_arg('ref', $current_user->ID, Utils::getPHPCurrentUrl()); ?>">
        </div>
    </li>
    <?php
}


/**
 * 站内信欢迎新注册用户并通知完善账号信息
 *
 * @since 2.0.4
 * @param $user_id
 * @return void
 */
function tt_welcome_for_new_registering($user_id){
    $blog_name = get_bloginfo('name');
    //tt_create_message($user_id, 0, 'System', 'notification', sprintf( __('欢迎来到%1$s, 请首先在个人设置中完善您的账号信息, 如邮件地址是必需的', 'tt'), $blog_name ), '', 0, 'publish');
    tt_create_pm($user_id, $blog_name, sprintf( __('欢迎来到%1$s, 请首先在个人设置中完善您的账号信息, 如邮件地址是必需的', 'tt'), $blog_name), true);
}
add_action('user_register', 'tt_welcome_for_new_registering');

// load_func('func.Content');
/**
 * 处理文章内容图片链接以支持lightbox
 *
 * @since 2.0.0
 * @param string $content
 * @return string
 */
function tt_filter_content_for_lightbox ($content){
    $pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 class="lightbox-gallery" data-lightbox="postContentImages" $6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('the_content', 'tt_filter_content_for_lightbox', 98);


/**
 * 替换摘要more字样
 * @param $more
 * @return mixed
 */
function tt_excerpt_more($more) {
    $read_more=tt_get_option('tt_read_more', ' ···');
    return $read_more;
}
add_filter('excerpt_more', 'tt_excerpt_more');

// load_func('func.Follow');
/* 关注和粉丝 */


/**
 * 获取正在关注的用户列表
 *
 * @since 2.0.0
 * @param $uid
 * @param $limit
 * @param $offset
 * @return array|int|null|object
 */
function tt_get_following($uid, $limit = 20, $offset = 0) {
    $uid = absint($uid);
    $limit = absint($limit);
    if(!$uid) return false;
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `follow_user_id`=%d AND `follow_status` IN(1,2) ORDER BY `follow_time` DESC LIMIT %d OFFSET %d", $uid, $limit, $offset ));
    return $results;
}


/**
 * 获取正在关注的用户数量
 *
 * @since 2.0.0
 * @param $uid
 * @return int
 */
function tt_count_following($uid) {
    $uid = absint($uid);
    if(!$uid) return false;
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE follow_user_id=%d AND follow_status IN(1,2)", $uid));
    return $count;
}


/**
 * 获取粉丝数据
 *
 * @since 2.0.0
 * @param $uid
 * @param int $limit
 * @param int $offset
 * @return array|bool|null|object
 */
function tt_get_followers($uid, $limit = 20, $offset = 0) {
    $uid = absint($uid);
    $limit = absint($limit);
    if(!$uid) return false;
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `user_id`=%d AND `follow_status` IN(1,2) ORDER BY `follow_time` DESC LIMIT %d OFFSET %d", $uid, $limit, $offset ));
    return $results;
}

/**
 * 获取粉丝数量
 *
 * @since 2.0.0
 * @param $uid
 * @return int
 */
function tt_count_followers($uid) {
    $uid = absint($uid);
    if(!$uid) return false;
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_status` IN(1,2)", $uid));
    return $count;
}


/**
 * 关注或取消关注
 *
 * @since 2.0.0
 * @param $followed_id
 * @param string $action
 * @param int $follower_id
 * @return object|bool|WP_Error|array
 */
function tt_follow_unfollow($followed_id, $action = 'follow', $follower_id = 0){
    date_default_timezone_set ('Asia/Shanghai');
    $followed = get_user_by('ID', absint($followed_id));
    if(!$followed) {
        return new WP_Error( 'user_not_found', __( 'The user you are following not exist', 'tt' ) );
    }
    if(!$follower_id) $follower_id = get_current_user_id();
    if(!$follower_id) {
        return new WP_Error( 'user_not_logged_in', __( 'You must sign in to follow someone', 'tt' ) );
    }
    if($followed_id == $follower_id){
        return new WP_Error( 'invalid_follow', __( 'You cannot follow yourself', 'tt' ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    if($action == 'unfollow'){
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d", $followed_id, $follower_id));
        $status = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $follower_id, $followed_id));
        $status1 = 0;
        $status2 = $status ? 1 : 0;
        if($check){
            if($wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND follow_user_id=%d", $status1, $followed_id, $follower_id ))){
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET follow_status=%d WHERE user_id=%d AND follow_user_id=%d", $status2, $follower_id, $followed_id ));
                return array(
                    'success' => true,
                    'message' => __('Unfollow user successfully', 'tt')
                );
            }else{
                return array(
                    'success' => false,
                    'message' => __('Unfollow user failed', 'tt')
                );
            }
        }else{
            return array(
                'success' => false,
                'message' => __('Unfollow user failed, you do not have followed him', 'tt')
            );
        }
    }else{
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d", $followed_id, $follower_id));
        $status = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $follower_id, $followed_id));
        $status1 = $status ? 2 : 1;
        $status2 = $status ? 2 : 0;
        $time = current_time('mysql');
        if($check){
            if($wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d, `follow_time`='%s' WHERE `user_id`=%d AND `follow_user_id`=%d", $status1, $time, $followed_id, $follower_id))){
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND `follow_user_id`=%d", $status2, $follower_id, $followed_id));
                return array(
                    'success' => true,
                    'message' => __('Follow user successfully', 'tt'),
                    'followEach' => !!$status
                );
            }else{
                return array(
                    'success' => false,
                    'message' => __('Follow user failed', 'tt')
                );
            }
        }else{
            if($wpdb->query( $wpdb->prepare("INSERT INTO $table_name (user_id, follow_user_id, follow_status, follow_time) VALUES (%d, %d, %d, %s)", $followed_id, $follower_id, $status1, $time))){
                $wpdb->query($wpdb->prepare("UPDATE $table_name SET `follow_status`=%d WHERE `user_id`=%d AND `follow_user_id`=%d", $status2, $follower_id, $followed_id));
                return array(
                    'success' => true,
                    'message' => __('Follow user successfully', 'tt'),
                    'followEach' => !!$status
                );
            }else{
                return array(
                    'success' => false,
                    'message' => __('Follow user failed', 'tt')
                );
            }
        }
    }
}

/**
 * 关注用户
 *
 * @since 2.0.0
 * @param $uid
 * @return WP_Error|array
 */
function tt_follow($uid){
    return tt_follow_unfollow($uid);
}

/**
 * 取消关注
 *
 * @since 2.0.0
 * @param $uid
 * @return WP_Error|array
 */
function tt_unfollow($uid){
    return tt_follow_unfollow($uid, 'unfollow');
}


/**
 * 根据关注状态输出对应的关注按钮
 *
 * @param $uid
 * @return string
 */
function tt_follow_button($uid){
    $uid = absint($uid);
    if(!$uid) return '';

    $current_uid = get_current_user_id();
    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_follow';
    $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE `user_id`=%d AND `follow_user_id`=%d AND `follow_status` IN(1,2)", $uid, $current_uid));
    if($check){
        if($check->follow_status==2){ // 互相关注
            $button = '<a class="follow-btn followed" href="javascript: void 0" title="' . __('Unfollow', 'tt') . '" data-uid="' . $uid . '" data-act="unfollow"><i class="tico tico-exchange"></i><span>' . __('FOLLOWED EACH', 'tt') . '</span></a>';
        }else{
            $button = '<a class="follow-btn followed" href="javascript: void 0" title="' . __('Unfollow', 'tt') . '" data-uid="' . $uid . '" data-act="unfollow"><i class="tico tico-user-check"></i><span>' . __('FOLLOWED', 'tt') . '</span></a>';
        }
    }else{
        $button = '<a class="follow-btn unfollowed" href="javascript: void 0" title="' . __('Follow the user', 'tt') . '" data-uid="' . $uid . '" data-act="follow"><i class="tico tico-user-plus"></i><span>' . __('FOLLOW', 'tt') . '</span></a>';
    }
    return $button;
}

///* Following and follower avatar html output */
//function um_follow_list($uid,$limits,$type='follower'){
//    if($type=='following'){$results = um_following($uid,$limits);$field='user_id';} else {$results = um_follower($uid,$limits);$field='follow_user_id';}
//    $html = '';
//    if($results)
//        foreach($results as $result){
//            $user = get_userdata($result->$field);
//            $username = $user->display_name;
//            $html .= '<li class="flow" title="'.$username.'"><a href="'.um_get_user_url('post',$user->ID).'" target="_blank">'.um_get_avatar( $result->$field , '40' , um_get_avatar_type($result->$field) ).'</a><span class="name">'.$username.'</span></li>';
//        }
//    return $html;
//}

// load_func('func.Message');
/**
 * 创建消息
 *
 *
 * @since 2.0.0
 * @param int $user_id  接收用户ID
 * @param int $sender_id  发送者ID(可空)
 * @param string $sender   发送者
 * @param string $type  消息类型(notification/chat/credit)
 * @param string $title 消息标题
 * @param string $content 消息内容
 * @param int $read (已读/未读)
 * @param string $status  消息状态(publish/trash)
 * @param string $date  消息时间
 * @return bool
 */
function tt_create_message( $user_id=0, $sender_id=0, $sender, $type='', $title='', $content='', $read=MsgReadStatus::UNREAD, $status='publish', $date='' ){

    $user_id = absint($user_id);
    $sender_id = absint($sender_id);
    $title = sanitize_text_field($title);

    if( !$user_id || empty($title) ) return false;

    $type = $type ? sanitize_text_field($type) : 'chat';
    $date = $date ? $date : current_time('mysql');
    $content = htmlspecialchars($content);

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("INSERT INTO $table_name (user_id, sender_id, sender, msg_type, msg_title, msg_content, msg_read, msg_status, msg_date) VALUES (%d, %d, %s, %s, %s, %s, %d, %s, %s)", $user_id, $sender_id, $sender, $type, $title, $content, $read, $status, $date )) )
        return true;
    return false;
}


/**
 * 创建一条私信
 *
 * @param $receiver_id
 * @param $sender
 * @param $text
 * @param $send_mail
 * @return bool
 */
function tt_create_pm($receiver_id, $sender, $text, $send_mail = false) {
    // 清理未读消息统计数的缓存
    if(wp_using_ext_object_cache()) {
        $key = 'tt_user_' . $receiver_id . '_unread';
        wp_cache_delete($key);
    }

    if($sender instanceof WP_User && $sender->ID) {
        if($send_mail && $sender->user_email) {
            $subject = sprintf( __('%1$s向你发送了一条消息 - %2$s', 'tt'), $sender->display_name, get_bloginfo('name') );
            $args = array(
                'senderName' => $sender->display_name,
                'message' => $text,
                'chatLink' => tt_url_for('uc_chat', $sender)
            );
            //tt_async_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
            tt_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
        }
        return tt_create_message($receiver_id, $sender->ID, $sender->display_name, 'chat', $text);
    }elseif(is_int($sender)){
        $sender = get_user_by('ID', $sender);
        if($send_mail && $sender->user_email) {
            $subject = sprintf( __('%1$s向你发送了一条消息 - %2$s', 'tt'), $sender->display_name, get_bloginfo('name') );
            $args = array(
                'senderName' => $sender->display_name,
                'message' => $text,
                'chatLink' => tt_url_for('uc_chat', $sender)
            );
            //tt_async_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
            tt_mail('', get_user_by('id', $receiver_id)->user_email, $subject, $args, 'pm');
        }
        return tt_create_message($receiver_id, $sender->ID, $sender->display_name, 'chat', $text);
    }
    return false;
}


/**
 * 标记消息阅读状态
 *
 * @since 2.0.0
 * @param $id
 * @param int $read
 * @return bool
 */
function tt_mark_message( $id, $read = MsgReadStatus::READ ) {
    $id = absint($id);
    $user_id = get_current_user_id(); //确保只能标记自己的消息

    if( ( !$id || !$user_id) ) return false;

    $read = $read == MsgReadStatus::UNREAD ? : MsgReadStatus::READ;

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_read` = %d WHERE `msg_id` = %d AND `user_id` = %d", $read, $id, $user_id) )) {
        // 清理未读消息统计数的缓存
        if(wp_using_ext_object_cache()) {
            $key = 'tt_user_' . $user_id . '_unread';
            wp_cache_delete($key);
        }
        return true;
    }
    return false;
}


/**
 * 标记所有未读消息已读
 *
 * @since 2.0.0
 * @return bool
 */
function tt_mark_all_message_read( ) {
    $user_id = get_current_user_id();
    if(!$user_id) return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_read` = 1 WHERE `user_id` = %d AND `msg_read` = 0", $user_id) )) {
        // 清理未读消息统计数的缓存
        if(wp_using_ext_object_cache()) {
            $key = 'tt_user_' . $user_id . '_unread';
            wp_cache_delete($key);
        }
        return true;
    }
    return false;
}


/**
 * 获取单条消息
 *
 * @since 2.0.0
 * @param $msg_id
 * @return bool|object
 */
function tt_get_message($msg_id) {
    $user_id = get_current_user_id();
    if(!$user_id) return false; // 用于防止获取其他用户的消息

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    $row = $wpdb->get_row(sprintf("SELECT * FROM $table_name WHERE `msg_id`=%d AND `user_id`=%d OR `sender_id`=%d", $msg_id, $user_id, $user_id));
    if($row) return $row;
    return false;
}


/**
 * 查询消息
 *
 * @since 2.0.0
 * @param string $type (notification/chat/credit)
 * @param int $limit
 * @param int $offset
 * @param int $read
 * @param string $msg_status
 * @param int $sender_id
 * @param bool  $count
 * @return array|bool|null|object|int
 */
function tt_get_messages( $type = 'chat', $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $sender_id = 0, $count = false ) {
    $user_id = get_current_user_id();

    if(!$user_id) return false;

    if(is_array($type)) {
        $type = implode("','", $type); //NOTE  IN('comment','star','update','notification') IN表达式的引号
    }
    if(!in_array($read, array(MsgReadStatus::READ, MsgReadStatus::UNREAD, MsgReadStatus::ALL))) {
        $read = MsgReadStatus::UNREAD;
    }
    if(!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    $sql = sprintf("SELECT %s FROM $table_name WHERE `user_id`=%d%s AND `msg_type` IN('$type')%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? "COUNT(*)" : "*", $user_id, $sender_id ? " AND `sender_id`=$sender_id" : "", $read!=MsgReadStatus::ALL ? " AND `msg_read`=$read" : "", $msg_status!='all' ? " AND `msg_status`='$msg_status'" : "", $count ? "" : " LIMIT $offset, $limit");

    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);
    if($results){
        return $results;
    }
    return 0;
}


/**
 * 指定类型消息计数
 *
 * @since 2.0.0
 * @param string $type
 * @param int $read
 * @param string $msg_status
 * @param int $sender_id
 * @return array|bool|int|null|object
 */
function tt_count_messages( $type = 'chat', $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $sender_id = 0) {
    return tt_get_messages($type, 0, 0, $read, $msg_status, $sender_id, true);
}


/**
 * 获取未读消息
 *
 * @since 2.0.0
 * @param string $type
 * @param int $limit
 * @param int $offset
 * @param string $msg_status
 * @return array|bool|int|null|object
 */
function tt_get_unread_messages( $type = 'chat', $limit = 20, $offset = 0, $msg_status = 'publish') {
    return tt_get_messages($type, $limit, $offset, MsgReadStatus::UNREAD, $msg_status);
}


/**
 * 未读消息计数
 *
 * @since 2.0.0
 * @param string $type
 * @param string $msg_status
 * @return array|bool|int|null|object
 */
function tt_count_unread_messages( $type = 'chat', $msg_status = 'publish' ) {
    return tt_count_messages($type, MsgReadStatus::UNREAD, $msg_status);
}


/**
 * 获取积分消息
 *
 *
 * @since 2.0.0
 * @param int $limit
 * @param int $offset
 * @param string $msg_status
 * @return array|bool|int|null|object
 */
function tt_get_credit_messages( $limit = 20, $offset = 0, $msg_status = 'all'){ //TODO: 积分消息不应该有msg_status，不可删除
    $messages = tt_get_messages('credit', $limit, $offset, MsgReadStatus::ALL, $msg_status); //NOTE: 积分消息不分已读未读
    return $messages ? $messages : array();
}


/**
 * 获取现金余额变动消息
 *
 *
 * @since 2.2.0
 * @param int $limit
 * @param int $offset
 * @param string $msg_status
 * @return array|bool|int|null|object
 */
function tt_get_cash_messages( $limit = 20, $offset = 0, $msg_status = 'all'){ //TODO: 余额消息不应该有msg_status，不可删除
    $messages = tt_get_messages('cash', $limit, $offset, MsgReadStatus::ALL, $msg_status); //NOTE: 余额消息不分已读未读
    return $messages ? $messages : array();
}


/**
 * 积分消息计数
 *
 * @since 2.0.0
 * @return array|bool|int|null|object
 */
function tt_count_credit_messages() {
    return tt_count_messages('credit', MsgReadStatus::ALL, 'all');
}


/**
 * 现金余额相关消息计数
 *
 * @since 2.2.0
 * @return array|bool|int|null|object
 */
function tt_count_cash_messages() {
    return tt_count_messages('cash', MsgReadStatus::ALL, 'all');
}


/**
 * 获取收到的对话消息
 *
 * @since 2.0.0
 * @param $sender_id
 * @param int $limit
 * @param int $offset
 * @param int $read
 * @return array|bool|int|null|object
 */
function tt_get_pm($sender_id = 0, $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD) {
    return tt_get_messages( 'chat', $limit, $offset, $read, 'publish', $sender_id);
}


/**
 * 获取来自指定发送者的聊天消息数量(sender_id为0时不指定发送者)
 *
 * @param int $sender_id
 * @param int $read
 * @return int
 */
function tt_count_pm($sender_id = 0, $read = MsgReadStatus::UNREAD) {
    return tt_count_messages('chat', $read, 'publish', $sender_id);
}


function tt_count_pm_cached($user_id = 0, $sender_id = 0, $read = MsgReadStatus::UNREAD) {
    if(wp_using_ext_object_cache()) {
        $user_id = $user_id ? : get_current_user_id();
        $key = 'tt_user_' . $user_id . '_unread';
        $cache = wp_cache_get($key);
        if($cache !== false) {
            return (int)$cache;
        }
        $unread = tt_count_pm($sender_id, $read);
        wp_cache_add($key, $unread, '', 3600);
        return $unread;
    }
    return tt_count_pm($sender_id, $read);
}


/**
 * 获取我发送的消息($to_user为0时不指定收件人)
 *
 * @since 2.0.0
 * @param int $to_user
 * @param int $limit
 * @param int $offset
 * @param int|string $read
 * @param string $msg_status
 * @param bool $count
 * @return array|bool|int|null|object|string
 */
function tt_get_sent_pm($to_user = 0, $limit = 20, $offset = 0, $read = MsgReadStatus::ALL, $msg_status = 'publish', $count = false ) {
    $sender_id = get_current_user_id();

    if(!$sender_id) return false;

    $type = 'chat';
    if(!in_array($read, array(MsgReadStatus::UNREAD, MsgReadStatus::READ, MsgReadStatus::UNREAD))) {
        $read = MsgReadStatus::ALL;
    }
    if(!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    $sql = sprintf("SELECT %s FROM $table_name WHERE `sender_id`=%d%s AND `msg_type` IN('$type')%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? "COUNT(*)" : "*", $sender_id, $to_user ? " AND `user_id`=$to_user" : "", $read!=MsgReadStatus::ALL ? " AND `msg_read`='$read'" : "", $msg_status!='all' ? " AND `msg_status`='$msg_status'" : "", $count ? "" : " LIMIT $offset, $limit");

    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);
    if($results){
        return $results;
    }
    return 0;
}


/**
 * 获取我发送的消息数量
 *
 * @since 2.0.0
 * @param int $to_user
 * @param int $read
 * @return int
 */
function tt_count_sent_pm($to_user = 0, $read = MsgReadStatus::ALL) {
    return tt_get_sent_pm($to_user, 0, 0, $read, 'publish', true);
}


/**
 * 删除消息
 *
 * @since 2.0.0
 * @param $msg_id
 * @return bool
 */
function tt_trash_message($msg_id) {
    $user_id = get_current_user_id();
    if(!$user_id) return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_status` = 'trash' WHERE `msg_id` = %d AND `user_id` = %d", $msg_id, $user_id) ) || $wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_status` = 'trash' WHERE `msg_id` = %d AND `sender_id` = %d", $msg_id, $user_id) )) { //TODO optimize
        return true;
    }
    return false;
}


/**
 * 恢复消息
 *
 * @since 2.0.0
 * @param $msg_id
 * @return bool
 */
function tt_restore_message($msg_id) { //NOTE: 应该不用
    $user_id = get_current_user_id();
    if(!$user_id) return false;

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';

    if($wpdb->query( $wpdb->prepare("UPDATE $table_name SET `msg_status` = 'publish' WHERE `msg_id` = %d AND `user_id` = %d", $msg_id, $user_id) )) {
        return true;
    }
    return false;
}


/**
 * 获取对话(双向消息)
 *
 * @since 2.0.0
 * @param $one_uid
 * @param int $limit
 * @param int $offset
 * @param int $read
 * @param string $msg_status
 * @param bool $count
 * @return array|bool|int|null|object|string
 */
function tt_get_bothway_chat( $one_uid, $limit = 20, $offset = 0, $read = MsgReadStatus::UNREAD, $msg_status = 'publish', $count = false ) {
    $user_id = get_current_user_id();

    if(!$user_id) return false;

    if(!in_array($read, array(MsgReadStatus::UNREAD, MsgReadStatus::READ, MsgReadStatus::ALL))) {
        $read = MsgReadStatus::UNREAD;
    }
    if(!in_array($msg_status, array('publish', 'trash', 'all'))) {
        $msg_status = 'publish';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'tt_messages';
    $concat_id_str = '\'' . $one_uid . '_' . $user_id . '\',' . '\'' . $user_id . '_' . $one_uid . '\'';

    $sql = sprintf("SELECT %s FROM $table_name WHERE CONCAT_WS('_', `user_id`, `sender_id`) IN (%s) AND `msg_type`='chat'%s%s ORDER BY (CASE WHEN `msg_read`='all' THEN 1 ELSE 0 END) DESC, `msg_date` DESC%s", $count ? "COUNT(*)" : "*", $concat_id_str, $read!=MsgReadStatus::ALL ? " AND `msg_read`='$read'" : "", $msg_status!='all' ? " AND `msg_status`='$msg_status'" : "", $count ? "" : " LIMIT $offset, $limit");
    $results = $count ? $wpdb->get_var($sql) : $wpdb->get_results($sql);

    if($results){
        return $results;
    }
    return 0;
}

// load_func('func.Referral');
/**
 * 捕获链接中的推广者
 *
 * @since 2.0.0
 * @return void
 */
function tt_retrieve_referral_keyword() {
    if(isset($_REQUEST['ref'])) {
        $ref = absint($_REQUEST['ref']);
        do_action('tt_ref', $ref);
    }
}
//add_action('template_redirect', 'tt_retrieve_referral_keyword');


function tt_handle_ref($ref) {
    //TODO
}
//add_action('tt_ref', 'tt_handle_ref', 10, 1);

// load_func('func.Query');
/**
 * 在主查询生成前过滤参数(因为使用了原生paged分页参数, 导致作者页文章以外其他tab的分页不能超过文章分页数量, 否则404)
 *
 * @since 2.0.0
 * @param WP_Query $q
 * @return void
 */
function tt_reset_uc_pre_get_posts( $q ) { //TODO 分页不存在时返回404
    if(get_post_type() == 'product'){
        $q->set( 'posts_per_page', 12 ); //商品archive页默认12篇每页
    }elseif(is_search() && isset($_GET['in_shop']) && $_GET['in_shop'] == 1){
        $q->set( 'posts_per_page', 12 ); //商品搜索页默认12篇每页
    }elseif($uctab = get_query_var('uctab') && $q->is_main_query()) {
        if(in_array($uctab, array('comments', 'stars', 'followers', 'following', 'chat'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }elseif($manage = get_query_var('manage_child_route') && $q->is_main_query()){
        if(in_array($manage, array('orders', 'users', 'members', 'coupons', 'cards'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }elseif($me = get_query_var('me_child_route') && $q->is_main_query()){
        if(in_array($me, array('orders', 'users', 'credits', 'messages', 'following', 'followers'))) {
            $q->set( 'posts_per_page', 1 );
            $q->set( 'offset', 0 ); //此时paged参数不起作用
        }
    }
}
add_action( 'pre_get_posts', 'tt_reset_uc_pre_get_posts' );

// load_func('func.Credit');
/**
 * 获取用户的积分
 *
 * @since 2.0.0
 * @param int $user_id
 * @return int
 */
function tt_get_user_credit($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    return (int)get_user_meta($user_id, 'tt_credits', true);
}


/**
 * 获取用户已经消费的积分
 *
 * @since 2.0.0
 * @param $user_id
 * @return int
 */
function tt_get_user_consumed_credit($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    return (int)get_user_meta($user_id, 'tt_consumed_credits', true);
}


/**
 * 更新用户积分(添加积分或消费积分)
 *
 * @since 2.0.0
 * @param int $user_id
 * @param int $amount
 * @param string $msg
 * @param bool $admin_handle
 * @return bool
 */
function tt_update_user_credit($user_id = 0, $amount = 0, $msg = '', $admin_handle = false){
    $user_id = $user_id ? : get_current_user_id();
    $before_credits = (int)get_user_meta($user_id, 'tt_credits', true);
    // 管理员直接更改用户积分
    if($admin_handle){
        $update = update_user_meta($user_id, 'tt_credits', max(0, (int)$amount) + $before_credits);
        if($update){
            // 添加积分消息
            $msg = $msg ? : sprintf(__('Administrator add %d credits to you, current credits %d', 'tt') , max(0, (int)$amount), max(0, (int)$amount) + $before_credits);
            tt_create_message( $user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
        return !!$update;
    }
    // 普通更新
    if($amount > 0){
        $update = update_user_meta($user_id, 'tt_credits', $before_credits + $amount); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if($update){
            // 添加积分消息
            $msg = $msg ? : sprintf(__('Gain %d credits', 'tt') , $amount);
            tt_create_message( $user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }elseif($amount < 0){
        if($before_credits + $amount < 0){
            return false;
        }
        $before_consumed = (int)get_user_meta($user_id, 'tt_consumed_credits', true);
        update_user_meta($user_id, 'tt_consumed_credits', $before_consumed - $amount);
        $update = update_user_meta($user_id, 'tt_credits', $before_credits + $amount);
        if($update){
            // 添加积分消息
            $msg = $msg ? : sprintf(__('Spend %d credits', 'tt') , absint($amount));
            tt_create_message( $user_id, 0, 'System', 'credit', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }
    return true;
}


/**
 * 积分充值到账
 *
 * @since 2.0.0
 * @param $order_id
 */
function tt_add_credits_by_order($order_id){
    $order = tt_get_order($order_id);
    if(!$order || $order->order_status != OrderStatus::TRADE_SUCCESS){
        return;
    }

    $user = get_user_by('id', $order->user_id);
    $credit_price = abs(tt_get_option('tt_hundred_credit_price', 1));
    $buy_credits = intval($order->order_total_price * 100 / $credit_price);
    tt_update_user_credit($order->user_id, $buy_credits, sprintf(__('Buy <strong>%d</strong> Credits, Cost %0.2f YUAN', 'tt') , $buy_credits, $order->order_total_price));

    // 发送邮件
    $blog_name = get_bloginfo('name');
    $subject = sprintf(__('Charge Credits Successfully - %s', 'tt'), $blog_name);
    $args = array(
        'blogName' => $blog_name,
        'creditsNum' => $buy_credits,
        'currentCredits' => tt_get_user_credit($user->ID),
        'adminEmail' => get_option('admin_email')
    );
    // tt_async_mail('', $user->user_email, $subject, $args, 'charge-credits-success');
    tt_mail('', $user->user_email, $subject, $args, 'charge-credits-success');
}


/**
 * 使用积分支付
 *
 * @since 2.0.0
 * @param int $amount
 * @param string $product_subject
 * @param bool $rest
 * @return bool|WP_Error
 */
function tt_credit_pay($amount = 0, $product_subject = '', $rest = false) {
    $amount = absint($amount);
    $user_id = get_current_user_id();
    if(!$user_id) {
        return $rest ? new WP_Error('unknown_user', __('You must sign in before payment', 'tt')) : false;
    }

    $credits = (int)get_user_meta($user_id, 'tt_credits', true);
    if($credits < $amount) {
        return $rest ? new WP_Error('insufficient_credits', __('You do not have enough credits to accomplish this payment', 'tt')) : false;
    }

//    $new_credits = $credits - $amount;
//    $update = update_user_meta($user_id, 'tt_credits', $new_credits);
//    if($update) {
//        $consumed = (int)get_user_meta($user_id, 'tt_consumed_credits', true);
//        update_user_meta($user_id, 'tt_consumed_credits', $consumed + $amount);
//        // 添加积分消息
//        $msg = sprintf(__('Spend %d credits', 'tt') , absint($amount));
//        tt_create_message( $user_id, 0, 'System', 'credit', $msg, '', 0, 'publish');
//    }

    $msg = $product_subject ? sprintf(__('Cost %d to buy %s', 'tt'), $amount, $product_subject) : '';
    tt_update_user_credit($user_id, $amount*(-1), $msg); //TODO confirm update
    return true;
}


/**
 * 用户注册时添加推广人和奖励积分
 *
 * @since 2.0.0
 * @param $user_id
 * @return void
 */
function tt_update_credit_by_user_register( $user_id ) {
    if( isset($_COOKIE['tt_ref']) && is_numeric($_COOKIE['tt_ref']) ){
        $ref_from = absint($_COOKIE['tt_ref']);
        //链接推广人与新注册用户(推广人meta)
        if(get_user_meta( $ref_from, 'tt_ref_users', true)){
            $ref_users = get_user_meta( $ref_from, 'tt_ref_users', true);
            if(empty($ref_users)){
                $ref_users = $user_id;
            }else{
                $ref_users .= ',' . $user_id;}
            update_user_meta( $ref_from, 'tt_ref_users', $ref_users);
        }else{
            update_user_meta( $ref_from, 'tt_ref_users', $user_id);
        }
        //链接推广人与新注册用户(注册人meta)
        update_user_meta( $user_id, 'tt_ref', $ref_from );
        $rec_reg_num = (int)tt_get_option('tt_rec_reg_num','5');
        $rec_reg = json_decode(get_user_meta( $ref_from, 'tt_rec_reg', true ));
        $ua = $_SERVER["REMOTE_ADDR"] . '&' . $_SERVER["HTTP_USER_AGENT"];
        if(!$rec_reg){
            $rec_reg = array();
            $new_rec_reg = array($ua);
        }else{
            $new_rec_reg = $rec_reg;
            array_push($new_rec_reg , $ua);
        }
        if( (count($rec_reg) < $rec_reg_num) &&  !in_array($ua,$rec_reg) ){
            update_user_meta( $ref_from , 'tt_rec_reg' , json_encode( $new_rec_reg ) );

            $reg_credit = (int)tt_get_option('tt_rec_reg_credit', '30');
            if($reg_credit){
                tt_update_user_credit($ref_from, $reg_credit, sprintf(__('获得注册推广（来自%1$s的注册）奖励%2$s积分', 'tt') , get_the_author_meta('display_name', $user_id), $reg_credit));
            }
        }
    }
    $credit = tt_get_option('tt_reg_credit', 50);
    if($credit){
        tt_update_user_credit($user_id, $credit, sprintf(__('获得注册奖励%s积分', 'tt') , $credit));
    }
}
add_action( 'user_register', 'tt_update_credit_by_user_register');


/**
 * 推广访问奖励积分
 *
 * @since 2.0.0
 */
function tt_update_credit_by_referral_view(){
    if( isset($_COOKIE['tt_ref']) && is_numeric($_COOKIE['tt_ref']) ){
        $ref_from = absint($_COOKIE['tt_ref']);
        $rec_view_num = (int)tt_get_option('tt_rec_view_num', '50');
        $rec_view = json_decode(get_user_meta( $ref_from, 'tt_rec_view', true ));
        $ua = $_SERVER["REMOTE_ADDR"] . '&' . $_SERVER["HTTP_USER_AGENT"];
        if(!$rec_view){
            $rec_view = array();
            $new_rec_view = array($ua);
        }else{
            $new_rec_view = $rec_view;
            array_push($new_rec_view , $ua);
        }
        //推广人推广访问数量，不受每日有效获得积分推广次数限制，但限制同IP且同终端刷分
        if( !in_array($ua, $rec_view) ){
            $ref_views = (int)get_user_meta( $ref_from, 'tt_aff_views', true);
            $ref_views++;
            update_user_meta( $ref_from, 'tt_aff_views', $ref_views);
        }
        //推广奖励，受每日有效获得积分推广次数限制及同IP终端限制刷分
        if( (count($rec_view) < $rec_view_num) && !in_array($ua, $rec_view) ){
            update_user_meta( $ref_from , 'tt_rec_view' , json_encode( $new_rec_view ) );
            $view_credit = (int)tt_get_option('tt_rec_view_credit','5');
            if($view_credit){
                tt_update_user_credit($ref_from, $view_credit, sprintf(__('获得访问推广奖励%d积分', 'tt') , $view_credit));
            }
        }
    }
}
add_action( 'tt_ref', 'tt_update_credit_by_referral_view');


/**
 * 发表评论时给作者添加积分
 *
 * @since 2.0.0
 * @param $comment_id
 * @param $comment_object
 */
function tt_comment_add_credit($comment_id, $comment_object){

    $user_id = $comment_object->user_id;
    if($user_id){
        $rec_comment_num = (int)tt_get_option('tt_rec_comment_num', 10);
        $rec_comment_credit = (int)tt_get_option('tt_rec_comment_credit', 5);
        $rec_comment = (int)get_user_meta( $user_id, 'tt_rec_comment', true );

        if( $rec_comment<$rec_comment_num && $rec_comment_credit ){
            tt_update_user_credit($user_id, $rec_comment_credit, sprintf(__('获得评论回复奖励%d积分', 'tt') , $rec_comment_credit));
            update_user_meta( $user_id, 'tt_rec_comment', $rec_comment+1);
        }
    }
}
add_action('wp_insert_comment', 'tt_comment_add_credit', 99, 2 );


/**
 * 每天 00:00 清空推广数据
 *
 * @since 2.0.0
 * @return void
 */
function tt_clear_rec_setup_schedule() {
    if ( ! wp_next_scheduled( 'tt_clear_rec_daily_event' ) ) {
        //~ 1193875200 是 2007/11/01 00:00 的时间戳
        wp_schedule_event( '1193875200', 'daily', 'tt_clear_rec_daily_event');
    }
}
add_action( 'wp', 'tt_clear_rec_setup_schedule' );

function tt_do_clear_rec_daily() {
    global $wpdb;
    $wpdb->query( " DELETE FROM $wpdb->usermeta WHERE meta_key='tt_rec_view' OR meta_key='tt_rec_reg' OR meta_key='tt_rec_post' OR meta_key='tt_rec_comment' OR meta_key='tt_resource_dl_users' " ); // TODO tt_resource_dl_users
}
add_action( 'tt_clear_rec_daily_event', 'tt_do_clear_rec_daily' );


/**
 * 在后台用户列表中显示积分
 *
 * @since 2.0.0
 * @param $columns
 * @return mixed
 */
function tt_credit_column( $columns ) {
    $columns['tt_credit'] = __('Credit','tt');
    return $columns;
}
add_filter( 'manage_users_columns', 'tt_credit_column' );

function tt_credit_column_callback( $value, $column_name, $user_id ) {

    if( 'tt_credit' == $column_name ){
        $credit = intval(get_user_meta($user_id, 'tt_credits', true));
        $void = intval(get_user_meta($user_id, 'tt_consumed_credits', true));
        $value = sprintf(__('总积分 %1$d 已消费 %2$d 剩余 %3$d', 'tt'), $credit+$void, $void, $credit );
    }
    return $value;
}
add_action( 'manage_users_custom_column', 'tt_credit_column_callback', 10, 3 );


/**
 * 按积分排序获取用户排行
 *
 * @since 2.0.0
 * @param int $limits
 * @param int $offset
 * @return array|null|object
 */
function tt_credits_rank($limits=10, $offset = 0){
    global $wpdb;
    $limits = (int)$limits;
    $offset = absint($offset);
    $ranks = $wpdb->get_results( " SELECT DISTINCT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key='tt_credits' ORDER BY -meta_value ASC LIMIT $limits OFFSET $offset" );
    return $ranks;
}


/**
 * 创建积分充值订单
 *
 * @since 2.0.0
 * @param $user_id
 * @param int $amount // 积分数量为100*$amount
 * @return array|bool
 */
function tt_create_credit_charge_order($user_id, $amount = 1){
    $amount = absint($amount);
    if(!$amount){
        return false;
    }
    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    $product_id = Product::CREDIT_CHARGE;
    $product_name = Product::CREDIT_CHARGE_NAME;
    $currency = 'cash';
    $hundred_credits_price = intval(tt_get_option('tt_hundred_credit_price', 1));
    $order_price = sprintf('%0.2f', $hundred_credits_price/100);
    $order_quantity = $amount * 100;
    $order_total_price = sprintf('%0.2f', $hundred_credits_price * $amount);

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
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
        array('%d', '%s', '%d', '%s', '%s', '%f', '%s', '%d', '%f', '%d')
    );
    if($insert) {
        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price
        );
    }
    return false;
}


/**
 * 获取每日签到按钮HTML
 *
 * @since 2.0.0
 * @param int $user_id
 * @return string
 */
function tt_daily_sign_anchor($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    if(get_user_meta($user_id, 'tt_daily_sign', true)){
        date_default_timezone_set ('Asia/Shanghai');
        $sign_date_meta = get_user_meta($user_id,'tt_daily_sign',true);
        $sign_date = date('Y-m-d', strtotime($sign_date_meta));
        $now_date = date('Y-m-d', time());
        if($sign_date != $now_date){
            return '<a class="btn btn-info btn-daily-sign" href="javascript:;" title="' . __('Sign to gain credits', 'tt') . '">'.__('Daily Sign', 'tt').'</a>';
        }else{
            return '<a class="btn btn-warning btn-daily-sign signed" href="javascript:;" title="' . sprintf(__('Signed on %s', 'tt'), $sign_date_meta) .'">' . __('Signed today', 'tt') . '</a>';
        }
    }else{
        return '<a class="btn btn-primary btn-daily-sign" href="javascript:;" id="daily_sign" title="' . __('Sign to gain credits', 'tt') . '">' . __('Daily Sign', 'tt').'</a>';
    }
}

/**
 * 每日签到
 *
 * @since 2.0.0
 * @return WP_Error|bool
 */
function tt_daily_sign(){
    date_default_timezone_set ('Asia/Shanghai');
    $user_id = get_current_user_id();
    if(!$user_id) {
        return new WP_Error('user_not_sign_in', __('You must sign in before daily sign', 'tt'), array('status' => 401));
    }
    $date = date('Y-m-d H:i:s',time());
    $sign_date_meta = get_user_meta($user_id, 'tt_daily_sign', true);
    $sign_date = date('Y-m-d', strtotime($sign_date_meta));
    $now_date = date('Y-m-d', time());
    if($sign_date != $now_date):
        update_user_meta($user_id, 'tt_daily_sign', $date);
        $credits = (int)tt_get_option('tt_daily_sign_credits', 10);
        tt_update_user_credit($user_id, $credits, sprintf(__('Gain %d credits for daily sign', 'tt'), $credits));
        //TODO clear VM cache
        return true;
    else:
        return new WP_Error('daily_signed', __('You have signed today', 'tt'), array('status' => 200));
    endif;
}

// load_func('func.Cash');
/**
 * 获取用户的现金余额
 *
 * @since 2.2.0
 * @param int $user_id
 * @return float
 */
function tt_get_user_cash($user_id = 0)
{
    $user_id = $user_id ? : get_current_user_id();
    // 注意 余额按分为单位存储
    return sprintf('%0.2f', (int)get_user_meta($user_id, 'tt_cash', true) / 100);
}


/**
 * 获取用户已经消费的现金
 *
 * @since 2.2.0
 * @param $user_id
 * @return int
 */
function tt_get_user_consumed_cash($user_id = 0)
{
    $user_id = $user_id ? : get_current_user_id();
    return sprintf('%0.2f', (int)get_user_meta($user_id, 'tt_consumed_cash', true) / 100);
}


/**
 * 更新用户余额(充值或消费现金余额)
 *
 * @since 2.2.0
 * @param int $user_id
 * @param int $amount(单位：分)
 * @param string $msg
 * @param bool $admin_handle
 * @return bool
 */
function tt_update_user_cash($user_id = 0, $amount = 0, $msg = '', $admin_handle = false)
{
    $user_id = $user_id ? : get_current_user_id();
    $before_cash = (int)get_user_meta($user_id, 'tt_cash', true);
    // 管理员直接更改用户余额
    if ($admin_handle) {
        $update = update_user_meta($user_id, 'tt_cash', max(0, (int)$amount) + $before_cash);
        if ($update) {
            // 添加余额变动消息
            $msg = $msg ? : sprintf(__('Administrator add %s cash to you, current cash balance %s', 'tt'), sprintf('%0.2f', max(0, (int)$amount) / 100), sprintf('%0.2f', max(0, (int)$amount) + $before_cash) / 100);
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
        return !!$update;
    }
    // 普通更新
    if ($amount > 0) {
        $update = update_user_meta($user_id, 'tt_cash', $before_cash + $amount); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if ($update) {
            // 添加余额变动消息
            $msg = $msg ? : sprintf(__('Charge %s cash, current cash balance %s', 'tt'), sprintf('%0.2f', $amount / 100), sprintf('%0.2f', (int)($amount + $before_cash) / 100));
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }
    elseif ($amount < 0) {
        if ($before_cash + $amount < 0) {
            return false;
        }
        $before_consumed = (int)get_user_meta($user_id, 'tt_consumed_cash', true);
        update_user_meta($user_id, 'tt_consumed_cash', $before_consumed - $amount);
        $update = update_user_meta($user_id, 'tt_cash', $before_cash + $amount);
        if ($update) {
            // 添加余额变动消息
            $msg = $msg ? : sprintf(__('Spend %s cash, current cash balance %s', 'tt'), sprintf('%0.2f', absint($amount) / 100), (int)($before_cash + $amount) / 100);
            tt_create_message($user_id, 0, 'System', 'cash', $msg, '', MsgReadStatus::UNREAD, 'publish');
        }
    }
    return true;
}


/**
 * 余额充值到账(目前只能卡密充值)
 *
 * @since 2.2.0
 * @param $card_id
 * @param $card_pwd
 * @return boolean|WP_Error
 */
function tt_add_cash_by_card($card_id, $card_pwd)
{
    $card = tt_get_card($card_id, $card_pwd);
    if (!$card) {
        return new WP_Error('card_not_exist', __('Card is not exist', 'tt'));
    } else if ($card->status != 1) {
        return new WP_Error('card_invalid', __('Card is not valid', 'tt'));
    }

    tt_mark_card_used($card->id);

    $user = wp_get_current_user();
    $cash = intval($card->denomination);
    $balance = tt_get_user_cash($user->ID);
    $update = tt_update_user_cash($user->ID, $cash, sprintf(__('Charge <strong>%s</strong> Cash by card, current cash balance %s', 'tt'), sprintf('%0.2f', $cash / 100), sprintf('%0.2f', $cash / 100 + $balance)));

    // 发送邮件
    $blog_name = get_bloginfo('name');
    $subject = sprintf(__('Charge Cash Successfully - %s', 'tt'), $blog_name);
    $args = array(
        'blogName' => $blog_name,
        'cashNum' => sprintf('%0.2f', $cash / 100),
        'currentCash' => tt_get_user_cash($user->ID),
        'adminEmail' => get_option('admin_email')
    );
    // tt_async_mail('', $user->user_email, $subject, $args, 'charge-cash-success');
    tt_mail('', $user->user_email, $subject, $args, 'charge-cash-success');
    if ($update) {
        return $cash; // 充值卡面额(分)
    }
    return $update;
}


/**
 * 使用现金余额支付
 *
 * @since 2.2.0
 * @param float $amount
 * @param string $product_subject
 * @param bool $rest
 * @return bool|WP_Error
 */
function tt_cash_pay($amount = 0.0, $product_subject = '', $rest = false)
{
    $amount = abs($amount);
    $user_id = get_current_user_id();
    if (!$user_id) {
        return $rest ? new WP_Error('unknown_user', __('You must sign in before payment', 'tt')) : false;
    }

    $balance = (float)tt_get_user_cash($user_id);
    if ($amount - $balance >= 0.0001) {
        return $rest ? new WP_Error('insufficient_cash', __('You do not have enough cash to accomplish this payment', 'tt')) : false;
    }

    $msg = $product_subject ? sprintf(__('Cost %0.2f cash to buy %s, current cash balance %s', 'tt'), $amount, $product_subject, $balance - $amount) : '';
    tt_update_user_cash($user_id, (int)($amount * (-100)), $msg); //TODO confirm update
    return true;
}

/**
 * 在后台用户列表中显示余额
 *
 * @since 2.2.0
 * @param $columns
 * @return mixed
 */
function tt_cash_column($columns)
{
    $columns['tt_cash'] = __('Cash Balance', 'tt');
    return $columns;
}
add_filter('manage_users_columns', 'tt_cash_column');

function tt_cash_column_callback($value, $column_name, $user_id)
{

    if ('tt_cash' == $column_name) {
        $cash = intval(get_user_meta($user_id, 'tt_cash', true));
        $void = intval(get_user_meta($user_id, 'tt_consumed_cash', true));
        $value = sprintf(__('总额 %1$s 已消费 %2$s 余额 %3$s', 'tt'), sprintf('%0.2f', $cash + $void), sprintf('%0.2f', $void), sprintf('%0.2f', $cash));
    }
    return $value;
}
add_action('manage_users_custom_column', 'tt_cash_column_callback', 10, 3);

// load_func('func.Card');
/**
 * 通过卡号卡密获取卡记录
 *
 * @since 2.2.0
 * @param $card_id
 * @param $card_secret
 *
 * @return array|null|object|void
 */
function tt_get_card($card_id, $card_secret) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $cards_table WHERE `card_id`='%s' AND `card_secret`='%s'", $card_id, $card_secret));
    return $row;
}

/**
 * 标记卡已被使用
 *
 * @since 2.2.0
 * @param $id
 *
 * @return false|int
 */
function tt_mark_card_used($id) {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    $update = $wpdb->update(
        $cards_table,
        array(
            'status' => 0
        ),
        array('id' => $id),
        array('%d'),
        array('%d')
    );
    return $update;
}

/**
 * 统计卡数量
 *
 * @since 2.2.0
 * @param $in_effect
 * @return int
 */
function tt_count_cards($in_effect = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    if($in_effect){
        $sql = sprintf("SELECT COUNT(*) FROM $cards_table WHERE `status`=1");
    }else{
        $sql = "SELECT COUNT(*) FROM $cards_table";
    }
    $count = $wpdb->get_var($sql);
    return $count;
}


/**
 * 删除card记录
 *
 * @since 2.2.0
 * @param $id
 * @return bool
 */
function tt_delete_card($id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    $delete = $wpdb->delete(
        $cards_table,
        array('id' => $id),
        array('%d')
    );
    return !!$delete;
}


/**
 * 随机生成一定数量的卡
 *
 * @since 2.2.0
 * @param $quantity
 * @param $denomination
 * @return array | bool
 */
function tt_gen_cards($quantity, $denomination) {
    $raw_cards = array();
    $cards = array();
    $place_holders = array();
    $denomination = absint($denomination);
    $create_time = current_time('mysql');
    for ($i = 0; $i < $quantity; $i++) {
        $card_id = Utils::generateRandomStr(10, 'number');
        $card_secret = Utils::generateRandomStr(16);
        array_push($raw_cards, array(
            'card_id' => $card_id,
            'card_secret' => $card_secret
        ));
        array_push($cards, $card_id, $card_secret, $denomination, $create_time, 1);
        array_push($place_holders, "('%s', '%s', '%d', '%s', '%d')");
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';

    $query = "INSERT INTO $cards_table (card_id, card_secret, denomination, create_time, status) VALUES ";
    $query .= implode(', ', $place_holders);
    $result = $wpdb->query( $wpdb->prepare("$query ", $cards));

    if (!$result) {
        return false;
    }
    return $raw_cards;
}


/**
 * 获取多条充值卡
 *
 * @since 2.2.0
 * @param int $limit
 * @param int $offset
 * @param bool $in_effect
 * @return array|null|object
 */
function tt_get_cards($limit = 20, $offset = 0, $in_effect = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $cards_table = $prefix . 'tt_cards';
    if($in_effect){
        $sql = sprintf("SELECT * FROM $cards_table WHERE `status`=1 ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    }else{
        $sql = sprintf("SELECT * FROM $cards_table ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset);
    }
    $results = $wpdb->get_results($sql);
    return $results;
}

// load_func('func.Member');
/**
 * 获取用户开通会员订单记录
 *
 * @since 2.0.0
 * @param int $user_id
 * @param int $limit
 * @param int $offset
 * @return array|null|object
 */
function tt_get_user_member_orders($user_id = 0, $limit = 20, $offset = 0){
    global $wpdb;
    $user_id = $user_id ? : get_current_user_id();
    $prefix = $wpdb->prefix;
    $table = $prefix . 'tt_orders';
    $vip_orders=$wpdb->get_results(sprintf("SELECT * FROM %s WHERE `user_id`=%d AND `product_id` IN (-1,-2,-3) ORDER BY `id` DESC LIMIT %d OFFSET %d", $table, $user_id, $limit, $offset));
    return $vip_orders;
}


/**
 * 统计用户会员订单数量
 *
 * @since 2.0.0
 * @param $user_id
 * @return int
 */
function tt_count_user_member_orders($user_id){
    global $wpdb;
    $user_id = $user_id ? : get_current_user_id();
    $prefix = $wpdb->prefix;
    $table = $prefix . 'tt_orders';
    $count=$wpdb->get_var(sprintf("SELECT COUNT(*) FROM %s WHERE `user_id`=%d AND `product_id` IN (-1,-2,-3)", $table, $user_id));
    return (int)$count;
}


/**
 * 获取会员类型描述文字
 *
 * @since 2.0.0
 * @param $code
 * @return string|void
 */
function tt_get_member_type_string($code){
    switch($code){
        case Member::PERMANENT_VIP:
            $type = __('Permanent Membership', 'tt');
            break;
        case Member::ANNUAL_VIP:
            $type = __('Annual Membership', 'tt');
            break;
        case Member::MONTHLY_VIP:
            $type = __('Monthly Membership', 'tt');
            break;
        case Member::EXPIRED_VIP:
            $type = __('Expired Membership', 'tt');
            break;
        default:
            $type = __('None Membership', 'tt');
    }
    return $type;
}


/**
 * 获取用户会员状态文字(有效性)
 *
 * @since 2.0.0
 * @param $code
 * @return string|void
 */
function tt_get_member_status_string($code) {
    switch($code){
        case Member::PERMANENT_VIP:
        case Member::ANNUAL_VIP:
        case Member::MONTHLY_VIP:
            return __('In Effective', 'tt');
            break;
        case Member::EXPIRED_VIP:
            return __('Expired', 'tt');
            break;
        default:
            return __('N/A', 'tt');
    }
}


/**
 * 根据会员ID获取会员记录
 *
 * @since 2.0.0
 * @param $id
 * @return array|null|object|void
 */
function tt_get_member($id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $members_table WHERE `id`=%d", $id));
    return $row;
}


/**
 * 根据用户ID获取会员记录
 *
 * @since 2.0.0
 * @param $user_id
 * @return array|null|object|void
 */
function tt_get_member_row($user_id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $row = $wpdb->get_row(sprintf("SELECT * FROM $members_table WHERE `user_id`=%d", $user_id));
    return $row;
}


/**
 * 添加会员记录(如果已存在记录则更新)
 *
 * @since 2.0.0
 * @param $user_id
 * @param $vip_type
 * @param $start_time
 * @param $end_time
 * @param bool $admin_handle 是否管理员手动操作
 * @return bool|int
 */
function tt_add_or_update_member($user_id, $vip_type, $start_time = 0, $end_time = 0, $admin_handle = false){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';

    if(!in_array($vip_type, array(Member::NORMAL_MEMBER, Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))){
        $vip_type = Member::NORMAL_MEMBER;
    }
    $duration = 0;
    switch ($vip_type){
        case Member::PERMANENT_VIP:
            $duration = Member::PERMANENT_VIP_PERIOD;
            break;
        case Member::ANNUAL_VIP:
            $duration = Member::ANNUAL_VIP_PERIOD;
            break;
        case Member::MONTHLY_VIP:
            $duration = Member::MONTHLY_VIP_PERIOD;
            break;
    }

    if(!$start_time) {
        $start_time = (int)current_time('timestamp');
    }elseif(is_string($start_time)){
        $start_time = strtotime($start_time);
    }

    if(is_string($end_time)){
        $end_time = strtotime($end_time);
    }
    $now = time();
    $row = tt_get_member_row($user_id);
    if($row) {
        $prev_end_time = strtotime($row->endTime);
        if($prev_end_time - $now > 100){ //尚未过期
            $start_time = strtotime($row->startTime); //使用之前的开始时间
            $end_time = $end_time ? : strtotime($row->endTime) + $duration;
        }else{ //已过期
            $start_time = $now;
            $end_time = $end_time ? : $now + $duration;
        }
        $update = $wpdb->update(
            $members_table,
            array(
                'user_type' => $vip_type,
                'startTime' => date('Y-m-d H:i:s', $start_time),
                'endTime' => date('Y-m-d H:i:s', $end_time),
                'endTimeStamp' => $end_time
            ),
            array('user_id' => $user_id),
            array('%d', '%s', '%s', '%d'),
            array('%d')
        );

        // 发送邮件
        $admin_handle ? tt_promote_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time)) : tt_open_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time));
        // 站内消息
        tt_create_message($user_id, 0, 'System', 'notification', __('你的会员状态发生了变化', 'tt'), sprintf( __('会员类型: %1$s, 到期时间: %2$s', 'tt'), tt_get_member_type_string($vip_type), date('Y-m-d H:i:s', $end_time) ));
        return $update !== false;
    }
    $start_time = $now;
    $end_time = $end_time ? : $now + $duration;
    $insert = $wpdb->insert(
        $members_table,
        array(
            'user_id' => $user_id,
            'user_type' => $vip_type,
            'startTime' => date('Y-m-d H:i:s', $start_time),
            'endTime' => date('Y-m-d H:i:s', $end_time),
            'endTimeStamp' => $end_time
        ),
        array('%d', '%d', '%s', '%s', '%d')
    );
    if($insert) {
        // 发送邮件
        $admin_handle ? tt_promote_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time)) : tt_open_vip_email($user_id, $vip_type, date('Y-m-d H:i:s', $start_time), date('Y-m-d H:i:s', $end_time));
        // 站内消息
        tt_create_message($user_id, 0, 'System', 'notification', __('你的会员状态发生了变化', 'tt'), sprintf( __('会员类型: %1$s, 到期时间: %2$s', 'tt'), tt_get_member_type_string($vip_type), date('Y-m-d H:i:s', $end_time) ));

        return $wpdb->insert_id;
    }
    return false;
}


/**
 * 删除会员记录
 *
 * @since 2.0.0
 * @param $user_id
 * @return bool
 */
function tt_delete_member($user_id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $delete = $wpdb->delete(
        $members_table,
        array('user_id' => $user_id),
        array('%d')
    ); //TODO deleted field
    return !!$delete;
}


/**
 * 删除会员记录
 *
 * @since 2.0.0
 * @param $id
 * @return bool
 */
function tt_delete_member_by_id($id){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $delete = $wpdb->delete(
        $members_table,
        array('id' => $id),
        array('%d')
    ); //TODO deleted field
    return !!$delete;
}


/**
 * 获取所有指定类型会员
 *
 * @since 2.0.0
 * @param int $member_type // -1 代表all
 * @param int $limit
 * @param int $offset
 * @return array|null|object
 */
function tt_get_vip_members($member_type = -1, $limit = 20, $offset = 0){
    if($member_type != -1 && !in_array($member_type, array(Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))){
        $member_type = -1;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $now = time();

    if($member_type == -1){
        $sql = sprintf("SELECT * FROM $members_table WHERE `user_type`>0 AND `endTimeStamp`>=%d LIMIT %d OFFSET %d", $now, $limit, $offset);
    }else{
        $sql = sprintf("SELECT * FROM $members_table WHERE `user_type`=%d AND `endTimeStamp`>%d LIMIT %d OFFSET %d", $member_type, $now, $limit, $offset);
    }

    $results = $wpdb->get_results($sql);
    return $results;
}


/**
 * 统计指定类型会员数量
 *
 * @since 2.0.0
 * @param int $member_type
 * @return int
 */
function tt_count_vip_members($member_type = -1){
    if($member_type != -1 && !in_array($member_type, array(Member::MONTHLY_VIP, Member::ANNUAL_VIP, Member::PERMANENT_VIP))){
        $member_type = -1;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $members_table = $prefix . 'tt_members';
    $now = time();

    if($member_type == -1){
        $sql = sprintf("SELECT COUNT(*) FROM $members_table WHERE `user_type`>0 AND `endTimeStamp`>=%d", $now);
    }else{
        $sql = sprintf("SELECT COUNT(*) FROM $members_table WHERE `user_type`=%d AND `endTimeStamp`>%d", $member_type, $now);
    }

    $count = $wpdb->get_var($sql);
    return $count;
}


/**
 * 会员标识
 *
 * @since 2.0.0
 * @param $user_id
 * @return string
 */
function tt_get_member_icon($user_id){
    $member = new Member($user_id);
    //0代表已过期或非会员 1代表月费会员 2代表年费会员 3代表永久会员
    if($member->is_permanent_vip()){
        return '<i class="vipico permanent_member" title="永久会员"></i>';
    }elseif($member->is_annual_vip()){
        return '<i class="vipico annual_member" title="年费会员"></i>';
    }elseif($member->is_monthly_vip()){
        return '<i class="vipico monthly_member" title="VIP会员"></i>';
    }
    return '<i class="vipico normal_member"></i>';
}


/**
 * 获取充值VIP价格
 *
 * @since 2.0.0
 * @param int $vip_type
 * @return float
 */
function tt_get_vip_price($vip_type = Member::MONTHLY_VIP){
    switch ($vip_type){
        case Member::MONTHLY_VIP:
            $price = tt_get_option('tt_monthly_vip_price', 10);
            break;
        case Member::ANNUAL_VIP:
            $price = tt_get_option('tt_annual_vip_price', 100);
            break;
        case Member::PERMANENT_VIP:
            $price = tt_get_option('tt_permanent_vip_price', 199);
            break;
        default:
            $price = 0;
    }
    return sprintf('%0.2f', $price);
}

/**
 * 创建开通VIP的订单
 *
 * @since 2.0.0
 * @param $user_id
 * @param int $vip_type
 * @return array|bool
 */
function tt_create_vip_order($user_id, $vip_type = 1){
    if(!in_array($vip_type * (-1), array(Product::MONTHLY_VIP, Product::ANNUAL_VIP, Product::PERMANENT_VIP))){
        $vip_type = Product::PERMANENT_VIP;
    }

    $order_id = tt_generate_order_num();
    $order_time = current_time('mysql');
    $product_id = $vip_type * (-1);
    $currency = 'cash';
    $order_price = tt_get_vip_price($vip_type);
    $order_total_price = $order_price;

    switch ($vip_type * (-1)){
        case Product::MONTHLY_VIP:
            $product_name = Product::MONTHLY_VIP_NAME;
            break;
        case Product::ANNUAL_VIP:
            $product_name = Product::ANNUAL_VIP_NAME;
            break;
        case Product::PERMANENT_VIP:
            $product_name = Product::PERMANENT_VIP_NAME;
            break;
        default:
            $product_name = '';
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => 1,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id
        ),
        array('%d', '%s', '%d', '%s', '%s', '%f', '%s', '%d', '%f', '%d')
    );
    if($insert) {
        return array(
            'insert_id' => $wpdb->insert_id,
            'order_id' => $order_id,
            'total_price' => $order_total_price
        );
    }
    return false;
}


/**
 * 根据订单产品ID获取对应VIP开通类型名
 *
 * @since 2.0.0
 * @param $product_id
 * @return string
 */
function tt_get_vip_product_name($product_id) {
    switch ($product_id){
        case Product::PERMANENT_VIP:
            return Product::PERMANENT_VIP_NAME;
        case Product::ANNUAL_VIP:
            return Product::ANNUAL_VIP_NAME;
        case Product::MONTHLY_VIP:
            return Product::MONTHLY_VIP_NAME;
        default:
            return "";
    }
}

// load_func('func.IP');
/**
 * 查询IP地址
 *
 * @since 2.0.0
 * @param $ip
 * @return array|mixed|object
 */
function tt_query_ip_addr($ip) {
    $url = 'http://freeapi.ipip.net/' . $ip;
    $body = wp_remote_retrieve_body(wp_remote_get($url));
//    "中国",                // 国家
//    "天津",                // 省会或直辖市（国内）
//    "天津",                // 地区或城市 （国内）
//    "",                   // 学校或单位 （国内）
//    "鹏博士",              // 运营商字段（只有购买了带有运营商版本的数据库才会有）
    //return json_decode($body);
    $arr = json_decode($body);
    if($arr[1] == $arr[2]){
        array_splice($arr, 2, 1);
    }
    return implode($arr);
}

// load_func('func.ShortCode');
// Toggle content
function tt_sc_toggle_content($atts, $content = null){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('hide'=>'no','title'=>'','color'=>''), $atts));
    return '<div class="' . tt_conditional_class('toggle-wrap', $hide=='no', 'show') . '"><div class="toggle-click-btn" style="color:' . $color . '"><i class="tico tico-angle-right"></i>' . $title . '</div><div class="toggle-content">' . $content . '</div></div>';
}
add_shortcode('toggle', 'tt_sc_toggle_content');

// 插入商品短代码
function tt_sc_product($atts, $content = null){
    extract(shortcode_atts(array('id'=>''), $atts));
    if(!empty($id)) {
        $vm = EmbedProductVM::getInstance(intval($id));
        $data = $vm->modelData;
        if(!isset($data->product_id)) return $id;
        $templates = new League\Plates\Engine(THEME_TPL . '/plates');
        $rating = $data->product_rating;
        $args = array(
            'thumb' => $data->product_thumb,
            'link' => $data->product_link,
            'name' => $data->product_name,
            'price' => $data->product_price,
            'currency' => $data->product_currency,
            'rating_value' => $rating['value'],
            'rating_count' => $rating['count'],

        );
        return $templates->render('embed-product', $args);
    }
    return '';
}
add_shortcode('product', 'tt_sc_product');

// Button
function tt_sc_button($atts, $content = null){
    extract(shortcode_atts(array('class'=>'default','size'=>'default','href'=>'','title'=>''), $atts));
    if(!empty($href)) {
        return '<a class="btnhref" href="' . $href . '" title="' . $title . '" target="_blank"><button type="button" class="btn btn-' . $class .' btn-' . $size . '">' . $content . '</button></a>';
    }else{
        return '<button type="button" class="btn btn-' . $class . ' btn-' . $size . '">' . $content . '</button>';
    }
}
add_shortcode('button', 'tt_sc_button');

// Call out
function tt_sc_infoblock($atts, $content = null){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('class'=>'info','title'=>''), $atts));
    return '<div class="contextual-callout callout-' . $class . '"><h4>' . $title . '</h4><p>' . $content . '</p></div>';
}
add_shortcode('callout', 'tt_sc_infoblock');

// Info bg
function tt_sc_infobg($atts, $content = null){
    $content = do_shortcode($content);
    extract(shortcode_atts(array('class'=>'info','closebtn'=>'no','bgcolor'=>'','color'=>'','showicon'=>'yes','title'=>''), $atts));
    $close_btn = $closebtn=='yes' ? '<span class="infobg-close"><i class="tico tico-close"></i></span>' : '';
    $div_class = $showicon!='no' ? 'contextual-bg bg-' . $class . ' showicon' : 'bg-' . $class . ' contextual';
    $content = $title ? '<h4>' . $title . '</h4><p>' . $content . '</p>' : '<p>' . $content . '</p>';
    return '<div class="' . $div_class . '">' . $close_btn . $content . '</div>';
}
add_shortcode('infobg', 'tt_sc_infobg');

// Login to visual
function tt_sc_l2v( $atts, $content ){
    if( !is_null( $content ) && !is_user_logged_in() ) $content = '<div class="bg-lr2v contextual-bg bg-warning"><i class="tico tico-group"></i>' . __(' 此处内容需要 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
    return $content;
}
add_shortcode( 'ttl2v', 'tt_sc_l2v' );

// Review to visual
function tt_sc_r2v( $atts, $content ){
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-comment"></i>' . __('此处内容需要登录并 <span class="user-login">发表评论</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $comments = get_comments( array('status' => 'approve', 'user_id' => $user_id, 'post_id' => $post->ID, 'count' => true ) );
                if(!$comments) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-comment"></i>' . __('此处内容需要登录并 <span class="tt-lv"><a href="#respond">发表评论</a></span> 才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttr2v', 'tt_sc_r2v' );

// 会员可见
function tt_sc_vipv( $atts, $content ){
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要开通会员并 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if(!$member->is_vip()) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通会员</a></span>才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvipv', 'tt_sc_vipv' );
// 月费会员可见
function tt_sc_vip1v( $atts, $content ){
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要开通月费会员及以上并 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type < 1) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通月费会员</a></span>及以上才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip1v', 'tt_sc_vip1v' );
// 年费会员可见
function tt_sc_vip2v( $atts, $content ){
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要开通年费会员及以上并 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type < 2) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通年费会员</a></span>及以上才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip2v', 'tt_sc_vip2v' );
// 永久会员可见
function tt_sc_vip3v( $atts, $content ){
    if( !is_null( $content ) ) :
        if(!is_user_logged_in()){
            $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要开通永久会员并 <span class="user-login">登录</span> 才可见', 'tt') . '</div>';
        }else{
            global $post;
            $user_id = get_current_user_id();
            if( $user_id != $post->post_author && !user_can($user_id,'edit_others_posts') ){
                $member = new Member($user_id);
                if($member->vip_type != 3) {
                    $content = '<div class="bg-lr2v contextual-bg bg-info"><i class="tico tico-group"></i>' . __('此处内容需要<span class="tt-lv"><a href="/me/membership">开通永久会员</a></span>才可见' , 'tt'). '</div>';
                }
            }
        }
    endif;
    return $content;
}
add_shortcode( 'ttvip3v', 'tt_sc_vip3v' );

// Pre tag
function tt_to_pre_tag( $atts, $content ){
    return '<div class="precode clearfix"><pre class="lang:default decode:true " >'.str_replace('#038;','', htmlspecialchars( $content,ENT_COMPAT, 'UTF-8' )).'</pre></div>';
}
add_shortcode( 'php', 'tt_to_pre_tag' );

// load_func('func.Download');
/**
 * 检查用户是否购买了文章内付费资源
 *
 * @since 2.0.0
 * @param $post_id
 * @param $resource_seq
 * @return bool
 */
function tt_check_bought_post_resources($post_id, $resource_seq) {
    $user_id = get_current_user_id();
    if(!$user_id) {
        return false;
    }

    $user_bought = get_user_meta($user_id, 'tt_bought_posts', true);
    if(empty($user_bought)){
        return false;
    }
    $user_bought = maybe_unserialize($user_bought);
    if(!isset($user_bought['p_' . $post_id])) {
        return false;
    }

    $post_bought_resources = $user_bought['p_' . $post_id];
    if(isset($post_bought_resources[$resource_seq]) && $post_bought_resources[$resource_seq]) {
        return true;
    }

    return false;
}


/**
 * 购买文章内容资源
 *
 * @since 2.0.0
 * @param $post_id
 * @param $resource_seq
 * @param $is_new_type
 * @return WP_Error|array
 */
function tt_bought_post_resource($post_id, $resource_seq, $is_new_type = false) {
    $user = wp_get_current_user();
    $user_id = $user->ID;
    if(!$user_id) {
        return new WP_Error('user_not_signin', __('You must sign in to continue your purchase', 'tt'), array('status' => 401));
    }

    //检查文章资源是否存在
    $resource_meta_key = $is_new_type ? 'tt_sale_dl2' : 'tt_sale_dl';
    $post_resources = explode(PHP_EOL, trim(get_post_meta($post_id, $resource_meta_key, true)));
    if(!isset($post_resources[$resource_seq - 1])) {
        return new WP_Error('post_resource_not_exist', __('The resource you willing to buy is not existed', 'tt'), array('status' => 404));
    }
    $the_post_resource = explode('|', $post_resources[$resource_seq - 1]);
    // <!-- 资源名称|资源下载url1_密码1,资源下载url2_密码2|资源价格|币种 -->
    $currency = $is_new_type && isset($the_post_resource[3]) && strtolower(trim($the_post_resource[3]) === 'cash') ? 'cash' : 'credit';
    $price = isset($the_post_resource[2]) ? abs(trim($the_post_resource[2])) : 1;
    $resource_name = $the_post_resource[0];
    if ($is_new_type) {
        $pans = explode(',', $the_post_resource[1]);
        $pan_detail = explode('__', $pans[0]);
        $resource_link = $pan_detail[0];
        $resource_pass = isset($pan_detail[1]) ? trim($pan_detail[1]) : __('None', 'tt');
    } else {
        $resource_link = $the_post_resource[1];
        $resource_pass = isset($the_post_resource[3]) ? trim($the_post_resource[3]) : __('None', 'tt');
    }

    //检查是否已购买
    if($is_new_type ? tt_check_bought_post_resources2($post_id, $resource_seq) : tt_check_bought_post_resources($post_id, $resource_seq)) {
        return new WP_Error('post_resource_bought', __('You have bought the resource yet, do not repeat a purchase', 'tt'), array('status' => 200));
    }

    // 先计算VIP价格优惠
    $member = new Member($user);
    $vip_price = $price;
    $vip_type = $member->vip_type;
    switch ($vip_type) {
        case Member::MONTHLY_VIP:
            $vip_price = absint(tt_get_option('tt_monthly_vip_discount', 100) * $price / 100);
            break;
        case Member::ANNUAL_VIP:
            $vip_price = absint(tt_get_option('tt_annual_vip_discount', 90) * $price / 100);
            break;
        case Member::PERMANENT_VIP:
            $vip_price = absint(tt_get_option('tt_permanent_vip_discount', 80) * $price / 100);
            break;
    }
    $vip_string = tt_get_member_type_string($vip_type);

    if ($is_new_type) {
        $create = tt_create_resource_order($post_id, $resource_name, $resource_seq, $vip_price, $currency === 'cash');
        if ($create instanceof WP_Error) {
            return $create;
        } elseif (!$create) {
            return new WP_Error('create_order_failed', __('Create order failed', 'tt'));
        }
        $checkout_nonce = wp_create_nonce('checkout');
        $checkout_url = add_query_arg(array('oid' => $create['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
        if ($vip_price - 0 >= 0.01) {
            $create['url'] = $checkout_url;
        } else {
            $create = array_merge($create, array(
                'cost' => 0,
                'text' => sprintf(__('消费: %1$d (%2$s优惠, 原价%3$d)', 'tt'), $vip_price, $vip_string, $price),
                'vip_str' => $vip_string
            ));
        }
        return tt_api_success(__('Create order successfully', 'tt'), array('data' => $create));
    } else {
        //检查用户积分是否足够
        $payment = tt_credit_pay($vip_price, $resource_name, true);
        if($payment instanceof WP_Error) {
            return $payment;
        }

        $user_bought = get_user_meta($user_id, 'tt_bought_posts', true);
        if(empty($user_bought)){
            $user_bought = array(
                'p_' . $post_id => array($resource_seq => true)
            );
        }else{
            $user_bought = maybe_unserialize($user_bought);
            if(!isset($user_bought['p_' . $post_id])) {
                $user_bought['p_' . $post_id] = array($resource_seq => true);
            }else{
                $buy_seqs = $user_bought['p_' . $post_id];
                $buy_seqs[$resource_seq] = true;
                $user_bought['p_' . $post_id] = $buy_seqs;
            }
        }
        $save = maybe_serialize($user_bought);
        $update = update_user_meta($user_id, 'tt_bought_posts', $save); //Meta ID if the key didn't exist; true on successful update; false on failure or if $meta_value is the same as the existing meta value in the database.
        if(!$update){ //TODO 返还扣除的积分
            return new WP_Error('post_resource_bought_failure', __('Failed to buy the resource, or maybe you have bought before', 'tt'), array('status' => 500));
        }

        // 发送邮件
        $subject = __('Payment for the resource finished successfully', 'tt');
        $balance = get_user_meta($user_id, 'tt_credits', true);
        $args = array(
            'adminEmail' => get_option('admin_email'),
            'resourceName' => $resource_name,
            'resourceLink' => $resource_link,
            'resourcePass' => $resource_pass,
            'spentCredits' => $price,
            'creditsBalance' => $balance
        );
        tt_async_mail('', $user->user_email, $subject, $args, 'buy-resource');

        if($price - $vip_price > 0) {
            $text = sprintf(__('消费积分: %1$d (%2$s优惠, 原价%3$d)<br>当前积分余额: %2$d', 'tt'), $vip_price, $vip_string, $price, $balance);
            $cost = $vip_price;
        }else{
            $text = sprintf(__('消费积分: %1$d<br>当前积分余额: %2$d', 'tt'), $price, $balance);
            $cost = $price;
        }
        return array(
            'cost' => $cost,
            'text' => $text,
            'vip_str' => $vip_string,
            'balance' => $balance
        );
    }
}

// 2.4+ 新引入

/**
 * 创建资源订单(文章内嵌资源对接订单系统)
 *
 * @since 1.1.0
 * @param $product_id
 * @param string $product_name
 * @param number $resource_seq
 * @param $order_price
 * @param $is_cash
 * @return bool|array
 */
function tt_create_resource_order($product_id, $product_name, $resource_seq, $order_price = 1, $is_cash){
    $user_id = get_current_user_id();
    $order_id = tt_generate_order_num() . '_' . $resource_seq;
    $order_time = current_time('mysql');
    $currency = $is_cash ? 'cash' : 'credit';
    $order_quantity = 1;
    $order_total_price = $order_price;

    global $wpdb;
    $prefix = $wpdb->prefix;
    $orders_table = $prefix . 'tt_orders';
    $insert = $wpdb->insert(
        $orders_table,
        array(
            'parent_id' => 0,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'order_time' => $order_time,
            'order_price' => $order_price,
            'order_currency' => $currency,
            'order_quantity' => $order_quantity,
            'order_total_price' => $order_total_price,
            'user_id' => $user_id,
            'order_status' => $order_total_price - 0 < 0.01 ? OrderStatus::TRADE_SUCCESS : OrderStatus::WAIT_PAYMENT
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
            '%d',
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
 * 检查用户是否购买了文章内付费资源
 *
 * @since 2.0.0
 * @param $post_id
 * @param $resource_seq
 * @return bool
 */
function tt_check_bought_post_resources2($post_id, $resource_seq) {
    $user_id = get_current_user_id();
    if(!$user_id) {
        return false;
    }

    $orders = tt_get_user_post_resource_orders($user_id, $post_id);
    if (count($orders) == 0) {
        return false;
    }

    $suffix = '_' . $resource_seq;
    $length = strlen($suffix);
    foreach ($orders as $order) {
        if (substr($order->order_id, -1 * $length) == $suffix) {
            return true;
        }
    }

    return false;
}

/**
 * 获取文章内嵌的资源列表
 * 
 * @param $post_id
 * @return array
 */
function tt_get_post_sale_resources($post_id) {
    $sale_dls = trim(get_post_meta($post_id, 'tt_sale_dl2', true));
    $sale_dls = !empty($sale_dls) ? explode(PHP_EOL, $sale_dls) : array();
    $resources = array();
    $seq = 0;
    foreach ($sale_dls as $sale_dl) {
        $sale_dl = explode('|', $sale_dl);
        if(count($sale_dl) < 3) {
            continue;
        } else {
            $seq++;
        }
        $resource = array();
        $resource['seq'] = $seq;
        $resource['name'] = $sale_dl[0];
        $pans = explode(',', $sale_dl[1]);
        $downloads = array();
        foreach ($pans as $pan) {
            $pan_details = explode('__', $pan);
            array_push($downloads, array(
               'url' => $pan_details[0],
                'password' => $pan_details[1]
            ));
        }
        $resource['downloads'] = $downloads;
        $resource['price'] = isset($sale_dl[2]) ? trim($sale_dl[2]) : 1;
        $resource['currency'] = isset($sale_dl[3]) && strtolower(trim($sale_dl[3])) == 'cash' ? 'cash' : 'credit';
        array_push($resources, $resource);
    }
    return $resources;
}

/**
 * 获取并生成文章内嵌资源的HTML内容用于邮件发送
 * 
 * @param $post_id
 * @param $seq
 * @return array|string
 */
function tt_get_post_download_content($post_id, $seq){
    $content = '';
    $resources = tt_get_post_sale_resources($post_id);
    if ($seq == 0 || $seq > count($resources)) {
        return $content;
    }
    $resource = $resources[$seq - 1];
    $downloads = $resource['downloads'];
    foreach($downloads as $download) {
        $content .= sprintf(__('<li style="margin: 0 0 10px 0;"><p style="padding: 5px 0; margin: 0;">%1$s</p><p style="padding: 5px 0; margin: 0;">下载链接：<a href="%2$s" title="%1$s" target="_blank">%2$s</a>下载密码：%3$s</p></li>', 'tt'), $resource['name'], $download['url'], $download['password']);
    }
    return $content;
}
// load_func('func.Image');
/**
 * 给上传的图片生成独一无二的图片名
 *
 * @since 2.0.0
 * @param $filename
 * @param $type
 * @return string
 */
function tt_unique_img_name($filename, $type){//$type -> image/png
    $tmp_name = mt_rand(10,25) . time() . $filename;
    $ext = str_replace('image/', '', $type);
    return md5($tmp_name) . '.' . $ext;
}


/**
 * 获取图片信息
 *
 * @since 2.0.0
 * @param $img
 * @return array|bool
 */
function tt_get_img_info( $img ){
    $imageInfo = getimagesize($img);
    if( $imageInfo!== false) {
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
        $info = array(
            "width"     => $imageInfo[0],
            "height"    => $imageInfo[1],
            "type"      => $imageType,
            "mime"      => $imageInfo['mime'],
        );
        return $info;
    }else {
        return false;
    }
}


/**
 * 裁剪图片并转换为JPG
 *
 * @since 2.0.0
 * @param $ori
 * @param string $dst
 * @param int $dst_width
 * @param int $dst_height
 * @param bool $delete_ori
 */
function tt_resize_img( $ori, $dst = '', $dst_width = 100, $dst_height = 100, $delete_ori = false ){ //绝对路径, 带文件名
    $info = tt_get_img_info( $ori );

    if( $info ){
        if( $info['type']=='jpg' || $info['type']=='jpeg' ){
            $im = imagecreatefromjpeg( $ori );
        }
        if( $info['type']=='gif' ){
            $im = imagecreatefromgif( $ori );
        }
        if( $info['type']=='png' ){
            $im = imagecreatefrompng( $ori );
        }
        if( $info['type']=='bmp' ){
            $im = imagecreatefromwbmp( $ori );
        }
        if( $info['width'] > $info['height'] ){
            $height = intval($info['height']);
            $width = $height;
            $x = ($info['width']-$width)/2;
            $y = 0;
        } else {
            $width = intval($info['width']);
            $height = $width;
            $x = 0;
            $y = ($info['height']-$height) / 2;
        }
        $new_img = imagecreatetruecolor( $width, $height );
        imagecopy($new_img, $im, 0, 0, $x, $y, $info['width'], $info['height']);
        $scale = $dst_width / $width;
        $target = imagecreatetruecolor($dst_width, $dst_height);
        $final_w = intval($width * $scale);
        $final_h = intval($height * $scale);
        imagecopyresampled( $target, $new_img, 0, 0, 0, 0, $final_w, $final_h, $width, $height );
        imagejpeg( $target, $dst ? : $ori );
        imagedestroy( $im );
        imagedestroy( $new_img );
        imagedestroy( $target );

        if($delete_ori){
            unlink($ori);
        }
    }
    return;
}


/**
 * 根据头像上传配置用户头像类型并清理对应VM缓存
 *
 * @since 2.0.0
 * @param int $user_id
 */
function tt_update_user_avatar_by_upload($user_id = 0){
    $user_id = $user_id ? : get_current_user_id();
    update_user_meta($user_id, 'tt_avatar_type', 'custom');

    //删除VM缓存
    //tt_clear_cache_key_like('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    //tt_clear_cache_key_like('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    //删除头像缓存
    //tt_clear_cache_key_like('tt_cache_daily_avatar_' . strval($user_id));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'small' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'medium' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'large' . Utils::getCurrentDateTimeStr('day')));
}


/**
 * 开放平台登录后清理头像等资料缓存
 *
 * @param $user_id
 * @param string $avatar_type
 */
function tt_update_user_avatar_by_oauth($user_id, $avatar_type = 'qq')
{
    if (!$user_id) return;

    update_user_meta($user_id, 'tt_avatar_type', $avatar_type); //TODO filter $avatar_type

    //删除VM缓存
    delete_transient('tt_cache_daily_vm_MeSettingsVM_user' . $user_id);
    delete_transient('tt_cache_daily_vm_UCProfileVM_author' . $user_id);
    //删除头像缓存
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'small' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'medium' . Utils::getCurrentDateTimeStr('day')));
    delete_transient('tt_cache_daily_avatar_' . $user_id . '_' . md5(strval($user_id) . 'large' . Utils::getCurrentDateTimeStr('day')));
}

// load_func('func.Oauth');
/**
 * 判断用户是否已经绑定了开放平台账户
 *
 * @since 2.0.0
 * @param string $type
 * @param int $user_id
 * @return bool
 */
function tt_has_connect($type = 'qq', $user_id = 0){
    if(!in_array($type, array('qq', 'weibo', 'weixin'))) {
        return  false;
    }
    $user_id = $user_id ? : get_current_user_id();
    switch ($type){
        case 'qq':
            $instance = new OpenQQ($user_id);
            return $instance->isOpenConnected();
        case 'weibo':
            $instance = new OpenWeibo($user_id);
            return $instance->isOpenConnected();
        case 'weixin':
            $instance = new OpenWeiXin($user_id);
            return $instance->isOpenConnected();
    }

    return false;
}

// load_func('func.API.Actions');
/**
 * 执行向API发送的action
 *
 * @since 2.0.0
 * @param $action
 * @return WP_Error|WP_REST_Response
 */
function tt_exec_api_actions($action)
{
    switch ($action) {
        case 'daily_sign' :
            $result = tt_daily_sign();
            if ($result instanceof WP_Error) {
                return $result;
            }
            if ($result) {
                return tt_api_success(sprintf(__('Daily sign successfully and gain %d credits', 'tt'), (int)tt_get_option('tt_daily_sign_credits', 10)));
            }
            break;
        case 'credits_charge' :
            $charge_order = tt_create_credit_charge_order(get_current_user_id(), intval($_POST['amount']));
            if (!$charge_order) {
                return tt_api_fail(__('Create credits charge order failed', 'tt'));
            }
            elseif (is_array($charge_order) && isset($charge_order['order_id'])) {
                // $pay_method = tt_get_cash_pay_method();
                // switch ($pay_method){
                //     case 'alipay':
                //         return tt_api_success('', array('data' => array( // 返回payment gateway url
                //             'orderId' => $charge_order['order_id'],
                //             'url' => tt_get_alipay_gateway($charge_order['order_id'])
                //         )));
                //     default: //qrcode
                //         return tt_api_success('', array('data' => array( // 直接返回扫码支付url,后面手动修改订单
                //             'orderId' => $charge_order['order_id'],
                //             'url' => tt_get_qrpay_gateway($charge_order['order_id'])
                //         )));
                // }
                $checkout_nonce = wp_create_nonce('checkout');
                $checkout_url = add_query_arg(array('oid' => $charge_order['order_id'], 'spm' => $checkout_nonce), tt_url_for('checkout'));
                $charge_order['url'] = $checkout_url;
                return tt_api_success(__('Create order successfully', 'tt'), array('data' => $charge_order));
            }
            break;
        case 'add_credits' :
            $user_id = absint($_POST['uid']);
            $amount = absint($_POST['num']);
            $result = tt_update_user_credit($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user credits successfully', 'tt'));
            }
            return tt_api_fail(__('Update user credits failed', 'tt'));
        case 'add_cash' :
            $user_id = absint($_POST['uid']);
            $amount = absint($_POST['num']);
            $result = tt_update_user_cash($user_id, $amount, '', true);
            if ($result) {
                return tt_api_success(__('Update user cash successfully', 'tt'));
            }
            return tt_api_fail(__('Update user cash failed', 'tt'));
        case 'apply_card' :
            $card_id = htmlspecialchars($_POST['card_id']);
            $card_secret = htmlspecialchars($_POST['card_secret']);
            $result = tt_add_cash_by_card($card_id, $card_secret);
            if ($result instanceof WP_Error) {
                return $result;
            }
            elseif ($result) {
                return tt_api_success(sprintf(__('Apply card to charge successfully, balance add %0.2f', 'tt'), $result / 100));
            }
            return tt_api_fail(__('Apply card to charge failed', 'tt'));
    }
    return null;
}
// load_func('func.Bulletin');
/**
 * 创建公告自定义文章类型
 *
 * @since 2.0.5
 * @return void
 */
function tt_create_bulletin_post_type() {
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    register_post_type( 'bulletin',
        array(
            'labels' => array(
                'name' => _x( 'Bulletins', 'taxonomy general name', 'tt' ),
                'singular_name' => _x( 'Bulletin', 'taxonomy singular name', 'tt' ),
                'add_new' => __( 'Add New Bulletin', 'tt' ),
                'add_new_item' => __( 'Add New Bulletin', 'tt' ),
                'edit' => __( 'Edit', 'tt' ),
                'edit_item' => __( 'Edit Bulletin', 'tt' ),
                'new_item' => __( 'Add Bulletin', 'tt' ),
                'view' => __( 'View', 'tt' ),
                'all_items' => __( 'All Bulletins', 'tt' ),
                'view_item' => __( 'View Bulletin', 'tt' ),
                'search_items' => __( 'Search Bulletin', 'tt' ),
                'not_found' => __( 'Bulletin not found', 'tt' ),
                'not_found_in_trash' => __( 'Bulletin not found in trash', 'tt' ),
                'parent' => __( 'Parent Bulletin', 'tt' ),
                'menu_name' => __( 'Bulletins', 'tt' ),
            ),

            'public' => true,
            'menu_position' => 16,
            'supports' => array( 'title', 'author', 'editor',/* 'comments', */'excerpt' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-megaphone',
            'has_archive' => false,
            'rewrite'	=> array('slug'=>$bulletin_slug)
        )
    );
}
add_action( 'init', 'tt_create_bulletin_post_type' );


/**
 * 为公告启用单独模板
 *
 * @since 2.0.0
 * @param $template_path
 * @return string
 */
function tt_include_bulletin_template_function( $template_path ) {
    if ( get_post_type() == 'bulletin' ) {
        if ( is_single() ) {
            //指定单个公告模板
            if ( $theme_file = locate_template( array ( 'core/templates/bulletins/tpl.Bulletin.php' ) ) ) {
                $template_path = $theme_file;
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'tt_include_bulletin_template_function', 1 );


/**
 * 自定义公告的链接
 *
 * @since 2.0.0
 * @param $link
 * @param object $post
 * @return string|void
 */
function tt_custom_bulletin_link( $link, $post = null ){
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    $bulletin_slug_mode = tt_get_option('tt_bulletin_link_mode')=='post_name' ? $post->post_name : $post->ID;
    if ( $post->post_type == 'bulletin' ){
        return home_url( $bulletin_slug . '/' . $bulletin_slug_mode . '.html' );
    } else {
        return $link;
    }
}
add_filter('post_type_link', 'tt_custom_bulletin_link', 1, 2);


/**
 * 处理公告自定义链接Rewrite规则
 *
 * @since 2.0.0
 * @return void
 */
function tt_handle_custom_bulletin_rewrite_rules(){
    $bulletin_slug = tt_get_option('tt_bulletin_archives_slug', 'bulletin');
    if(tt_get_option('tt_bulletin_link_mode') == 'post_name'):
        add_rewrite_rule(
            $bulletin_slug . '/([一-龥a-zA-Z0-9_-]+)?.html([\s\S]*)?$',
            'index.php?post_type=bulletin&name=$matches[1]',
            'top' );
    else:
        add_rewrite_rule(
            $bulletin_slug . '/([0-9]+)?.html([\s\S]*)?$',
            'index.php?post_type=bulletin&p=$matches[1]',
            'top' );
    endif;
}
add_action( 'init', 'tt_handle_custom_bulletin_rewrite_rules' );

// load_func('func.Role');

/**
 * 允许投稿者上传图片
 */
function tt_allow_contributor_uploads() {
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}

if ( current_user_can('contributor') && !current_user_can('upload_files') ) {
    add_action('init', 'tt_allow_contributor_uploads');
}

/**
 * 前台投稿页面的媒体上传预览准备数据filter掉post_id
 */
function tt_remove_post_id_for_front_contribute($settings) {
    if (get_query_var('me_child_route') === "newpost") {
        $settings['post'] = array();
    }
    return $settings;
}

if(!is_admin()) {
    add_filter('media_view_settings', 'tt_remove_post_id_for_front_contribute', 10, 1);
}


if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_func('func.Shop.Loader');
}

/* 载入类 */
load_class('class.Avatar');
load_class('class.Captcha');
load_class('class.Open');
load_class('class.PostImage');
load_class('class.Utils');
load_class('class.Member');
load_class('class.Async.Task');
load_class('class.Async.Email');
load_class('class.Enum');
// Plates模板引擎
load_class('plates/Engine');
load_class('plates/Extension/ExtensionInterface');
load_class('plates/Template/Data');
load_class('plates/Template/Directory');
load_class('plates/Template/FileExtension');
load_class('plates/Template/Folder');
load_class('plates/Template/Folders');
load_class('plates/Template/Func');
load_class('plates/Template/Functions');
load_class('plates/Template/Name');
load_class('plates/Template/Template');
load_class('plates/Extension/Asset');
load_class('plates/Extension/URI');

if (is_admin()) {
    load_class('class.Tgm.Plugin.Activation');
}
if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_class('shop/class.Product');
    load_class('shop/class.OrderStatus');
    load_class('shop/alipay/alipay_notify.class');
    load_class('shop/alipay/alipay_submit.class');
}

/* 载入数据模型 */
load_vm('vm.Base');
load_vm('vm.Home.Hero');
load_vm('vm.Home.Popular');
load_vm('vm.Stickys');
load_vm('vm.Home.CMSCats');
load_vm('vm.Home.Latest');
load_vm('vm.Home.FeaturedCategory');
load_vm('vm.Single.Post');
load_vm('vm.Single.Page');
load_vm('vm.Post.Comments');
load_vm('vm.Category.Posts');
load_vm('vm.Tag.Posts');
load_vm('vm.Date.Archive');
load_vm('vm.Term.Posts');
load_vm('widgets/vm.Widget.Author');
load_vm('widgets/vm.Widget.HotHit.Posts');
load_vm('widgets/vm.Widget.HotReviewed.Posts');
load_vm('widgets/vm.Widget.Recent.Comments');
load_vm('widgets/vm.Widget.Latest.Posts');
load_vm('widgets/vm.Widget.CreditsRank');
load_vm('widgets/vm.Widget.HotProduct');
load_vm('uc/vm.UC.Latest');
load_vm('uc/vm.UC.Stars');
load_vm('uc/vm.UC.Comments');
load_vm('uc/vm.UC.Followers');
load_vm('uc/vm.UC.Following');
load_vm('uc/vm.UC.Chat');
load_vm('uc/vm.UC.Profile');
load_vm('me/vm.Me.Settings');
load_vm('me/vm.Me.Credits');
load_vm('me/vm.Me.Drafts');
load_vm('me/vm.Me.Messages');
load_vm('me/vm.Me.Notifications');
load_vm('me/vm.Me.EditPost');
load_vm('vm.Search');
if (TT_PRO && tt_get_option('tt_enable_shop', false)) {
    load_vm('shop/vm.Shop.Header.SubNav');
    load_vm('shop/vm.Shop.Home');
    load_vm('shop/vm.Shop.Category');
    load_vm('shop/vm.Shop.Tag');
    load_vm('shop/vm.Shop.Search');
    load_vm('shop/vm.Shop.Product');
    load_vm('shop/vm.Shop.Comment');
    load_vm('shop/vm.Shop.LatestRated');
    load_vm('shop/vm.Shop.ViewHistory');
    load_vm('shop/vm.Embed.Product');
}
load_vm('bulletin/vm.Bulletin');
load_vm('bulletin/vm.Bulletins');
if (TT_PRO) {
    load_vm('me/vm.Me.Order');
    load_vm('me/vm.Me.Orders');
    load_vm('me/vm.Me.Membership');
	load_vm('me/vm.Me.Cash');
    load_vm('management/vm.Mg.Status');
    load_vm('management/vm.Mg.Comments');
    load_vm('management/vm.Mg.Coupons');
    load_vm('management/vm.Mg.Members');
    load_vm('management/vm.Mg.Orders');
    load_vm('management/vm.Mg.Order');
    load_vm('management/vm.Mg.Posts');
    load_vm('management/vm.Mg.Users');
    load_vm('management/vm.Mg.User');
    load_vm('management/vm.Mg.Products');
	load_vm('management/vm.Mg.Cards');
}

/* 载入小工具 */
load_widget('wgt.TagCloud');
load_widget('wgt.Author');
load_widget('wgt.HotHits.Posts');
load_widget('wgt.HotReviews.Posts');
load_widget('wgt.RecentComments');
load_widget('wgt.Latest.Posts');
load_widget('wgt.UC');
load_widget('wgt.Float');
load_widget('wgt.EnhancedText');
load_widget('wgt.Postmeta');
// load_widget('wgt.Donate');
// load_widget('wgt.AwardCoupon');
load_widget('wgt.CreditsRank');
// load_widget('wgt.Search');
load_widget('wgt.HotProduct');
load_widget('wgt.HotProduct');

/* 实例化异步任务类实现注册异步任务钩子 */
new AsyncEmail();