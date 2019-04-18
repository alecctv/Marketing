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

<?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>

<?php $vm = ShopHomeVM::getInstance($paged, isset($_GET['sort']) ? $_GET['sort'] : 'latest', isset($_GET['type']) ? $_GET['type'] : 'all'); ?>


<?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $products = $data->products; ?>
<div class="post-card">
    <ul class="products columns-4">
    	<?php foreach ($products as $key=>$product) { ?>
	        <li class="col product" id="<?php echo 'product-' . $product['ID']; ?>">
	            <div class="product__image">
	                <a href="<?php echo $product['permalink']; ?>" class="focus"><img width="100%" height="auto" src="<?php echo LAZY_PENDING_IMAGE; ?>" data-original="<?php echo $product['thumb']; ?>" class="thumb thumb-medium wp-post-image lazy fadeIn" alt="<?php echo $product['title']; ?>" style="display:inline"></a>
	            </div>
	            <h3><a href="<?php echo $product['permalink']; ?>"><?php echo $product['title']; ?></a></h3>
	            <span class="price">
	            	<?php if(!($product['price'] > 0)) { ?>
                            <span class="price-count"><?php echo __('FREE', 'tt'); ?></span>
                        <?php }elseif(!isset($product['discount'][0]) || $product['min_price'] >= $product['price']){ ?>
                            
                        <span class="price-count"><?php echo $product['price_icon']; ?> <?php echo $product['price']; ?></span>
                        <?php }else{ ?>
                            <span class="price-count">
                                <del><?php echo $product['price_icon']; ?><?php echo $product['price']; ?></del>
                                <?php echo $product['price_icon']; ?><ins><?php echo $product['min_price']; ?></span></ins>
                            </span>
                        <?php } ?>
	            </span>
	            <small class="wcvendors_sold_by_in_loop"> 
                    <span class="wcvendors-profile-image" style="background-image: url('<?php echo tt_get_avatar($product['authorid'], 'small'); ?>');"></span><?php echo $product['author']; ?></small>
	        </li>
	        <?php  $postlimt=intval(tt_get_option('tt_home_products_num'))-1; ?>
	        <?php if($key == $postlimt){ break; }?>
        <?php } ?>
    </ul>
</div> 
<?php } ?>