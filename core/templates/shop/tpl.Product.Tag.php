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
<?php $tag = get_queried_object(); ?>
<?php $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1; ?>
<?php tt_get_header('shop'); ?>

<section id="shop-hero">
    <div class="hero hero--animate" style="height: 450px"> 
        <div class="hero__image" style="background-image: url('<?php echo tt_get_option('tt_shop_hero_bg');?>');"> 
            <div class="hero__image__overlay"></div>
        </div>
        <div class="hero__inner"> 
            <div class="hero__content"> 
                <h1><i class="tico tico-price-tag"></i> <?php echo $tag->name; ?></h1>
                <h2><?php echo $tag->description; ?></h2>
            </div>
        </div>
    </div> 
</section>

<section id="shop-postmode" class="wrapper container">
    <?php $vm = ShopTagVM::getInstance($paged, $tag->term_id); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Products tag cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <div class="content shop-content">
        <?php if($data = $vm->modelData) { $pagination_args = $data->pagination; $products = $data->products; ?>
        <div class="post-card">
            <ul class="products columns-4 wow bounceInUp">
                <?php foreach ($products as $key=>$product) { ?>
                    <li class="col product" id="<?php echo 'product-' . $product['ID']; ?>">
                        <div class="product__image">
                            <a href="<?php echo $product['permalink']; ?>" class="focus"><img width="100%" height="auto" src="<?php echo $product['thumb']; ?>" class="thumb thumb-medium wp-post-image fadeIn" alt="<?php echo $product['title']; ?>" style="display:inline"></a>
                        </div>
                        <h3><a href="<?php echo $product['permalink']; ?>"><?php echo $product['title']; ?></a></h3>
                        <span class="price">
                            <?php if(!($product['price'] > 0)) { ?>
                                    <span class="price-count">免费</span>
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
                <?php } ?>
            </ul>
        </div> 

        <?php if($pagination_args['max_num_pages'] > 1) { ?>
        <div class="pagination-new">
          <?php tt_pagination(str_replace('999999999', '%#%', get_pagenum_link(999999999)), $pagination_args['current_page'], $pagination_args['max_num_pages']); ?>
        </div>
        <?php } ?>
        <?php } ?>
    </div>
</section>

<?php tt_get_footer(); ?>
