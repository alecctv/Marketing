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
 * Class WP_REST_BoughtResource_Controller
 */
class WP_REST_BoughtResource_Controller extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'v1';
        $this->rest_base = 'users/boughtresources';
    }

    /**
     * 注册路由
     */
    public function register_routes(){

        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
    }


    /**
     * 判断当前请求是否有权限创建购买文章内容
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return boolean | WP_Error
     */
    public function create_item_permissions_check( $request ) {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_post_cannot_buy', __('Sorry, you cannot buy post resources without signing in.', 'tt'), array('status' => tt_rest_authorization_required_code()));
        }
        return true;
    }

    /**
     * 购买文章内容资源
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function create_item( $request ) {
        $post_id = intval($request->get_param('postId'));
        $resouce_seq = intval($request->get_param('resourceSeq'));
        $is_new_type = boolval($request->get_param('newType'));
        $result = tt_bought_post_resource($post_id, $resouce_seq, $is_new_type);
        if($result instanceof WP_Error) {
            return $result;
        }
        if ($is_new_type) {
            return $result;
        }
        return tt_api_success(__('Buy the resource successfully', 'tt'), array('data' => $result));
    }
}