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
<!-- 返回顶部等固定按钮 -->
<div id="fix-controls" class="wow bounceInRight">
    <a class="scroll-to scroll-top" href="javascript:" data-tooltip="<?php _e('Scroll to top', 'tt'); ?>"><i class="tico tico-arrow-up2"></i></a>
	<?php if(is_single() && get_post_type()=='product' ){?>
    <a id="scroll-shop-pay" href="javascript:" data-tooltip="付费内容"><i class="tico tico-paypal"></i></a>
    <a id="scroll-shop-comment" href="javascript:" data-tooltip="评论"><i class="tico tico-comments-o"></i></a>
	<?php } ?>
    <a class="scroll-to scroll-bottom" href="javascript:" data-tooltip="<?php _e('Scroll to bottom', 'tt'); ?>"><i class="tico tico-arrow-down2"></i></a>
</div>

<script type="text/javascript">

$('#scroll-shop-pay').click(function(){
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $('#tab-paycontent').offset().top-80}, 400);
    return false;
});

$('#scroll-shop-comment').click(function(){
	$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $('#tab-reviews').offset().top-80}, 400);
    return false;
});

    
</script>