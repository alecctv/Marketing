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
<?php global $origin_post;global $current_user; get_currentuserinfo(); ?>
<?php
    $free_dls = trim(get_post_meta($origin_post->ID, 'tt_free_dl', true));
    $free_dls = !empty($free_dls) ? explode(',', str_replace(PHP_EOL, ',', $free_dls)) : array();
    $sale_dls = trim(get_post_meta($origin_post->ID, 'tt_sale_dl', true));
    $sale_dls = !empty($sale_dls) ? explode(',', str_replace(PHP_EOL, ',', $sale_dls)) : array();
    $sale_dls2 = tt_get_post_sale_resources($origin_post->ID);
	$member = new Member($current_user->ID);
?>
<div id="main" class="main primary col-md-9 download-box" role="main">
 <nav class="kuacg-breadcrumb">
                        <a href="<?php echo home_url(); ?>"><i class="tico tico-home"></i><?php _e('HOME', 'tt'); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
						<?php echo get_the_category_list(' ', '', $origin_post->ID); ?>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        <a href="<?php echo get_permalink($origin_post); ?>"><?php echo get_the_title($origin_post); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        资源下载
    </nav>
    <div class="download">
        <div class="dl-declaration contextual-callout callout-warning">
            <p>本站所刊载内容均为网络上收集整理，包括但不限于代码、应用程序、影音资源、电子书籍资料等，并且以研究交流为目的，所有仅供大家参考、学习，不存在任何商业目的与商业用途。若您使用开源的软件代码，请遵守相应的开源许可规范和精神，
              若您需要使用非免费的软件或服务，您应当购买正版授权并合法使用。如果你下载此文件，表示您同意只将此文件用于参考、学习使用而非任何其他用途。</p><br/>
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;如果下载本资源积分不足，可登录后在这里<strong><a href="/me/credits" title="充值积分" rel="link" target="_blank"> 充值积分 </a>.</strong></p> 
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;当前积分兑换比率为：<strong><?php printf(__('100 积分 = %d 元', 'tt'), tt_get_option('tt_hundred_credit_price', 1)); ?></strong></p>
          <?php if(is_user_logged_in()) { ?>
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;也可以在这里通过推广连接免费获取积分.</p> 
          <p><i class="tico tico-bullhorn2"></i>&nbsp;&nbsp;你的专属推广链接：【&nbsp;<strong><?php echo home_url('?ref=') . $current_user->ID . "\n";?></strong>&nbsp;】</p>
          <?php } ?>
        </div>
        <?php load_mod(('banners/bn.Download.Top')); ?>
        <div class="dl-detail">
        <?php if(count($free_dls)) { ?>
            <h2><?php _e('Free Resources', 'tt'); ?></h2>
            <?php if (!is_user_logged_in() && !tt_get_option('tt_enable_k_nologindownload', true)) { ?>
            <p>此免费资源需要<a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>"><i class="tico tico-sign-in"></i>登录</a>才能查看</p>
            <?php } else { ?>
            <ul class="free-resources">
            <?php $seq = 0; foreach ($free_dls as $free_dl) { ?>
                <?php $free_dl = explode('|', $free_dl); ?>
                <?php if(count($free_dl) < 2) {continue;}else{ $seq++; ?>
                <li>
                    <?php echo sprintf(__('%d. %2$s <a href="%3$s" target="_blank"><i class="tico tico-cloud-download"></i>点击下载</a> (密码: %4$s)', 'tt'), $seq, $free_dl[0], $free_dl[1], isset($free_dl[2]) ? $free_dl[2] : __('None', 'tt')); ?>
                </li>
                <?php } ?>
            <?php } ?>
            </ul>
            <?php } ?>
        <?php } ?>
        <?php if(!count($sale_dls2) && count($sale_dls)) { ?>
            <h2><?php _e('Sale Resources', 'tt'); ?></h2>
            <?php if (is_user_logged_in()) { ?>
                <ul class="sale-resources">
                    <?php $seq = 0; foreach ($sale_dls as $sale_dl) { ?>
                        <?php $sale_dl = explode('|', $sale_dl); ?>
                        <?php if(count($sale_dl) < 2) {continue;}else{ $seq++; ?>
                            <li>
                                <?php if(tt_check_bought_post_resources($origin_post->ID, $seq)) { ?>
                                    <?php echo sprintf(__('%d. %2$s <a href="%3$s" target="_blank"><i class="tico tico-cloud-download"></i>点击下载</a> (密码: %4$s)', 'tt'), $seq, $sale_dl[0], $sale_dl[1], isset($sale_dl[3]) ? $sale_dl[3] : __('None', 'tt')); ?>
                                <?php }else{ ?>
                                    <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (%4$s Credits)', 'tt'), $seq, $sale_dl[0], $origin_post->ID, isset($sale_dl[2]) ? $sale_dl[2] : 1); ?>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p>此付费资源需要<a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>"><i class="tico tico-sign-in"></i>登录</a>才能查看</p>
            <?php } ?>
        <?php } ?>
        <?php if(count($sale_dls2)) { ?>
            <h2><?php _e('Sale Resources', 'tt'); ?></h2>
            <?php if (is_user_logged_in()) { ?>
                <ul class="sale-resources">
                    <?php foreach ($sale_dls2 as $sale_dl) { ?>
                        <li>
                            <!-- 资源名称|资源下载url1_密码1,资源下载url2_密码2|资源价格|币种 -->
                            <?php if(tt_check_bought_post_resources2($origin_post->ID, $sale_dl['seq'])) { ?>
                                <?php echo sprintf(__('%d. %2$s ', 'tt'), $sale_dl['seq'], $sale_dl['name']); ?>
                                <?php $pans = $sale_dl['downloads'];
                                $pan_seq = 0;
                                foreach ($pans as $pan) {
                                    $pan_seq++;
                                    echo sprintf(__('<a href="%1$s" target="_blank"><i class="tico tico-cloud-download"></i>下载地址%2$d</a> (密码: %3$s)', 'tt'), $pan['url'], $pan_seq, isset($pan['password']) ? $pan['password'] : __('None', 'tt'));
                                }
                                ?>
                            <?php }else{ ?>
                                <?php if($member->vip_type == 1) { ?>
                                <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource2" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (会员专享价：%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, intval($sale_dl['price'] * tt_get_option('tt_monthly_vip_discount') / 100), $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                            <?php }elseif($member->vip_type == 2) { ?>
                                <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource2" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (会员专享价：%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, intval($sale_dl['price'] * tt_get_option('tt_annual_vip_discount') / 100), $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                            <?php }elseif($member->vip_type == 3) { ?>
                                <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource2" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (会员专享价：%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, intval($sale_dl['price'] * tt_get_option('tt_permanent_vip_discount') / 100), $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                            <?php }else{ ?>
                            <?php echo sprintf(__('%1$s. %2$s <a class="buy-resource2" href="javascript:;" data-post-id="%3$s" data-resource-seq="%1$s" target="_blank"><i class="tico tico-cart"></i>点击购买</a> (%4$s %5$s)', 'tt'), $sale_dl['seq'], $sale_dl['name'], $origin_post->ID, $sale_dl['price'], $sale_dl['currency'] == 'cash' ? '元' : '积分'); ?>
                          <?php } ?>
						  <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p>此付费资源需要<a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>"><i class="tico tico-sign-in"></i>登录</a>才能查看</p>
            <?php } ?>
        <?php } ?>
        </div>
        <div class="tt-gg"></div>
        <div class="dl-help contextual-bg bg-info">
            <p><?php _e('如果您发现本文件已经失效不能下载，请联系站长修正！', 'tt'); ?></p>
            <p><?php _e('本站提供的资源多数为百度网盘下载，对于大文件，你需要安装百度云客户端才能下载！', 'tt'); ?></p>
            <p><?php _e('部分文件引用的官方或者非网盘类他站下载链接，你可能需要使用迅雷、BT等下载工具下载！', 'tt'); ?></p>
            <p><?php _e('本站推荐的资源均经由站长检测或者个人发布，不包含恶意软件病毒代码等，如果你发现此类问题，请向站长举报！', 'tt'); ?></p>
            <p><?php _e('本站仅提供文件的免费下载服务，如果你对代码程序软件的使用有任何疑惑，请留意相关网站论坛。对于本站个人发布的资源，站长会提供有限的帮助！', 'tt'); ?></p>
        </div>
    </div>
    <?php load_mod(('banners/bn.Download.Bottom')); ?>
</div>