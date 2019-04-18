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
<?php global $tt_me_vars;
$tt_user_id = $tt_me_vars['tt_user_id'];
$tt_page = $tt_me_vars['tt_paged']; ?>
<div class="col col-md-10 col-right cash">
    <?php $vm = MeCashRecordsVM::getInstance($tt_user_id, $tt_page); ?>
    <?php if ($vm->isCache && $vm->cacheTime) { ?>
        <!-- User cash info cached <?php echo $vm->cacheTime; ?> -->
    <?php

} ?>
    <?php $data = $vm->modelData;
    $records = $data->records;
    $max_pages = $data->max_pages; ?>
    <div class="me-tab-box cash-tab">
        <div class="tab-content me-cash">
            <!-- 账户现金信息 -->
            <section class="cash-info clearfix">
                <header><h2><?php _e('My Cash', 'tt'); ?></h2></header>
                <div class="info-group clearfix">
                    <div class="row clearfix">
                        <label class="col-md-3 control-label"><?php _e('Cash Balance', 'tt'); ?></label>
                        <div class="col-md-9"><p class="form-control-static"><?php echo tt_get_user_cash($tt_user_id); ?></p></div>
                    </div>
                    <div class="row clearfix">
                        <label class="col-md-3 control-label"><?php _e('Cash Consumed', 'tt'); ?></label>
                        <div class="col-md-9"><p class="form-control-static"><?php echo tt_get_user_consumed_cash($tt_user_id); ?></p></div>
                    </div>
                </div>
            </section>
            <!-- 现金充值 -->
            <section class="cash-charge clearfix">
                <header><h2><?php _e('Cash Charge', 'tt'); ?></h2></header>
                <div class="info-group clearfix">
                    <div class="form-group charge-cash-form">
                        <label><?php _e('当前只接受充值卡充值', 'tt'); ?></label>
                        <div class="form-inline">
                            <div class="form-group">
                                <div class="input-group active">
                                    <div class="input-group-addon"><?php _e('Card No', 'tt'); ?></div>
                                    <input class="form-control" type="text" name="card-id" value="" aria-required="true" required="">
                                </div>
                                <div class="input-group active">
                                    <div class="input-group-addon"><?php _e('Card Secret', 'tt'); ?></div>
                                    <input class="form-control" type="text" name="card-secret" value="" aria-required="true" required="">
                                </div>
                            </div>
                            <button class="btn btn-inverse" type="submit" id="apply-card"><?php _e('APPLY', 'tt'); ?></button>
                        </div>
                        <p class="help-block"><?php _e('充值卡来源请关注网站说明，一般由站长在发卡平台发售', 'tt'); ?></p>
                    </div>
                </div>
            </section>
            <!-- 现金变动记录 -->
            <section class="cash-records clearfix">
                <header><h2><?php _e('Cash Records', 'tt'); ?></h2></header>
                <div class="info-group clearfix">
                    <ul class="records-list">
                    <?php foreach ($records as $record) { ?>
                        <li id="record-<?php echo $record->msg_id; ?>"><?php echo $record->msg_date; ?><span class="record-text"><?php echo $record->msg_title; ?></span></li>
                    <?php

                } ?>
                    </ul>
                    <?php if ($max_pages > 1) { ?>
                        <div class="pagination-mini clearfix">
                            <?php if ($tt_page == 1) { ?>
                                <div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php

                        }
                        else { ?>
                                <div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
                            <?php

                        } ?>
                            <div class="col-md-6 page-nums">
                                <span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_page); ?></span>
                                <span class="separator">/</span>
                                <span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
                            </div>
                            <?php if ($tt_page != $data->max_pages) { ?>
                                <div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php

                        }
                        else { ?>
                                <div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
                            <?php

                        } ?>
                        </div>
                    <?php

                } ?>
                </div>
            </section>
        </div>
    </div>
</div>