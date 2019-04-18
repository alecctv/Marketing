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
<div id="main" class="main primary col-md-9 post-box" role="main">
    <?php global $post; $vm = SinglePostVM::getInstance($post->ID); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Post cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php global $postdata; $postdata = $vm->modelData; ?>
    <div class="post">
      <?php if (tt_get_option('tt_enable_k_postnews', true)) { ?>
            <div class="single-body wow bounceInUp">
			<nav class="kuacg-breadcrumb">
                        <a href="<?php echo home_url(); ?>"><i class="tico tico-home"></i><?php _e('HOME', 'tt'); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        <?php echo $postdata->category; ?>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        正文
            </nav>
            <div class="article-header">
              <h1 class="article-title"><?php echo $postdata->title; ?></h1>
               <div class="article-meta">
                  <span class="item"><i class="tico tico-alarm"></i><time class="entry-date" datetime="<?php echo $postdata->datetime; ?>" title="<?php echo $postdata->datetime; ?>" pubdate="pubdate"><?php echo $postdata->timediff; ?></time></span>
                  <span class="item">（最后更新于<?php echo $postdata->modifieddiff; ?>）</span>
                  <span class="item"><i class="tico tico-folder-open-o"></i>分类: <?php echo $postdata->category; ?></span> 
                  <span class="item"><i class="tico tico-comments-o"></i><a class="post-meta-comments js-article-comment js-article-comment-count" href="#respond">评论(<?php echo $postdata->comment_count; ?>)</a></span> 
                  <span class="item"><i class="tico tico-eye"></i> 阅读(<?php echo $postdata->views; ?>)</span> 
                  <span class="item"><i class="tico tico-thumbs-o-up"></i><a class="post-meta-likes js-article-like <?php if(in_array(get_current_user_id(), $postdata->star_uids)) echo 'active'; ?>" href="javascript: void(0)" data-post-id="<?php echo $postdata->ID; ?>" data-nonce="<?php echo wp_create_nonce('tt_post_star_nonce'); ?>"> 赞(<?php echo $postdata->stars; ?>)</a></span> 
                  <?php if (current_user_can("edit_post", $postdata->ID)) { ?>
                  <span class="item"><i class="tico tico-pencil2"></i><a class="post-edit-link" href="<?php echo $postdata->edit_link; ?>"><?php _e('Edit', 'tt'); ?></a></span> 
                  <?php } ?>
               </div>
             <div class="post-tags"><?php echo $postdata->tags; ?></div>
            </div>
            <?php }else{ ?>
             <div class="single-header" style="background-image: url(<?php echo $postdata->thumb; ?>)">
            <div class="header-wrap">
                <div class="header-meta">
                    <span class="meta-category"><?php echo $postdata->category; ?></span>
                    <span class="separator" role="separator">·</span>
                    <span class="meta-date"><time class="entry-date" datetime="<?php echo $postdata->datetime; ?>" title="<?php echo $postdata->datetime; ?>"><?php echo $postdata->timediff; ?></time></span>
                </div>
                <h1 class="h2"><?php echo $postdata->title; ?></h1>
            </div>
        </div>
        <div class="single-body">
			<nav class="kuacg-breadcrumb">
                        <a href="<?php echo home_url(); ?>"><i class="tico tico-home"></i><?php _e('HOME', 'tt'); ?></a>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        <?php echo $postdata->category; ?>
                        <span class="breadcrumb-delimeter"><i class="tico tico-arrow-right"></i></span>
                        正文
            </nav>
            <div class="article-header" style="display: table;margin-bottom: 30px;border-bottom: inherit;text-align: inherit;">
                <div class="post-tags"><?php echo $postdata->tags; ?></div>
                <div class="post-meta">
                    <?php if (current_user_can("edit_others_posts")) { ?>
                        <a class="post-edit-link" href="<?php echo $postdata->edit_link; ?>"><i class="tico tico-pencil2"></i><?php _e('Edit', 'tt'); ?></a>
                    <?php } ?>
                    <a class="post-meta-views" href="javascript: void(0)"><i class="tico tico-eye"></i><span class="num"><?php echo $postdata->views; ?></span></a>
                    <a class="post-meta-comments js-article-comment js-article-comment-count" href="#respond"><i class="tico tico-comment"></i><span class="num"><?php echo $postdata->comment_count; ?></span></a>
                    <a class="post-meta-likes js-article-like <?php if(in_array(get_current_user_id(), $postdata->star_uids)) echo 'active'; ?>" href="javascript: void(0)" data-post-id="<?php echo $postdata->ID; ?>" data-nonce="<?php echo wp_create_nonce('tt_post_star_nonce'); ?>"><i class="tico tico-favorite"></i><span class="js-article-like-count num"><?php echo $postdata->stars; ?></span></a>
                </div>
            </div>
            <?php } ?>
            <?php load_mod(('banners/bn.PostContent.Top')); ?>
            <article class="single-article">
                <?php echo $postdata->content; apply_filters('the_content', 'content'); // 一些插件(如crayon-syntax-highlighter)将非内容性的钩子(wp_enqueue_script等)挂载在the_content上, 缓存命中时将失效 ?>
                <?php if (tt_get_option('tt_enable_k_xzhid', true) && tt_get_option('tt_enable_k_xzhwzgz', true)) { ?>
                <!-- 熊掌号文章页按钮注释开始 -->
                <script>cambrian.render('tail')</script>
				<!-- 熊掌号文章页按钮注释结束 -->
                <?php } ?>
                <?php if(isset($postdata->download) && $postdata->download && !tt_get_option('tt_disable_post_btn_dw', true)) { ?>
                <!-- 相关下载 -->
                <h2 class="content-dl"><?php _e('Related Downloads', 'tt'); ?></h2>
                <p style="text-align: center;margin-bottom: 50px;text-indent:0;"><a class="btn btn-download" href="<?php echo $postdata->download; ?>" target="_blank"><?php _e('Click to Download', 'tt'); ?></a></p>
                <?php } ?>
                <?php if(isset($postdata->embed_product) && $embed_product = $postdata->embed_product) { ?>
                <!-- 内嵌商品 -->
                <div class="embed-product">
                    <img src="<?php echo $embed_product['product_thumb']; ?>">
                    <div class="product-info">
                        <h4><a href="<?php echo $embed_product['product_link']; ?>"><?php echo $embed_product['product_name']; ?></a></h4>
                        <div class="price">
                            <?php if(!($embed_product['product_price'] > 0)) { ?>
                                <span><?php echo __('FREE', 'tt'); ?></span>
                            <?php }elseif(!isset($embed_product['product_discount'][0]) || $embed_product['product_min_price'] >= $embed_product['product_price']){ ?>
                                <?php echo $embed_product['price_icon']; ?>
                                <span><?php echo $embed_product['product_price']; ?></span>
                             <?php }else{ ?>
                                <del><span class="price original-price"><?php echo $embed_product['price_icon']; ?><?php echo $embed_product['product_price']; ?></span></del>
                                <?php echo $embed_product['price_icon']; ?><ins><span class="price discount-price"><?php echo $embed_product['product_min_price']; ?></span></ins>
                            <?php } ?>
                        </div>
                        <?php $rating = $embed_product['product_rating']; ?>
                        <div class="product-rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                            <div class="star-rating tico-star-o" title="<?php printf(__('Rated %0.1f out of 5', 'tt'), $rating['value']); ?>">
                                <span class="tico-star" style="<?php echo sprintf('width:%d', $rating['percent']) . '%;'; ?>">
                                    <?php printf(__('<strong itemprop="ratingValue" class="rating">%0.1f</strong> out of <span itemprop="bestRating">5</span>based on <span itemprop="ratingCount" class="rating">%d</span> customer ratings', 'tt'), $rating['value'], $rating['count']); ?>
                                </span>
                            </div>
                            <!--a href="#reviews" class="commerce-review-link" rel="nofollow">(<?php printf(__('<span itemprop="reviewCount" class="count">%d</span> customer reviews', 'tt'), $rating['count']); ?>)</a-->
                          <div class="entry-meta">
                            <i class="tico tico-eye"></i> 阅读(<?php echo $embed_product['product_views']; ?>)次
                            <i class="tico tico-truck"></i>累计销售(<?php if($embed_product['product_sales'] > 0) : echo $embed_product['product_sales']; else : echo '0'; endif; ?>)件
                        </div>
                        </div>
                        <a class="btn btn-success btn-buy" href="<?php echo $embed_product['product_link']; ?>"><i class="tico tico-shopping-cart"></i><?php _e('Buy Now', 'tt'); ?></a>
                    </div>
                </div>
                <?php } ?>
            </article>
            <?php load_mod(('banners/bn.PostContent.Bottom')); ?>
         <div class="article-footer">
                <div class="post-copyright">
                    <p><i class="tico tico-bell-o"></i><?php echo $postdata->cc_text; ?></p>
                </div>
            <div class="footer-share-bar">
                 <div class="share-bar">
                <a class="share-btn share-weixin" href="javascript: void(0)" title="<?php _e('Share to Wechat', 'tt'); ?>" target="_blank">
                    <div class="weixin-qr transition">
                        <img src="<?php echo tt_qrcode($postdata->permalink, 120); ?>" width="120">
                    </div>
                </a>
                <a class="share-btn share-weibo" href="<?php echo 'http://service.weibo.com/share/share.php?url=' . $postdata->permalink . '&count=1&title=' . $postdata->title . ' - ' . get_bloginfo('name') . '&pic=' . urlencode($postdata->thumb) . '&appkey=' . tt_get_option('tt_weibo_openkey'); ?>" title="<?php _e('Share to Weibo', 'tt'); ?>" target="_blank"></a>
                <a class="share-btn share-qzone" href="<?php echo 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $postdata->permalink . '&summary=' . $postdata->excerpt . '&title=' . $postdata->title . ' - ' . get_bloginfo('name') . '&site=' . get_bloginfo('name') . '&pics=' . urlencode($postdata->thumb); ?>" title="<?php _e('Share to QZone', 'tt'); ?>" target="_blank"></a>
                <a class="share-btn share-qq" href="<?php echo 'http://connect.qq.com/widget/shareqq/index.html?url=' . $postdata->permalink . '&title=' . $postdata->title . ' - ' . get_bloginfo('name') . '&summary=' . $postdata->excerpt . '&pics=' . urlencode($postdata->thumb) . '&flash=&site=' . get_bloginfo('name') . '&desc='; ?>" title="<?php _e('Share to QQ', 'tt'); ?>" target="_blank"></a>
                <a class="share-btn share-douban" href="<?php echo 'https://www.douban.com/share/service?href=' . $postdata->permalink . '&name=' . $postdata->title . ' - ' . get_bloginfo('name') . '&text=' . $postdata->excerpt . '&image=' . urlencode($postdata->thumb); ?>" title="<?php _e('Share to Douban', 'tt'); ?>" target="_blank"></a>
                <a class="share-btn share-facebook" href="<?php echo 'https://www.facebook.com/sharer/sharer.php?u=' . $postdata->permalink; ?>" target="_blank"></a>
                <a class="share-btn share-twitter" href="<?php echo 'https://twitter.com/intent/tweet?url=' . $postdata->permalink . '&text=' . $postdata->title; ?>" target="_blank"></a>
                <a class="share-btn share-googleplus" href="<?php echo 'https://plus.google.com/share?url=' . $postdata->permalink; ?>" target="_blank"></a>
                <a class="share-btn share-email" href="<?php echo 'mailto:?subject=' . $postdata->title . '&body=' . $postdata->permalink; ?>" target="_blank"></a>
            </div>
        </div>
                <div class="post-like">
                    <a class="post-meta-likes js-article-like <?php if(in_array(get_current_user_id(), $postdata->star_uids)) echo 'active'; ?>" href="javascript: void(0)" data-post-id="<?php echo $postdata->ID; ?>" data-nonce="<?php echo wp_create_nonce('tt_post_star_nonce'); ?>"><i class="tico tico-favorite"></i><span class="text"><?php in_array(get_current_user_id(), $postdata->star_uids) ? _e('Stared', 'tt') : _e('Star It', 'tt'); ?></span></a>
                   
                  <ul class="post-like-avatars">
                        <?php foreach ($postdata->star_users as $star_user) { ?>
                        <li class="post-like-user"><img src="<?php echo $star_user->avatar; ?>" alt="<?php echo $star_user->name; ?>" title="<?php echo $star_user->name; ?>" data-user-id="<?php echo $star_user->uid; ?>"></li>
                        <?php } ?>
                        <li class="post-like-counter"><span><span class="js-article-like-count num"><?php echo $postdata->stars; ?></span> <?php _e('persons', 'tt'); ?></span><?php _e('Stared', 'tt'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- 上下篇导航 -->
        <div class="navigation wow bounceInUp clearfix">
            <div class="col-md-6 post-navi-prev">
                <span><?php _e('Previous article', 'tt'); ?></span>
                <h2 class="h5"><?php echo $postdata->prev; ?></h2>
            </div>
            <div class="col-md-6 post-navi-next">
                <span><?php _e('Next article', 'tt'); ?></span>
                <h2 class="h5"><?php echo $postdata->next; ?></h2>
            </div>
        </div>
        <!-- 相关文章 -->
        <?php if(count($postdata->relates) > 0) { ?>
        <?php load_mod(('banners/bn.Post.Relates.Top')); ?>
        <div class="related-posts wow bounceInUp">
            <h3><?php _e('Related Articles', 'tt'); ?></h3>
            <div class="related-articles row clearfix">
                <?php foreach ($postdata->relates as $relate) { ?>
                <article class="col-md-4 col-sm-4">
                    <div class="related-thumb">
                        <a href="<?php echo $relate['permalink']; ?>" title="<?php echo $relate['title']; ?>"><img src="<?php echo $relate['thumb']; ?>" class="thumb-medium wp-post-image" alt=""> </a>
                        <div class="entry-category"><?php echo $relate['category']; ?></div>
                    </div>
                    <div class="entry-detail">
                        <header class="entry-header">
                            <h2 class="entry-title h5"><a href="<?php echo $relate['permalink']; ?>" rel="bookmark"><?php echo $relate['title']; ?></a></h2>
                        </header>
                    </div>
                </article>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <!-- 评论 -->
        <?php load_mod(('banners/bn.Post.Comment.Top')); ?>
        <div id="respond" class="wow bounceInUp">
        <?php if($postdata->comment_status) { ?>
            <h3><?php _e('LEAVE A REPLY', 'tt'); ?></h3>
            <?php load_mod( 'mod.ReplyForm', true ); ?>
            <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
        <?php }else{ ?>
            <h3><?php _e('COMMENTS CLOSED', 'tt'); ?></h3>
            <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
        <?php } ?>
        </div>
    </div>
</div>