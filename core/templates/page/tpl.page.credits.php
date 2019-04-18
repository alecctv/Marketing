<?php
/**
 * Template Name: 积分说明
 *
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
<?php tt_get_header(); ?>
    <div id="content" class="wrapper container right-aside">
        <section id="mod-insideContent" class="main-wrap content-section clearfix">
            <!-- 页面 -->
           <div id="main" class="main primary col-md-9 post-box wow bounceInUp" role="main">
    <?php global $post; $vm = SinglePageVM::getInstance($post->ID); ?>
    <?php if($vm->isCache && $vm->cacheTime) { ?>
        <!-- Page cached <?php echo $vm->cacheTime; ?> -->
    <?php } ?>
    <?php global $postdata; $postdata = $vm->modelData; ?>
    <div class="post page">
        <div class="single-header">
            <div class="header-wrap">
                <h1 class="h2"><?php echo $postdata->title; ?></h1>
                <div class="header-meta">
                    <span class="meta-date"><?php _e('Post on: ', 'tt'); ?><time class="entry-date" datetime="<?php echo $postdata->datetime; ?>" title="<?php echo $postdata->datetime; ?>"><?php echo $postdata->timediff; ?></time></span>
                    <span class="separator" role="separator"> · </span>
                    <span class="meta-date"><?php _e('Modified on: ', 'tt'); ?><time class="entry-date" datetime="<?php echo $postdata->modified; ?>" title="<?php echo $postdata->modified; ?>"><?php echo $postdata->modifieddiff; ?></time></span>
                </div>
            </div>
        </div>
        <div class="single-body">
<!--            <div class="article-header">-->
<!--                <div class="post-tags">--><?php //echo $postdata->tags; ?><!--</div>-->
<!--                <div class="post-meta">-->
<!--                    <a class="post-meta-views" href="javascript: void(0)"><i class="tico tico-eye"></i><span class="num">--><?php //echo $postdata->views; ?><!--</span></a>-->
<!--                    <a class="post-meta-comments js-article-comment js-article-comment-count" href="#respond"><i class="tico tico-comment"></i><span class="num">--><?php //echo $postdata->comment_count; ?><!--</span></a>-->
<!--                    <a class="post-meta-likes js-article-like --><?php //if(in_array(get_current_user_id(), $postdata->star_uids)) echo 'active'; ?><!--" href="javascript: void(0)" data-post-id="--><?php //echo $postdata->ID; ?><!--" data-nonce="--><?php //echo wp_create_nonce('tt_post_star_nonce'); ?><!--"><i class="tico tico-favorite"></i><span class="js-article-like-count num">--><?php //echo $postdata->stars; ?><!--</span></a>-->
<!--                </div>-->
<!--            </div>-->
            <article class="single-article"><h2>一、如何获得积分？</h2>
用户通过在网站注册，文章投稿，评论回复，访问推广，注册推广，网站活动以及每日签到获取积分，也可以在个人中心充值积分。
<h3>正在进行的网站活动</h3>
<?php echo $postdata->content; apply_filters('the_content', 'content'); // 一些插件(如crayon-syntax-highlighter)将非内容性的钩子(wp_enqueue_script等)挂载在the_content上, 缓存命中时将失效 ?>
<section class="credits-approach clearfix"><header>
<h3>免费获取积分</h3>
</header>
<div class="info-group clearfix">
<table class="table table-centered table-framed table-striped credit-table">
<thead>
<tr>
<th>积分方法</th>
<th>一次得分</th>
<th>可用次数</th>
</tr>
</thead>
<tbody>
<tr>
<td>注册奖励</td>
<td>50 积分</td>
<td>只有 1 次</td>
</tr>
<tr>
<td>文章投稿</td>
<td>10 积分</td>
<td>每天 5 次</td>
</tr>
<tr>
<td>评论回复</td>
<td>1 积分</td>
<td>每天 10 次</td>
</tr>
<tr>
<td>访问推广</td>
<td>2 积分</td>
<td>每天 50 次</td>
</tr>
<tr>
<td>注册推广</td>
<td>5 积分</td>
<td>每天 5 次</td>
</tr>
<tr>
<td>每日签到</td>
<td>5 积分</td>
<td>每天 1 次</td>
</tr>
</tbody>
</table>
</div>
</section>
<h3>充值购买积分</h3>
<label>当前积分兑换比率为：100 积分 = 1 元</label>
<h3>你的专属推广链接</h3>
<?php if(!is_user_logged_in()) { ?>
<label><strong><a href="<?php echo tt_add_redirect(tt_url_for('signin'), tt_get_current_url()); ?>"><span>登录</span></a>后此处将显示你的专属推广链接</strong></label>           
<?php }else{ ?>
<label>你的专属推广链接：<strong>【&nbsp;<?php global $current_user; get_currentuserinfo(); echo home_url('?ref=') . $current_user->ID . "\n";?>&nbsp;】</strong></label>
<?php } ?>
<h2>二、如何查询和充值积分？</h2>
<label>登录本站后，点击右上角菜单，点击<a href="<?php echo home_url('/me/credits');?>">我的积分</a>即可查询和充值</label>
<h2>三、如何使用积分</h2>
<label>目前有两个途经消费积分，一是通过<a href="<?php echo home_url('/shop');?>">在线商城</a>购买积分商品，二是通过文章下载积分付费商品。</label>
<h2>四、积分使用注意事项及违规处理</h2>
<label>积分采取个人制，不可转赠不可借用，如发现有分享帐号给他人使用账户内积分的，一律清空积分或永久封号。</label></article>
            <div class="article-footer">
                <div class="support-author"></div>
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
<!-- 评论 -->
        <?php if($postdata->comment_status) { ?>
        <div id="respond">
            <h3><?php _e('LEAVE A REPLY', 'tt'); ?></h3>
                <?php load_mod( 'mod.ReplyForm', true ); ?>
                <?php comments_template( '/core/modules/mod.Comments.php', true ); ?>
            </div>
      <?php } ?>
    </div>
</div>
<!-- 边栏 -->
<?php load_mod('mod.Sidebar'); ?>
        </section>
    </div>
<?php tt_get_footer(); ?>