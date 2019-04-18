<?php
/**
 * Copyright (c) 2016-2018, WebApproach.net
 * All right reserved.
 *
 * @since 2.5.0
 *
 * @author Zhiyan
 * @date 2018/1/31 18:05
 *
 * @license GPL v3 LICENSE
 * @license uri http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @see https://webapproach.net/tint.html
 */
?>
<?php

//if(!is_user_logged_in()){
//    wp_die(__('You cannot visit this page without sign in', 'tt'), __('Error: Unknown User', 'tt'), 403);
//}

if (!isset($_GET['oid'])) {
    wp_die(__('The required parameters for retrieve a order  are missing', 'tt'), __('Invalid Query Parameters', 'tt'), 500);
}

$order_id = htmlspecialchars($_GET['oid']);
$order = tt_get_order($order_id);
if (!$order) {
    wp_die(__('The order with order id you specified is not existed', 'tt'), __('Invalid Order', 'tt'), 500);
}

if (in_array($order->order_status, array(2, 3, 4))) {
    wp_die(__('The order with order id you specified has been payed', 'tt'), __('Invalid Order', 'tt'), 500);
}

$currency = $order->order_currency;

if ($currency != 'cash') {
    wp_die(__('The order does not support cash payment', 'tt'), __('Unsuitable Payment Method', 'tt'), 500);
}

$enable_helper = tt_get_option('tt_enable_youzan_helper', false);
if (!$enable_helper) {
    require_once THEME_LIB.'/youzan/YZGetTokenClient.php';
    require_once THEME_LIB.'/youzan/YZTokenClient.php';
    $client_id = tt_get_option('tt_youzan_client_id', '');
    $client_secret = tt_get_option('tt_youzan_client_secret', '');
    $token_client = new YZGetTokenClient($client_id, $client_secret);

    $type = 'self';
    $keys = array(
        'grant_type' => 'silent',
        'kdt_id' => intval(tt_get_option('tt_youzan_kdt_id', '')),
    );
    $token = $token_client->get_token($type, $keys);

    $client = new YZTokenClient($token['access_token']);

    $method = 'youzan.pay.qrcode.create'; //要调用的api名称
    $api_version = '3.0.0'; //要调用的api版本号
    $params = array(
        'qr_name' => $order->product_name,
        'qr_price' => $order->order_total_price * 100,
        'qr_type' => 'QR_TYPE_DYNAMIC',
    );

    $resp = $client->post($method, $api_version, $params);
    if (!isset($resp['response'])) {
        var_dump($resp);
    } else {
        $qr = $resp['response'];
        $qr_id = $qr['qr_id'];
        $qr_code = $qr['qr_code'];

        // 保存qr_id到订单
        tt_update_order($order->order_id, array(
            'trade_no' => $qr_id,
        ), array('%s'));
    }
}
?>
<?php tt_get_header('simple'); ?>
<body class="is-loadingApp site-page qrpay yz-qrpay">
<?php load_template(THEME_MOD.'/mod.LogoHeader.php'); ?>
<div id="content" class="wrapper container no-aside">
    <div class="main inner-wrap">
        <section class="processor">
            <ol>
                <li class="done size1of2">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">1</i>
                        <h4><?php _e('Confirm Order', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="current size1of2 no_extra">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">2</i>
                        <h4><?php _e('Accomplish Payment', 'tt'); ?></h4>
                    </div>
                </li>
                <li class="size1of2 last">
                    <div class="step_line"></div>
                    <div class="step_inner">
                        <i class="icon_step">3</i>
                        <h4><?php _e('Confirm Delivery', 'tt'); ?></h4>
                    </div>
                </li>
            </ol>
        </section>
        <section class="payment">
            <div class="payment-wrapper">
                <h1><?php echo sprintf(__('Payment Amount for Order ID %s is %0.2f', 'tt'), $order_id, $order->order_total_price); ?></h1>
                <p class="introduction"><?php _e('请使用支付宝或微信扫描下方二维码进行支付, 如果二维码无法显示请刷新页面', 'tt'); ?></p>
                <div class="pay-qr-images row">
                <?php if ($enable_helper) {
    ?>
                    <div class="qrcode yz-qrcode">
                        <div id="yzQrcode"><p><span><i class="spinning tico tico-spinner3"></i></span><?php _e('正在生成支付二维码...', 'tt'); ?></p></div>
                    </div>
                    <script>
                        var yzPayInfo = {
                            orderId: "<?php echo $order->order_id; ?>",
                            total: <?php echo sprintf('%0.0f', $order->order_total_price * 100); ?>,
                            name: "<?php echo $order->product_name; ?>"
                        }
                    </script>
                <?php
} else {
        ?>
                    <div class="qrcode yz-qrcode">
                        <div id="yzQrcode">
                        <img src="<?php echo $qr_code; ?>" /></div>
                    </div>
                <?php
    } ?>
                </div>
                <div class="actions"><a class="btn btn-success btn-wide go-order-detail" href="<?php echo tt_url_for('my_order', $order->id); ?>" target="_blank"><?php _e('Check Order Detail', 'tt'); ?></a></div>
            </div>
        </section>
    </div>
</div>
</body>
<?php tt_get_footer(); ?>