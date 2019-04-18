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
<aside class="product-info__section product-info__section--tags">
    <h2>商品分类</h2>
    <?php echo implode(', ', $cat_breads); ?>
</aside>


<!-- 分类 -->
<aside class="commerce-widget widget_product_categories">
    <h3 class="widget-title"><?php _e('Categories', 'tt'); ?></h3>
    <ul class="shop-leftwidget-category">
      <li class="tico-angle cat-item"> <a class="product-cat cat-link" href="<?php echo tt_url_for('shop_archive'); ?>" title="">全部商品</a> </li>
        <?php foreach ($all_categories as $category) { ?>
            <li class="<?php if(in_array($category['ID'], $catIDs)){echo 'tico-angle cat-item active';}else{echo 'tico-angle cat-item';}; ?>">
                <a class="product-cat cat-link" href="<?php echo $category['permalink']; ?>" title=""><?php echo $category['name']; ?></a>
            </li>
        <?php } ?>
    </ul>
</aside>

<!-- 标签 -->
<aside class="commerce-widget widget_product_tag_cloud">
    <h3 class="widget-title"><?php _e('Product Tags', 'tt'); ?></h3>
    <div class="widget-content tagcloud">
        <?php foreach ($all_tags as $tag) { ?>
            <a class="product-tag tag-link" href="<?php echo $tag['permalink']; ?>" title=""><?php echo $tag['name']; ?></a>
        <?php } ?>
    </div>
</aside>





