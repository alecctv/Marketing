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
<?php
error_reporting(0);

$main_color = tt_get_option('tt_main_color', '#3895D6');
$main_transparent = tt_get_option('tt_custom_css_transparent', '1');
$main_background_img = tt_get_option('tt_custom_css_background_img');
$main_background_color = tt_get_option('tt_custom_css_background_color', '#f2f4f7');
//缓存过期时间
$expires_offset = 3600*24*7;
header('Content-Type: text/css; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
//header('Last-Modified: ' . gmdate( "D, d M Y H:i:s", time() - $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

?>
body.site_util-download>.download-wrapper>.main-wrap>.primary,#sidebar>.widget,#main>.post>.single-body,#main>.post>.navigation,#main>.post>.related-posts,#main>.post>#respond,body.home>#content>#mod-show,.loop-rows article,.social-widget-link,body.author>#content>.author-area>.inner>.author-tab-box,body.me>.wrapper>.user-area>.row,body.me>.wrapper>.user-area .me-tab-box,.loop-grid article,body>.wrapper>.content>.row>.col>.product>.entry-detail,#main>.page>.single-header,body.single-bulletin>#content>.main-wrap>#main>.bulletin,body.single-bulletin>#content>.main-wrap>#main>.navigation {background: rgba(255, 255, 255, <?php echo $main_transparent; ?>)!important;}
.site-page>.wrapper>.main,textarea,#cms-cats, #cms-stickies,.credit-table{background-color: rgba(255, 255, 255, <?php echo $main_transparent; ?>)!important;}
body>.wrapper>.main>.processor {background-color: rgba(249, 249, 249, <?php echo $main_transparent; ?>)!important;}
.single-article h2,.single-article h3 {background: rgba(250, 250, 250, <?php echo $main_transparent; ?>)!important;}
section.billboard{opacity: <?php echo $main_transparent; ?>;}

body.site-page,body.single-post,body.home,body.category,body.search,body.tag,body.page,body.manage,body.me,body.author,body.error404,body.site_util-download,body.single-bulletin{background:<?php echo $main_background_color; ?> <?php echo 'url('.$main_background_img.') no-repeat fixed center'; ?>;background-size:cover;}

input:focus, textarea:focus {border-color: <?php echo $main_color; ?>}
.form-control:focus, .form-group.focus .form-control {border-color: <?php echo $main_color; ?>}
.form-group.focus .input-group-addon, .input-group.active .input-group-addon, .input-group.focus .input-group-addon {background-color: <?php echo $main_color; ?>;border-color: <?php echo $main_color; ?>;}
.input-group-addon+input.form-control {border-color: <?php echo $main_color; ?>;}
a {color: <?php echo $main_color; ?>}

#cms-stickies>.block-wrapper .sticky-container>.home-heading>span {border-bottom-color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cat-col .cat-container>.home-heading>span {border-bottom-color: <?php echo $main_color; ?>}

#sidebar>.widget>.widget-title>span {border-bottom-color: <?php echo $main_color; ?>}
#sidebar>.widget_float-sidebar>.widget>.widget-title>span {border-bottom-color: <?php echo $main_color; ?>}

.widget_tag-cloud>.widget-content>.tags>a:hover {background-color: <?php echo $main_color; ?>}

body.error-page .wrapper .main #linkBackHome {color: <?php echo $main_color; ?>}

#main>.post>.single-body>.article-header>.post-tags>a {background-color: <?php echo $main_color; ?>; opacity: 0.9;}
#main>.post>.single-body>.article-header>.post-tags>a:hover {background-color: <?php echo $main_color; ?>; opacity: 1;}

#main>.page>.single-body>.article-header>.post-tags>a {background-color: <?php echo $main_color; ?>; opacity: 0.9;}
#main>.page>.single-body>.article-header>.post-tags>a:hover {background-color: <?php echo $main_color; ?>; opacity: 1;}

body.home>#content>#mod-show>#slider>.unslider>.slides-wrap>ul>li>.slider-content>.meta-category>a {background-color: <?php echo $main_color; ?>;}

.widget_hot-posts>.widget-content>article>.entry-detail>h2 a:hover {color: <?php echo $main_color; ?>}
.widget_recent-comments>.widget-content>.comment>.comment-title a:hover {color: <?php echo $main_color; ?>}

.loop-rows article.post>.entry-detail>.entry-header>h2 a:hover {color: <?php echo $main_color; ?>}
.loop-rows article.post>.entry-detail>.entry-header>.entry-meta>.author>a:hover {color: <?php echo $main_color; ?>}

body.home>#content>#mod-show>#popular>.block3-widget>.block3-widget-content>article a:hover {color: <?php echo $main_color; ?>}

#cms-cats>.block-wrapper .cat-col .cat-container>.home-heading>a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s2>.col-left>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s2>.col-right>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s5>.col-full>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s5>.col-small>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cat-col-1_2 .row-small>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cat-col-1_2 .row-big>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s4>.col-large>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s4>.col-small>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s3>.col-left>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s3>.col-right>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cms-cat-s1>.col>article .entry-detail h3 a:hover {color: <?php echo $main_color; ?>}
#cms-cats>.block-wrapper .cat-col-1_2 .row-small>article .entry-detail h3 i {background-color: <?php echo $main_color; ?>}

header.white nav>ul a:hover {color: <?php echo $main_color; ?>}
header.white nav>ul>li.current-menu-item>a {color: <?php echo $main_color; ?>}
#main>.post>.navigation a:hover {color: <?php echo $main_color; ?>}
#main>.post>.related-posts article .entry-detail .entry-title a:hover {color: <?php echo $main_color; ?>}

.widget_author-info>.widget-content>.author-card_content>.author-fields>.author-user_level {background-color: <?php echo $main_color; ?>}

body.manage>.wrapper>.main-area .nav>.mg_tabs>li>a.active {color: <?php echo $main_color; ?>}
body.me>.wrapper>.user-area .nav>.me_tabs>li>a.active {color: <?php echo $main_color; ?>}