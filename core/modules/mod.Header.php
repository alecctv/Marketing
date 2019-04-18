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
<?php load_mod('mod.Head'); ?>
<body <?php body_class('is-loadingApp'); ?>>
<!-- 页面开始 -->
<div class="page__wrapper">
<div class="loading-line"></div>
<?php if (tt_get_option('tt_is_loading_css', true)) { ?>
<div id="loading"> <div id="loading-center"> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> <div class="dot"></div> </div></div>
<?php } ?>
<!-- /.header -->
<header class="header">
<div class="header sps">
    <div class="header__inner">
        <div class="container header__content">
            <a class="header__logo" href="<?php echo home_url(); ?>"><img src="<?php echo tt_get_option('tt_logo'); ?>" alt="<?php echo get_bloginfo('name'); ?>" class="dark"><img src="<?php echo tt_get_option('tt_logo_light'); ?>" alt="<?php echo get_bloginfo('name'); ?>" class="light"></a>
            <div class="menu-primary-container">
                <?php wp_nav_menu( array( 'theme_location' => 'header-menu', 'container' => '', 'menu_id'=> 'menu-primary', 'menu_class' => 'header__nav header__nav--left', 'depth' => '2', 'fallback_cb' => false  ) ); ?>

            </div>
            
            <ul class="header__nav header__nav--right">

                <?php $user = wp_get_current_user(); ?>
                <?php if($user && $user->ID) { ?>
                <?php $unread = tt_count_pm_cached($user->ID, 0, MsgReadStatus::UNREAD); ?>
                    <li class="header__user">
                    <a href="<?php echo tt_url_for('my_settings'); ?>" title="个人中心">
                        <?php if($unread) { ?><i class="badge"></i><?php } ?>
                        <img src="<?php echo tt_get_avatar($user->ID, 'small'); ?>" class="avatar">
                        <span class="username"><?php echo $user->display_name; ?></span>
                    </a>
                    </li>
                    

                <?php }else{ ?>
                    <?php if (tt_get_option('tt_is_modloginform', true)) { ?>
                       <li class="header__login login-actions"><a href="javascript:" id="go-signin" class="login-link bind-redirect">登录</a></li>
                    <?php } else { ?>
                       <li class="header__login"><a href="<?php echo tt_add_redirect(tt_url_for('signin')); ?>" id="go-signin">登录</a></li>
                    <?php } ?>
                    <li class="header__nav__btn header__nav__btn--primary header__register"><a href="<?php echo tt_add_redirect(tt_url_for('signup')); ?>" id="go-register">注册</a></li>
            <?php } ?>

                <li class="header__nav__btn header__nav__btn--search">
                <div class="link-wrapper">
                    <i class="tico tico-search"></i>
                    <form method="get" action="<?php echo home_url(); ?>" role="search"">
                        <input name="s" type="text" value="" placeholder="输入关键词回车">
                    </form>
                </div>
                </li>
                <li class="header__nav__btn sidenav-trigger">
                    <a href="javascript: void(0)">
                        <span class="sidenav-trigger__open"><i class="tico tico-list-small-thumbnails"></i></span>
                        <span class="sidenav-trigger__close"><i class="tico tico-close delete"></i></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>   


  <?php if (tt_get_option('tt_enable_k_xzhid', true) && tt_get_option('tt_enable_k_xzhld', true)) { ?>
  <!-- 熊掌号Json_LD数据注释开始 -->
  <?php
    if(is_single()){
        echo '<script type="application/ld+json">{
        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
        "@id": "'.get_the_permalink().'",
        "appid": "'.tt_get_option('tt_k_id').'",
        "title": "'.get_the_title().'",
        "images": ["'.fanly_post_imgs().'"],
        "description": "'.get_the_excerpt().'",
        "pubDate": "'.get_the_time('Y-m-d\TH:i:s').'"
    }</script>
    ';}
    ?>
    <!-- 熊掌号Json_LD数据注释结束 -->
    <?php } ?>
</header> 