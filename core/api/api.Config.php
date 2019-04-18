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
 * 更改API路由主前缀
 *
 * @since   2.0.0
 *
 * @return  string
 */
function tt_set_rest_api_prefix(){
    return 'api';
}
add_filter('rest_url_prefix', 'tt_set_rest_api_prefix');


require_once THEME_API . '/api.Compatibility.php';
require_once THEME_API . '/api.Utils.php';

// v1 api
require_once THEME_API . '/v1/endpoints/class.Rest.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.User.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Session.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Comment.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Comment.Star.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Post.Star.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Follow.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Message.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.UserStatus.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Usermeta.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.ShoppingCart.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Order.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.BoughtResource.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.UserProfile.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Action.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Post.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Product.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Coupon.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Member.Controller.php';
require_once THEME_API . '/v1/endpoints/class.Rest.Card.Controller.php';