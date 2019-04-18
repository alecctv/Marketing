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

if(!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
    wp_die(__('The request is not allowed', 'tt'), __('Illegal request', 'tt'));
}

if(!isset($_REQUEST['text'])) {
    wp_die(__('The text parameter is missing', 'tt'), __('Missing argument', 'tt'));
}

$text = trim($_REQUEST['text']);

load_class('class.QRcode');

QRcode::png($text);