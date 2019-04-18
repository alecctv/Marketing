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


<?php if(tt_get_option('home_footer_btn_is')) { ?>
<section id="home-features" class="wrapper">
<div class="cta-large shift-bottom">
    <div class="cta-large__inner container">
        <div class="cta-large__text">
            <img src="<?php echo THEME_ASSET . '/img/logo-dark.png'?>" alt="">
            <h1><?php echo tt_get_option('home_footer_title');?></h1>
            <h2><?php echo tt_get_option('home_footer_desc');?></h2>
        </div>
        <a class="cta-large__button" href="<?php echo tt_get_option('home_footer_btn_href');?>"><?php echo tt_get_option('home_footer_btn_name');?></a>
    </div>
</div>
</section>
<?php } ?>


<footer class="footer">
  <div class="footer-wrap">
    <div class="footer-nav footer-custom">
  <!-- <div class="footer-nav-links">
                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'menu_class' => 'footer-menu', 'depth' => '1', 'fallback_cb' => 'header-menu'  ) ); ?>
            </div> -->
    <div id="footer-menu">
        <div class="container">
            <div class="pull-left">
                <ul class="pull-left mr95">
                    <li class="fs16" style="padding: 0px;">关于我们</li>
                    <li>
                        <a href="/about">关于我们</a></li>
                    <li>
                        <a href="/sitemap">网站地图</a></li>
                    <li>
                        <a href="/site/privacy-policies-and-terms">版权声明</a></li>
                </ul>
                <ul class="pull-left mr95">
                    <li class="fs16" style="padding: 0px;">常见问题</li>
                    <li>
                        <a href="/gmlc">购买流程</a></li>
                    <li>
                        <a href="/zffs">支付方式</a></li>
                    <li>
                        <a href="/shfw">售后服务</a></li>
                </ul>
                <ul class="pull-left mr95">
                    <li class="fs16" style="padding: 0px;">合作伙伴</li>
                    <li>
                        <a href="/tgyj">投稿有奖</a></li>
                    <li>
                        <a href="/business">广告合作</a></li>
                    <li>
                        <a href="/links">友情链接</a></li>
                </ul>
                <ul class="pull-left mr95">
                    <li class="fs16" style="padding: 0px;">解决方案</li>
                    <li>
                        <a href="/ztxg">主题修改</a></li>
                    <li>
                        <a href="/azts">安装调试</a></li>
                    <li>
                        <a href="/hjdj">环境搭建</a></li>
                </ul>
                <ul class="pull-left ml20 mr20">
                    <li class="fs16" style="padding: 0px;">官方微信</li>
                    <li>
                        <a>
                            <img class="kuangimg" alt="云设计" src="<?php echo THEME_ASSET . '/img/qr/weixin.png'; ?>"></a>
                    </li>
                </ul>
                <ul class="pull-left ml20 mr20">
                    <li class="fs16" style="padding: 0px;">官方支付宝</li>
                    <li>
                        <a>
                            <img class="kuangimg" alt="云设计" src="<?php echo THEME_ASSET . '/img/qr/alipay.png'; ?>"></a>
                    </li>
                </ul>
            </div>
            <div class="col-contact">
                <p class="phone"><?php echo get_bloginfo('name'); ?></p>
                <p>
                    <span class="J_serviceTime-normal">周一至周日 10:00-24:00</span>
                    <br>（其他时间勿扰）</p>
                <?php $qq = tt_get_option('tt_site_qq')  ?>
                <a rel="nofollow" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">在线咨询</a></div>
        </div>
    </div>
    <div id="footer-copy">
        <div class="container">
            <div class="copyright">
                <!-- 页脚菜单/版权信息 IDC No. -->
                <div class="footer-shares">
                <?php if($facebook = tt_get_option('tt_site_facebook')) { ?>
                <a class="fts-facebook" href="<?php echo 'https://www.facebook.com/' . $facebook; ?>" target="_blank">
                    <i class="tico tico-facebook"></i>
                  </a>
                <?php } ?>
                <?php if($twitter = tt_get_option('tt_site_twitter')) { ?>
                    <a class="fts-twitter" href="<?php echo 'https://www.twitter.com/' . $twitter; ?>" target="_blank">
                    <i class="tico tico-twitter"></i>
                    </a>
                <?php } ?>
                <?php if($qq = tt_get_option('tt_site_qq')) { ?>
                    <a class="fts-qq" href="<?php echo 'http://wpa.qq.com/msgrd?v=3&uin=' . $qq . '&site=qq&menu=yes'; ?>" target="_blank">
                    <i class="tico tico-qq"></i>
                    </a>
                <?php } ?>
                <?php if($qq_group = tt_get_option('tt_site_qq_group')) { ?>
                    <a class="fts-qq" href="<?php echo 'http://shang.qq.com/wpa/qunwpa?idkey=' . $qq_group; ?>" target="_blank">
                    <i class="tico tico-users2"></i>
                    </a>
                <?php } ?>
                <?php if($weibo = tt_get_option('tt_site_weibo')) { ?>
                    <a class="fts-twitter" href="<?php echo 'http://www.weibo.com/' . $weibo; ?>" target="_blank">
                    <i class="tico tico-weibo"></i>
                    </a>
                <?php } ?>
                <?php if($weixin = tt_get_option('tt_site_weixin')) { ?>
                    <a class="fts-weixin" href="javascript:void(0)" rel="weixin-qr" target="_blank">
                    <i class="tico tico-weixin"></i>
                    </a>
                <?php } ?>
                <?php if($qq_mailme = tt_get_option('tt_mailme_id')) { ?>
                    <a class="fts-email" href="<?php echo 'http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=' . $qq_mailme; ?>" target="_blank">
                    <i class="tico tico-envelope"></i>
                    </a>
                <?php } ?>
                <a class="fts-rss" href="<?php bloginfo('rss2_url'); ?>" target="_blank">
                    <i class="tico tico-rss"></i>
                </a>
                </div>
              <div class="footer-copy">
                &copy;&nbsp;<?php echo tt_copyright_year(); ?>&nbsp;&nbsp;<?php echo ' ' . get_bloginfo('name') . ' All Right Reserved '; ?>
                    <?php if($beian = tt_get_option('tt_beian')){
                    echo '·&nbsp;<a href="http://www.miitbeian.gov.cn/" rel="link" target="_blank">' . $beian . '</a>';
                } ?>
                     <?php echo '·&nbsp;<b style="color: #ff4425;"></b>&nbsp;Theme & Design By <a href="https://yunsheji.cc/" title="云设计" rel="link" target="_blank">云设计.</a>'; ?>
                <?php if(tt_get_option('tt_show_queries_num', false)) printf(__(' ·&nbsp;%1$s queries in %2$ss', 'tt'), get_num_queries(), timer_stop(0)); ?>
            </div>
            </div>
        </div>
    </div>
 </div>
</footer>
<!-- 侧边栏遮罩 -->
<div class="page__wrapper__overlay"></div>
<?php load_mod('mod.FixedControls'); ?>
<?php if(is_author() && current_user_can('edit_users'))load_mod('mod.ModalBanBox'); ?>
<?php if(is_home() || is_single() || is_author()){
    load_mod('mod.ModalPmBox');
    do_action('tt_ref'); // 推广检查的钩子
} ?>

<!-- 弹窗登录 -->
<?php if (!is_user_logged_in() && tt_get_option('tt_is_modloginform', true) ) { load_mod('mod.ModalLoginForm'); } ?>

</div><!-- 主页面End -->

<!-- 全局页面右侧展开模块 -->
<?php load_mod('mod.RightNav'); ?>
<!-- 全站js -->
<script type="text/javascript" src="<?php echo THEME_ASSET.'/js/app.js'; ?>"></script>

<?php if(tt_get_option('tt_foot_code')) { echo tt_get_option('tt_foot_code'); } ?>
<?php wp_footer(); ?>
<!--<?php echo get_num_queries();?> queries in <?php timer_stop(1); ?> seconds.-->
</body>
</html>