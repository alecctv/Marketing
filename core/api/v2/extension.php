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
 * 拦截并重载默认的基于Cookies用户认证方式，采用OAuth的Access Token认证
 *
 * @since   x.x.x
 *
 * @param   int | false    $user_id     用户ID
 * @return  int | false
 */
function tt_install_token_authentication($user_id){
    // TODO: token verify and find the user_id
    return false;
}
add_filter('determine_current_user', 'tt_install_token_authentication', 5, 1);

remove_filter( 'determine_current_user', 'wp_validate_auth_cookie' );
remove_filter( 'determine_current_user', 'wp_validate_logged_in_cookie', 20 );