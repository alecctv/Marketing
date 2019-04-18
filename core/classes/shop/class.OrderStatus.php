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
 * Class OrderStatus
 *
 * 定义order的status enum
 */
final class OrderStatus {

    const DEFAULT_STATUS = 0;

    const WAIT_PAYMENT = 1;

    const PAYED_AND_WAIT_DELIVERY = 2;

    const DELIVERED_AND_WAIT_CONFIRM = 3;

    const TRADE_SUCCESS = 4;

    const TRADE_CLOSED = 9;
}