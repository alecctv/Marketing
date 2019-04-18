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
 * Class WP_REST_Card_Controller
 */
class WP_REST_Card_Controller extends WP_REST_Controller
{
	public function __construct()
	{
		$this->namespace = 'v1';
		$this->rest_base = 'cards';
	}

	/**
	 * 注册路由
	 */
	public function register_routes(){

		register_rest_route($this->namespace, '/' . $this->rest_base, array(
//			array(
//				'methods' => WP_REST_Server::READABLE,
//				'callback' => array($this, 'get_items'),
//				'permission_callback' => array($this, 'get_items_permissions_check'),
//				'args' => array(
//					'context' => $this->get_context_param(array('default' => 'view')),
//				),
//			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'create_items'),
				'permission_callback' => array($this, 'create_items_permissions_check'),
				'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
			),
			'schema' => array($this, 'get_public_item_schema'),
		));

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
//			array(
//				'methods'         => WP_REST_Server::READABLE,
//				'callback'        => array( $this, 'get_item' ),
//				'permission_callback' => array( $this, 'get_item_permissions_check' ),
//				'args'            => array(
//					'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
//				),
//			),
//			array(
//				'methods'         => WP_REST_Server::EDITABLE,
//				'callback'        => array( $this, 'update_item' ),
//				'permission_callback' => array( $this, 'update_item_permissions_check' ),
//				'args'            => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
//			),
			array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args' => array(
					'force'    => array(
						'default'     => false,
						'description' => __( 'Required to be true, as resource does not support trashing.' ),
					),
					'reassign' => array(),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}


	/**
	 * 判断请求是否有创建卡的权限
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean | WP_Error
	 */
	public function create_items_permissions_check($request)
	{
		if (!current_user_can('administrator')) {
			return new WP_Error('rest_card_cannot_create', __('Sorry, you are not permitted to create cards.', 'tt'), array('status' => tt_rest_authorization_required_code()));
		}
		return true;
	}


	/**
	 * 创建多个卡
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_items($request)
	{
		$quantity = (int)max(1, min($request->get_param('quantity'), 100));
		$denomination = (int)max(1, $request->get_param('denomination'));

		$gen = tt_gen_cards($quantity, $denomination);

		if($gen instanceof WP_Error) {
			return $gen;
		}elseif(!$gen) {
			return tt_api_fail(__('Generate cards failed', 'tt'), array(), 400);
		}

		return tt_api_success(__('Gen cards successfully', 'tt'), array('cards' => $gen));
	}


	/**
	 * 检查请求是否有删除指定卡的权限
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean | WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		if (!current_user_can('administrator')) {
			return new WP_Error('rest_card_cannot_delete', __('Sorry, you are not permitted to delete a card.', 'tt'), array('status' => tt_rest_authorization_required_code()));
		}
		return true;
	}

	/**
	 * 删除单个卡
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$id = (int) $request['id'];

		$result = tt_delete_card($id);
		if(!$result) {
			return new WP_Error( 'rest_cannot_delete', __( 'The card cannot be deleted.', 'tt' ), array( 'status' => 500 ) );
		}

		return tt_api_success(__('delete card successfully', 'tt'), array('card_id' => $id));
	}
}
