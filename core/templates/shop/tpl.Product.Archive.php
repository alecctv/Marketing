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
<?php tt_get_header('shop'); ?>
<?php if(is_archive() && !tt_is_product_category() && !tt_is_product_tag()) { ?>
<!-- Secondary navbar -->
<?php $vm = ShopHeaderSubNavVM::getInstance(); $data = $vm->modelData; ?>



<section id="shop-hero">
    <div class="hero hero--animate" style="height: 450px"> 
        <div class="hero__image" style="background-image: url('<?php echo tt_get_option('tt_shop_hero_bg');?>');"> 
            <div class="hero__image__overlay"></div>
        </div>
        <div class="hero__inner"> 
            <div class="hero__content"> 
                <h1><?php echo tt_get_option('tt_shop_title');?></h1> 
                <h2><?php echo tt_get_option('tt_shop_sub_title');?></h2>
            </div>
        </div>
    </div> 
</section>


<section id="shop-nav" class="wrapper container">
    <div class="secondary-navbar">
        <div class="secondary-navbar-inner clearfix">
            <ul class="secondary-navbar_list-items secondary-navbar_list-items--left clearfix">
                <!-- Categories -->
                <?php $categories = $data->categories; ?>
                <li class="secondary-navbar_list-item secondary-navbar_list-item--filter category-filter">
                    <a href="javascript:;">分类</a>
                    <ul>
                        <?php foreach ($categories as $category) { ?>
                            <li><a href="<?php echo $category['permalink']; ?>"><strong><?php echo $category['name']; ?></strong> (<?php echo $category['count']; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <!-- Price -->
                <?php $price_types = $data->price_types; ?>
                <li class="secondary-navbar_list-item secondary-navbar_list-item--filter price-filter">
                    <a href="javascript:;">价格</a>
                    <ul>
                        <?php foreach ($price_types as $price_type) { ?>
                            <li><a href="<?php echo $price_type['url']; ?>"><strong><?php echo $price_type['name']; ?></strong> (<?php echo $price_type['count']; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <!-- Tags -->
                <?php $tags = $data->tags; ?>
                <li class="secondary-navbar_list-item secondary-navbar_list-item--filter tag-filter">
                    <a href="javascript:;">标签</a>
                    <ul>
                        <?php foreach ($tags as $tag) { ?>
                            <li><a href="<?php echo $tag['permalink']; ?>"><strong><?php echo $tag['name']; ?></strong> (<?php echo $tag['count']; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="serach-form">
                    <div class="secondary-navbar_list-item header-search">
                        <form method="get" action="/">
                            <input autocomplete="off" class="header_search-input" placeholder="搜索点什么..." spellcheck="false" name="s" type="text" value="">
                            <input type="hidden" name="in_shop" value="1">
                        </form>
                    </div>
                </li>
            </ul>

            <ul class="secondary-navbar_list-items secondary-navbar_list-items--right clearfix">
                <li class="<?php echo tt_conditional_class('secondary-navbar_list-item secondary-navbar_list-item--sort', !isset($_GET['sort']) || $_GET['sort']=='popular'); ?>">
                    <a href="<?php echo add_query_arg(array('sort' => 'popular'), Utils::getPHPCurrentUrl()/*tt_url_for('shop_archive')*/); ?>">热门</a>
                </li>
                <li class="<?php echo tt_conditional_class('secondary-navbar_list-item secondary-navbar_list-item--sort', isset($_GET['sort']) && $_GET['sort']=='latest'); ?>">
                    <a href="<?php echo add_query_arg(array('sort' => 'latest'), Utils::getPHPCurrentUrl()/*tt_url_for('shop_archive')*/); ?>">最新上架</a>
                </li>
            </ul>
        </div>
    </div>
</section>
<?php } ?>

<section id="shop-postmode" class="wrapper container">
    <?php $vm = ShopHomeVM::getInstance($paged, isset($_GET['sort']) ? $_GET['sort'] : 'latest', isset($_GET['type']) ? $_GET['type'] : 'all'); ?>
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