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
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui=yes">
    <title><?php  $die_title = get_query_var('die_title'); if(isset($die_title)) { echo $die_title; }else{ _e('Error Happened!', 'tt'); } ?></title>
    <meta name='robots' content='noindex,follow' >
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp"> <!-- 禁止移动端百度转码 -->
    <meta http-equiv="Cache-Control" content="private">
    <!--    <meta http-equiv="Cache-Control" content="max-age=0">-->
    <!--    <meta http-equiv="Cache-Control" content="must-revalidate">-->
    <meta name="format-detection" content="telephone=no, email=no"> <!-- 禁止自动识别电话号码和邮箱 -->
    <?php if($favicon = tt_get_option('tt_favicon')) { ?>
        <link rel="shortcut icon" href="<?php echo $favicon; ?>" >
    <?php } ?>
    <?php if($png_favicon = tt_get_option('tt_png_favicon')) { ?>
        <link rel="alternate icon" type="image/png" href="<?php echo $png_favicon; ?>" >
    <?php } ?>
    <!--[if lt IE 9]>
    <script src="<?php echo THEME_ASSET.'/vender/js/html5shiv/3.7.3/html5shiv.min.js'; ?>"></script>
    <script src="<?php echo THEME_ASSET.'/vender/js/respond/1.4.2/respond.min.js'; ?>"></script>
    <![endif]-->
    <!--[if lte IE 7]>
    <script type="text/javascript">
        window.location.href = "<?php echo tt_url_for('upgrade_browser'); ?>";
    </script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo tt_get_css(CSS_ERROR_PAGE); ?>"  />
    <link rel="stylesheet" type="text/css" href="<?php echo tt_get_custom_css(); ?>"  />
    <?php wp_head(); ?>
</head>
<body class="error error-page wp-die">
<header class="header special-header">

</header>
<div class="wrapper container no-aside">
    <div class="row">
        <div class="main inner-wrap">
            <h1>
                <?php  $die_title = get_query_var('die_title'); if(isset($die_title)) { echo $die_title; }else{ _e('Error Happened!', 'tt'); } ?>
            </h1>
            <p class="die-msg">
                <?php  $die_msg = get_query_var('die_msg'); if(isset($die_msg)) { echo $die_msg; }else{ _e('An error has happened on this page.', 'tt'); } ?>
            </p>
            <p>
                <a class="btn btn-lg btn-success link-home" id="linkBackHome" href="<?php echo home_url(); ?>" title="<?php _e('Go Back Home', 'tt'); ?>" role="button"><?php _e('Go Back Home', 'tt'); ?></a>
                <a class="btn btn-lg btn-success link-home" onClick="javascript :history.back(-1);" id="linkBackHome" href="#" title="返回上一页" role="button" style="padding-left: 50px;">返回上一页</a>
            </p>
        </div>
    </div>
</div>
<footer class="footer special-footer">
    <p><span class="copy">&copy;</span>&nbsp;<?php echo tt_copyright_year() . '&nbsp;' . get_bloginfo('name'); ?></p>
</footer>
</body>
</html>