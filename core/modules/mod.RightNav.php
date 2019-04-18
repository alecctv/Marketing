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
<div class="sidenav">
    <?php $user = wp_get_current_user(); ?>
        <?php if($user && $user->ID) { ?>
        <?php $unread = tt_count_pm_cached($user->ID, 0, MsgReadStatus::UNREAD); ?>
            <?php if(current_user_can('edit_users')) { ?>
                <!-- 显示站务管理 -->
                <div id="nav_right" class="widget isdashboard">
                     <ul id="right-primary">
                        <li class=""><a target="_blank" href="<?php echo get_dashboard_url(); ?>">WP后台管理</a></li>
                        <li class=""><a target="_blank" href="<?php echo tt_url_for('manage_home'); ?>">站务管理</a></li>
                    </ul>
                </div>
            <?php } ?>
        <?php } ?>
    
    <!-- 显示Wp后台设置的菜单 -->
    <div id="nav_right" class="widget">
        <h2 class="widgettitle">快速导航</h2>
        <?php wp_nav_menu( array( 'theme_location' => 'shop-menu', 'container' => '', 'menu_id'=> 'right-primary', 'menu_class' => 'product-categories', 'depth' => '1', 'fallback_cb' => false  ) ); ?>
    </div>

    <!-- 只在商城类型显示购物车组件 -->
    <?php if(is_single() && get_post_type()=='product' ){?>
    <?php $rating_vm = ShopLatestRatedVM::getInstance(); $rated_products = $rating_vm->modelData; ?>
    <?php global $cart_items; if(!$cart_items)$cart_items = tt_get_cart(); ?>
    <?php $display_cart = $cart_items && count($cart_items) > 0; ?>
    <div id="nav_widget" class="widget widget_nav_menu">
    <aside class="commerce-widget shopcart widget_shopping_cart <?php if($display_cart) echo 'active'; ?>">
        <h2 class="widgettitle"><?php _e('CART', 'tt'); ?></h2>
        <ul class="widget-content widget_shopping_cart-list">
            <?php $total = 0; foreach ($cart_items as $cart_item) { $cart_item_min_price = tt_get_specified_user_product_price($cart_item['id'], get_current_user_id()); $total += $cart_item_min_price * $cart_item['quantity'] ?>
                <li class="cart-item" data-product-id="<?php echo $cart_item['id']; ?>">
                    <a href="<?php echo $cart_item['permalink']; ?>" title="<?php echo $cart_item['name']; ?>">
                        <img class="thumbnail" src="<?php echo $cart_item['thumb']; ?>">
                        <span class="product-title"><?php echo $cart_item['name']; ?></span>
                    </a>
                    <div class="price"><i class="tico tico-cny"></i><?php echo $cart_item_min_price . ' x ' . $cart_item['quantity']; ?></div>
                    <i class="tico tico-close delete"></i>
                </li>
            <?php } ?>
            <div class="cart-amount"><?php _e('TOTAL: ', 'tt'); ?><i class="tico tico-cny"></i><span><?php echo $total; ?></span></div>
        </ul>
        <div class="cart-actions">
            <a class="btn btn-border-success cart-act check-act" href="javascript:;"><?php _e('Check Out Now', 'tt'); ?></a>
            <a class="btn btn-border-danger cart-act clear-act" href="javascript:;"><?php _e('Clear All', 'tt'); ?></a>
        </div>
    </aside>
    </div>
    <?php } ?>

    <!-- 最近浏览商品列表 -->
    <div id="nav_widget" class="widget widget_nav_menu">
        <!-- User view history -->
        <?php $view_vm = ShopViewedHistoryVM::getInstance(get_current_user_id()); $view_products = $view_vm->modelData; ?>
        <?php if(count($view_products)) { ?>
        <aside class="commerce-widget widget_product_view_history">
            <h2 class="widgettitle">浏览历史</h2>
            <ul class="widget-content view_product-list">
                <?php foreach ($view_products as $view_product) { ?>
                    <li>
                        <a href="<?php echo $view_product['permalink']; ?>" title="<?php echo $view_product['title']; ?>">
                            <img class="thumbnail" src="<?php echo $view_product['thumb']; ?>">
                            <span class="product-title"><?php echo $view_product['title']; ?></span>
                        </a>
                        <?php if(!($view_product['price'] > 0)) { ?>
                            <div class="price price-free"><?php _e('FREE', 'tt'); ?></div>
                        <?php }elseif(!isset($view_product['discount'][0]) || $view_product['min_price'] >= $view_product['price']){ ?>
                            <div class="price"><?php echo $view_product['price_icon']; ?><?php echo $view_product['price']; ?></div>
                        <?php }else{ ?>
                            <div class="price">
                            <del><span class="price original-price"><?php echo $view_product['price_icon']; ?><?php echo $view_product['price']; ?></span></del>
                            <?php echo $view_product['price_icon']; ?><ins><span class="price discount-price"><?php echo $view_product['min_price']; ?></span></ins>
                            </div>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </aside>
        <?php } ?>
    </div>
</div>
<!-- End -->