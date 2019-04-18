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
<?php global $productdata; ?>
<?php $rating_vm = ShopLatestRatedVM::getInstance(); $rated_products = $rating_vm->modelData; ?>
<?php global $cart_items; if(!$cart_items)$cart_items = tt_get_cart(); ?>
<!-- Shopping cart -->
<?php $display_cart = $cart_items && count($cart_items) > 0; ?>

<!-- 作者 -->
<aside class="product-info__section product-info__section--author">
    <a href="<?php echo $productdata->author_url; ?>"><img src="<?php echo tt_get_avatar(get_the_author_ID(), 'large'); ?>" width="80" height="80" alt="themestall" class="avatar wp-user-avatar"></a><strong><?php echo $productdata->author; ?></strong>
</aside>

<!-- 价格 -->
<aside class="product-info__section product-info__section--price">
    <span class="price">
        <?php if(!($productdata->price > 0)) { ?>
            <span class="price price-free">免费</span>
        <?php }elseif(!isset($productdata->discount[0]) || $productdata->min_price >= $productdata->price){ ?>
            <span class="price"><?php echo $productdata->price_icon; ?><?php echo $productdata->price; ?></span>
        <?php }else{ ?>
            <span style="font-size:18px;"><del style="color: #666;"><?php echo $productdata->price_icon; ?><?php echo $productdata->price; ?></del>
            <ins>   <?php echo $productdata->price_icon; ?><?php echo $productdata->min_price; ?></ins></span>
        <?php } ?>
    </span>
</aside>

<!-- 销量 -->
<aside class="product-info__section product-info__table">
    <h2>商品编号：<?php echo $productdata->ID; ?></h2>
    <table><tr><td>销量：<?php if($productdata->sales > 0) : echo $productdata->sales; else : echo '0'; endif; ?></td>
    <td>库存：<?php echo $productdata->amount; ?></td></tr></table>
</aside>

<!-- 评分 -->
<aside class="product-info__section product-info__section--rating">
    <?php $rating = $productdata->rating; ?>
    <h2>平均评级</h2>
    <div class="stars">
        <div class="star-rating tico-star-o" >
            <span class="tico-star" style="<?php echo sprintf('width:%d', $rating['percent']) . '%;'; ?>"> </span>
        </div>
    </div>
    <strong><span title="<?php printf(__('Rated %0.1f out of 5', 'tt'), $rating['value']); ?>" >基于<?php echo $rating['count'] ?>个用户评价</span></strong>
</aside>

<?php global $productdata; $catIDs = $productdata->catIDs; $rand_products = $productdata->rands; ?>
<?php $tool_vm = ShopHeaderSubNavVM::getInstance(); $data = $tool_vm->modelData; $all_categories = $data->categories; $all_tags = $data->tags;?>


<!-- 分类 -->
<aside class="product-info__section product-info__section--cat">
    <h2>商品分类</h2>
    <ul class="shop-leftwidget-category">
        
        <?php foreach ($all_categories as $category) { ?>
            <li><a class="product-cat cat-link" href="<?php echo $category['permalink']; ?>" title=""><?php echo $category['name']; ?></a></li>
        <?php } ?>
    </ul>
</aside>

<!-- 标签 -->
<aside class="product-info__section product-info__section--tags">
    <h2>商品标签</h2>
    <?php foreach ($all_tags as $tag) { ?>
            <a class="product-tag tag-link" href="<?php echo $tag['permalink']; ?>" title=""><?php echo $tag['name']; ?></a>
        <?php } ?>
</aside>

<!-- 分享 -->
<div class="product-info__section product-info__section--social">
    <h2 style=" margin-bottom: 15px; margin-top: -10px; "><?php printf(__('人气： %d', 'tt'), $productdata->views); ?></h2>
    <div class="share">
        <div class="share-bar">
            <a class="share-btn share-weibo" href="<?php echo 'http://service.weibo.com/share/share.php?url=' . $productdata->permalink . '&count=1&title=' . $productdata->title . ' - ' . get_bloginfo('name') . '&pic=' . urlencode($productdata->thumb) . '&appkey=' . tt_get_option('tt_weibo_openkey'); ?>" title="<?php _e('Share to Weibo', 'tt'); ?>" target="_blank"></a>
            <a class="share-btn share-qzone" href="<?php echo 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $productdata->permalink . '&summary=' . $productdata->excerpt . '&title=' . $productdata->title . ' - ' . get_bloginfo('name') . '&site=' . get_bloginfo('name') . '&pics=' . urlencode($productdata->thumb); ?>" title="<?php _e('Share to QZone', 'tt'); ?>" target="_blank"></a>
            <a class="share-btn share-qq" href="<?php echo 'http://connect.qq.com/widget/shareqq/index.html?url=' . $productdata->permalink . '&title=' . $productdata->title . ' - ' . get_bloginfo('name') . '&summary=' . $productdata->excerpt . '&pics=' . urlencode($productdata->thumb) . '&flash=&site=' . get_bloginfo('name') . '&desc='; ?>" title="<?php _e('Share to QQ', 'tt'); ?>" target="_blank"></a>
            <a class="share-btn share-weixin" href="javascript: void(0)" data-trigger="focus" data-toggle="popover" data-placement="top" data-container="body" data-content='<?php echo '<img width=120 height=120 src="' . tt_qrcode($productdata->permalink, 120) . '">'; ?>' data-html="1" title="<?php _e('Share to Wechat', 'tt'); ?>" target="_blank"></a>
        </div>
    </div>
</div>