<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {
	// Change this to use your theme slug
	return 'options-framework-theme-tint';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'tt'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {
    // 主题版本
    $theme_version = trim(wp_get_theme()->get('Version'));

    $theme_pro = defined('TT_PRO') ? TT_PRO : !!preg_match('/([0-9-\.]+)PRO/i', $theme_version);

    // 博客名
    $blog_name = trim(get_bloginfo('name'));

    // 博客主页
    $blog_home = home_url();

    // 定义选项面板图片引用路径
    $imagepath =  THEME_URI . '/dash/of_inc/images/';

    // 所有分类
    $options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

    $options = array();

	// 主题选项 - 基本设置
	$options[] = array(
		'name' => __( 'Basic', 'tt' ),
		'type' => 'heading'
    );
  
	// - 首页描述
    $options[] = array(
        'name' => __( 'Home Page Description', 'tt' ),
        'desc' => __( 'Home page description meta information, good for SEO', 'tt' ),
        'id' => 'tt_home_description',
        'std' => '',
        'type' => 'text'
    );

    // - 首页关键词
    $options[] = array(
        'name' => __( 'Home Page Keywords', 'tt' ),
        'desc' => __( 'Home page keywords meta information, good for SEO', 'tt' ),
        'id' => 'tt_home_keywords',
        'std' => '',
        'type' => 'text'
    );

    // - 收藏夹图标
    $options[] = array(
        'name' => __( 'Favicon', 'tt' ),
        'desc' => __( 'Please upload an ico file', 'tt' ),
        'id' => 'tt_favicon',
        'std' => THEME_ASSET . '/img/favicon.ico',
        'type' => 'upload'
    );

    // - 收藏夹图标
    $options[] = array(
        'name' => __( 'Favicon(PNG)', 'tt' ),
        'desc' => __( 'Please upload an png file', 'tt' ),
        'id' => 'tt_png_favicon',
        'std' => THEME_ASSET . '/img/favicon.png',
        'type' => 'upload'
    );

    // - 本地化语言
    // $options[] = array(
    //     'name' => __( 'I18n', 'tt' ),
    //     'desc' => __( 'Multi languages and I18n support', 'tt' ),
    //     'id' => 'tt_l10n',
    //     'std' => 'zh_CN',
    //     'type' => 'select',
    //     'options' => array(
    //         'zh_CN' => __( 'zh_cn', 'tt' ),
    //         'en_US' => __( 'en_us', 'tt' )
    //     )
    // );

    // - Gravatar
    $options[] = array(
        'name' => __( 'Gravatar', 'tt' ),
        'desc' => __( 'Gravatar support', 'tt' ),
        'id' => 'tt_enable_gravatar',
        'std' => false,
        'type' => 'checkbox'
    );


    // - Timthumb
    $options[] = array(
        'name' => __( 'Timthumb Crop', 'tt' ),
        'desc' => __( 'Timthumb 裁剪支持（务必开启，否者图片大小不一致导致页面错乱）', 'tt' ),
        'id' => 'tt_enable_timthumb',
        'std' => true,
        'type' => 'checkbox'
    );

    // - Wp 图片裁剪
    $options[] = array(
        'name' => __( 'WP thumb image crop', 'tt' ),
        'desc' => __( 'Toggle WP thumb image crop', 'tt' ),
        'id' => 'tt_enable_wp_crop',
        'std' => false,
        'type' => 'checkbox'
    );

    // - jQuery 源
    $options[] = array(
        'name' => __( 'jQuery Source', 'tt' ),
        'desc' => __( 'Choose local or a CDN jQuery file', 'tt' ),
        'id' => 'tt_jquery',
        'std' => 'local_2',
        'type' => 'select',
        'options' => array(
            'local_1' => __('Local v1.12', 'tt'),
            'cdn_http' => __('CDN HTTP', 'tt'),
            'cdn_https' => __('CDN HTTPS', 'tt')
        )
    );

    // - jQuery 加载位置
//    $options[] = array(
//        'name' => __( 'jQuery Load Position', 'tt' ),
//        'desc' => __( 'Check to load jQuery on `body` end', 'tt' ),
//        'id' => 'tt_foot_jquery',
//        'std' => false,
//        'type' => 'checkbox'
//    );


	// 主题选项 - 样式设置
	$options[] = array(
		'name' => __( 'Style', 'tt' ),
		'type' => 'heading'
	);
  
   // - 网站主色
    $options[] = array(
        'name' => __( '网站主色', 'tt' ),
        'desc' => '',
        'id' => 'tt_main_color',
        'std' => '#3895D6',
        'type' => 'color'
    );
  
    // 是否弹窗登录
   $options[] = array(
        'name' => __( '是否启用弹窗登录', 'tt' ),
        'desc' => __( '启用（不启用则点击登录按钮跳转到独立登录页面）', 'tt' ),
        'id' => 'tt_is_modloginform',
        'std' => true,
        'type' => 'checkbox'
    );

    // 是否loading
   $options[] = array(
        'name' => __( '全站loading加载动画', 'tt' ),
        'desc' => __( '启用（页面加载中css动画）', 'tt' ),
        'id' => 'tt_is_loading_css',
        'std' => true,
        'type' => 'checkbox'
    );


    // - 自定义样式缓存时间
    $options[] = array(
        'name' => __( '自定义样式版本后缀', 'tt' ),
        'desc' => __( '在修改网站主色等自定义样式后如果因为缓存未生效,请修改此值', 'tt' ),
        'id' => 'tt_custom_css_cache_suffix',
        'std' => Utils::generateRandomStr(5),
        'class' => 'mini',
        'type' => 'text'
    );
    // - 网站 logo-dark
    $options[] = array(
        'name' => __( 'logo-暗色', 'tt' ),
        'desc' => __( '菜单默认的LOGO', 'tt' ),
        'id' => 'tt_logo',
        'std' => THEME_ASSET . '/img/logo-dark.png',
        'type' => 'upload'
    );

    // - 网站 logo-light
    $options[] = array(
        'name' => __( 'logo-亮色', 'tt' ),
        'desc' => __( '菜单下拉时候显示的LOGO', 'tt' ), // 用于邮件、登录页Logo等
        'id' => 'tt_logo_light',
        'std' => THEME_ASSET . '/img/logo-light.png',
        'type' => 'upload'
    );

    // - 登录页背景
    $options[] = array(
        'name' => __( '登录页背景', 'tt' ),
        'desc' => '',
        'id' => 'tt_signin_bg',
        'std' => THEME_ASSET . '/img/super-hero.jpg',
        'type' => 'upload'
    );

    // - 注册页背景
    $options[] = array(
        'name' => __( '注册页背景', 'tt' ),
        'desc' => '',
        'id' => 'tt_signup_bg',
        'std' => THEME_ASSET . '/img/super-hero.jpg',
        'type' => 'upload'
    );

    //开启文章页新样式
   $options[] = array(
        'name' => __( '文章页顶部不显示缩略图', 'tt' ),
        'desc' => __( '启用', 'tt' ),
        'id' => 'tt_enable_k_postnews',
        'std' => true,
        'type' => 'checkbox'
    );

    // 文章列表样式
    $options[] = array(
        'name' => '默认文章列表风格',
        'id' => 'post_item_style',
        'desc' => '可以选择文章列表的样式，搭配是否启用列文章表右侧侧边栏组合出多种布局风格',
        'options' => array(
            'style_0' => '列表风格',
            'style_1' => '卡片风格'
        ),
        'std' => 'style_0',
        'type' => "radio"
    );

     // - 右侧显示侧边栏
    $options[] = array(
        'name' => __( '默认文章列表右侧显示侧边栏', 'tt' ),
        'desc' => __( '只在首页文章列表右侧显示侧边栏', 'tt' ),
        'id' => 'post_item_is_sidebar',
        'std' => true,
        'type' => 'checkbox'
    );

    // 分类页-标签页-搜索页头部标题
    $options[] = array(
        'name' => __( '分类页-标签页-搜索页头部标题', 'tt' ),
        'desc' => __( '启用分类页-标签页-搜索页头部标题', 'tt' ),
        'id' => 'tt_enable_k_fbsbt',
        'std' => true,
        'type' => 'checkbox'
    );

    // 分类页模板
    $options[] = array(
        'name' => __('分类页面使用列表风格', 'tt'),
        'desc' => __('分类页面默认使用卡片+边栏布局，若要使用列表风格布局，请在此勾选对应分类', 'tt'),
        'id' => 'tt_alt_template_cats',
        'std' => array(),
        'type' => 'multicheck',
        'options' => $options_categories
    );

    // - 分类右侧显示侧边栏
    $options[] = array(
        'name' => __( '分类列表右侧显示侧边栏', 'tt' ),
        'desc' => __( '在分类文章列表右侧显示侧边栏', 'tt' ),
        'id' => 'post_template_cats_is_sidebar',
        'std' => true,
        'type' => 'checkbox'
    );




    // 主题选项 - 内容设置
    $options[] = array(
        'name' => __( 'Content', 'tt' ),
        'type' => 'heading'
    );

    // - 首页排除分类
    $options[] = array(
        'name' => __('Home Hide Categories', 'tt'),
        'desc' => __('Choose categories those are not displayed in homepage', 'tt'),
        'id' => 'tt_home_undisplay_cats',
        'std' => array(),
        'type' => 'multicheck',
        'options' => $options_categories
    );

    
    
     // - 开启首页顶部公告显示
    $options[] = array(
        'name' => __( '首页Banner底部显示公告', 'tt' ),
        'desc' => __( '在首页Banner底部滚动显示站点公告,开启必须新建公告文章，否则会出现缺失空白布局', 'tt' ),
        'id' => 'tt_enable_homepage_bulletins',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 公告链接的链接前缀
    $options[] = array(
        'name' => __( 'Bulletins Archive Link Slug', 'tt' ),
        'desc' => __( 'The special prefix in bulletin archive link', 'tt' ),
        'id' => 'tt_bulletin_archives_slug',
        'std' => 'bulletin',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 公告链接模式
    $options[] = array(
        'name' => __( 'Bulletin Permalink Mode', 'tt' ),
        'desc' => __( 'The link mode for the rewrite bulletin permalink', 'tt' ),
        'id' => 'tt_bulletin_link_mode',
        'std' => 'post_id',
        'type' => 'select',
        'class' => 'mini',
        'options' => array(
            'post_id' => __( 'Post ID', 'tt' ),
            'post_name' => __( 'Post Name', 'tt' )
        )
    );


    // - 公告的有效期天数
    $options[] = array(
        'name' => __( 'Bulletin Effect Days', 'tt' ),
        'desc' => __( 'The effect days of a bulletin, expired bulletin will never be show', 'tt' ),
        'id' => 'tt_bulletin_effect_days',
        'std' => 10,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 商品推荐
   $options[] = array(
       'name' => __( '首页商品展示模块', 'tt' ),
       'desc' => __( '显示热门首页商品', 'tt' ),
       'id' => 'tt_home_products_recommendation',
       'std' => true,
       'type' => 'checkbox'
   );

    $options[] = array(
        'name' => __( '模块顶部主标题', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_products_title',
        'std' => '主题 & 插件资源',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( '模块顶部标题描述', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_products_desc',
        'std' => '关注前沿设计风格，紧跟行业趋势，精选优质好资源！',
        'type' => 'text'
    );

   $options[] = array(
        'name' => __( '商品展示最新数目', 'tt' ),
        'desc' => __( '显示几个商品推荐', 'tt' ),
        'id' => 'tt_home_products_num',
        'std' => '4',
        'type' => 'text'
    );

    // - 首页最新文章
    $options[] = array(
        'name' => __( '首页最新文章', 'tt' ),
        'desc' => __( '默认开启，必选项', 'tt' ),
        'id' => 'tt_home_postlist_is',
        'std' => true,
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => __( '模块顶部主标题', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_postlist_title',
        'std' => '最新文章 & 资讯',
        'type' => 'text'
    );

    $options[] = array(
        'name' => __( '模块顶部标题描述', 'tt' ),
        'desc' => __( '', 'tt' ),
        'id' => 'tt_home_postlist_desc',
        'std' => '分享一切美好的事物、资讯、教程！',
        'type' => 'text'
    );





    // 主题选项 - Banner设置
    $options[] = array(
        'name' => __( 'Banner', 'tt' ),
        'type' => 'heading'
    );

    // - 首页顶部Banner
    $options[] = array(
        'name' => __( '首页顶部Banner', 'tt' ),
        'desc' => __( '建议开区', 'tt' ),
        'id' => 'tt_enable_home_hero',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 首页顶部Banner图像
    $options[] = array(
        'name' => __( 'Banner背景图', 'tt' ),
        'desc' => '',
        'id' => 'tt_home_hero_bg',
        'std' => THEME_ASSET . '/img/super-hero.jpg',
        'type' => 'upload'
    );

    $options[] = array(
            'name' => 'Banner主标题',
            'id' => 'home_banner_title',
            'desc' => 'Banner主标题文字',
            'std' => 'Website Marketing',
            'type' => 'text');

        $options[] = array(
            'id' => 'home_banner_dese',
            'desc' => 'Banner描述文字',
            'std' => '国内专业提供二次开发、主题插件定制，并提供各类网站建设工具资源分享，提供主题汉化、网站建置等支援服务。',
            'type' => 'text');

        $options[] = array(
            'id' => 'home_banner_btn_title',
            'desc' => '按钮文字',
            'std' => '立即体验',
            'type' => 'text');

        $options[] = array(
            'id' => 'home_banner_btn_href',
            'desc' => '按钮链接',
            'std' => '/shop',
            'type' => 'text');

    // 图标链接设置
    for ($i=1; $i <= 5; $i++) { 

        $options[] = array(
        'name' => '图标'.$i,
        'desc' => '建议尺寸110x96',
        'id' => 'home_banner_img_'.$i,
        'std' => THEME_ASSET . '/img/banner/'.$i.'.png',
        'type' => 'upload');

        $options[] = array(
            'id' => 'home_banner_title_'.$i,
            'desc' => '名称',
            'std' => 'Theme',
            'type' => 'text');

        $options[] = array(
            'id' => 'home_banner_href_'.$i,
            'desc' => '链接',
            'std' => '#',
            'type' => 'text');
    }


    // 主题选项 - 底部设置
    $options[] = array(
        'name' => __( '底部', 'tt' ),
        'type' => 'heading'
    );

    // - 底部按钮条
    $options[] = array(
        'name' => __( '网站底部按钮条', 'tt' ),
        'desc' => __( '开启', 'tt' ),
        'id' => 'home_footer_btn_is',
        'std' => true,
        'type' => 'checkbox'
    );

    $options[] = array(
            'id' => 'home_footer_title',
            'desc' => '主标题文字',
            'std' => 'Digital Marketing Theme',
            'type' => 'text');

    $options[] = array(
        'id' => 'home_footer_desc',
        'desc' => '描述文字',
        'std' => '为大客户提供的所有资源打包计划，包括当前已经发布的资源和以后持续更新内容在内只要购买这个资源包就可以免费下载和使用全站资源!',
        'type' => 'text');

    $options[] = array(
            'id' => 'home_footer_btn_name',
            'desc' => '按钮名称',
            'std' => '立即了解详情',
            'type' => 'text');

    $options[] = array(
        'id' => 'home_footer_btn_href',
        'desc' => '按钮链接',
        'std' => '/shop',
        'type' => 'text');

    // - Foot自定义代码
    $options[] = array(
        'name' => __( 'Foot Custom Code', 'tt' ),
        'desc' => __( 'Custom code loaded on page foot', 'tt' ),
        'id' => 'tt_foot_code',
        'std' => '',
        'type' => 'textarea',
        'raw' => true
    );

    // - Foot IDC备案文字
    $options[] = array(
        'name' => __( 'Foot Beian Text', 'tt' ),
        'desc' => __( 'IDC reference No. for regulations of China', 'tt' ),
        'id' => 'tt_beian',
        'std' => '',
        'type' => 'text'
    );


    $options[] = array(
        'name' => __( 'Site Open Date', 'tt' ),
        'desc' => __('The date of when site opened, use `YYYY-mm-dd` format', 'tt'),
        'id' => 'tt_site_open_date',
        'std' => date('Y-m-d'),//(new DateTime())->format('Y-m-d'),
        //'class' => 'mini',
        'type' => 'text'
    );



    // - 页脚输出统计PHP查询信息
    $options[] = array(
        'name' => __( 'Footer Queries Info', 'tt' ),
        'desc' => __( 'Show WordPress queries statistic information', 'tt' ),
        'id' => 'tt_show_queries_num',
        'std' => false,
        'type' => 'checkbox'
    );


    // 主题设置 - 边栏设置
    $options[] = array(
        'name' => __( 'Sidebar', 'tt' ),
        'type' => 'heading'
    );


    // - 所有边栏
    $all_sidebars = array(
        'sidebar_common'    =>    __('Common Sidebar', 'tt'),
        'sidebar_home'      =>    __('Home Sidebar', 'tt'),
        'sidebar_single'    =>    __('Single Sidebar', 'tt'),
        //'sidebar_archive'   =>    __('Archive Sidebar', 'tt'),
        //'sidebar_category'  =>    __('Category Sidebar', 'tt'),
        'sidebar_search'    =>    __('Search Sidebar', 'tt'),
        //'sidebar_404'       =>    __('404 Sidebar', 'tt'),
        'sidebar_page'      =>    __('Page Sidebar', 'tt'),
        'sidebar_download'  =>    __('Download Page Sidebar', 'tt')
    );
    // - 待注册的边栏
    $options[] = array(
        'name' => __('Register Sidebars', 'tt'),
        'desc' => __('Check the sidebars to register', 'tt'),
        'id'   => 'tt_register_sidebars',
        'std'  => array('sidebar_common' => true),
        'type' => 'multicheck',
        'options' => $all_sidebars
    );

    $register_status = of_get_option('tt_register_sidebars', array('sidebar_common' => true));
    if(!is_array($register_status)) {
        $register_status = array('sidebar_common' => true);
    }elseif(!isset($register_status['sidebar_common'])){
        $register_status['sidebar_common'] = true;
    }

    $available_sidebars = array();
    foreach ($register_status as $key => $value){
        if($value) $available_sidebars[$key] = $all_sidebars[$key];
    }
    $available_sidebars['sidebar_common'] = __('Common Sidebar', 'tt'); // 默认边栏始终可选

    $options[] = array(
        'name' => __('Home Sidebar', 'tt'),
        'desc' => __('Select a sidebar for homepage', 'tt'),
        'id'   => 'tt_home_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Single Sidebar', 'tt'),
        'desc' => __('Select a sidebar for single post page', 'tt'),
        'id'   => 'tt_single_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Archive Sidebar', 'tt'),
        'desc' => __('Select a sidebar for archive page', 'tt'),
        'id'   => 'tt_archive_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

//    $options[] = array(
//        'name' => __('Category Sidebar', 'tt'),
//        'desc' => __('Select a sidebar for category page', 'tt'),
//        'id'   => 'tt_category_sidebar',
//        'std'  => array('sidebar_common' => true),
//        'type' => 'select',
//        'class' => 'mini',
//        'options' => $available_sidebars
//    );

    $options[] = array(
        'name' => __('Search Sidebar', 'tt'),
        'desc' => __('Select a sidebar for search page', 'tt'),
        'id'   => 'tt_search_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

//    $options[] = array(
//        'name' => __('404 Sidebar', 'tt'),
//        'desc' => __('Select a sidebar for 404 page', 'tt'),
//        'id'   => 'tt_404_sidebar',
//        'std'  => array('sidebar_common' => true),
//        'type' => 'select',
//        'class' => 'mini',
//        'options' => $available_sidebars
//    );

    $options[] = array(
        'name' => __('Page Sidebar', 'tt'),
        'desc' => __('Select a sidebar for page', 'tt'),
        'id'   => 'tt_page_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );

    $options[] = array(
        'name' => __('Download Page Sidebar', 'tt'),
        'desc' => __('Select a sidebar for download page', 'tt'),
        'id'   => 'tt_download_sidebar',
        'std'  => array('sidebar_common' => true),
        'type' => 'select',
        'class' => 'mini',
        'options' => $available_sidebars
    );



    // 主题选项 - 服务设置
    $options[] = array(
        'name' => __( '服务', 'tt' ),
        'type' => 'heading'
    );
    
   // - 首页服务内容模块
    $options[] = array(
        'name' => __( '首页服务内容模块', 'tt' ),
        'desc' => __( '默认开启', 'tt' ),
        'id' => 'tt_home_features',
        'std' => true,
        'type' => 'checkbox'
    );

    for ($i=1; $i <= 6; $i++) {    
    $options[] = array(
        'name' => '服务'.$i,
        'id' => 'feature_title_'.$i,
        'desc' => '服务标题文字',
        'std' => '主题定制',
        'type' => 'text');

    $options[] = array(
        'id' => 'feature_desc_'.$i,
        'desc' => '服务描述文字',
        'std' => '根据客户提供的网站需求（网站构思、类型、产品、栏目、页面等）信息设计全新的网站视觉风格。',
        'type' => 'text');

    $options[] = array(
        'id' => 'feature_href_'.$i,
        'desc' => '链接',
        'std' => '#',
        'type' => 'text');
    }





	// 主题设置 - 边栏设置
	


	// 主题设置 - 社会化设置(包含管理员社会化链接等)
	$options[] = array(
		'name' => __( 'Social', 'tt' ),
		'type' => 'heading'
	);


    // - 站点服务QQ
    $options[] = array(
        'name' => __( 'Site QQ', 'tt' ),
        'desc' => __( 'The QQ which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_qq',
        'std' => '200933220',
        'type' => 'text'
    );


    // - 站点服务QQ群
    $options[] = array(
        'name' => __( 'Site QQ Group ID', 'tt' ),
        'desc' => __( 'The ID key of QQ group which is dedicated for the site, visit `http://shang.qq.com` for detail', 'tt' ),
        'id' => 'tt_site_qq_group',
        'std' => 'c3d3931c2af9e1d8d16dbc9088dbfc2298df2b9e78bd0f4db09f0f4dea6052a1',
        'type' => 'text'
    );


    // - 站点服务微博
    $options[] = array(
        'name' => __( 'Site Weibo', 'tt' ),
        'desc' => __( 'The name of Weibo account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_weibo',
        'std' => 'yunsheji',
        'type' => 'text'
    );


    // - 站点服务Facebook
    $options[] = array(
        'name' => __( 'Site Facebook', 'tt' ),
        'desc' => __( 'The name of Facebook account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_facebook',
        'std' => 'yunsheji',
        'type' => 'text'
    );


    // - 站点服务Twitter
    $options[] = array(
        'name' => __( 'Site Twitter', 'tt' ),
        'desc' => __( 'The name of Twitter account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_twitter',
        'std' => 'yunsheji',
        'type' => 'text'
    );


    // - 站点服务微信
    $options[] = array(
        'name' => __( 'Site Weixin', 'tt' ),
        'desc' => __( 'The qrcode image of Weixin account which is dedicated for the site', 'tt' ),
        'id' => 'tt_site_weixin_qr',
        'std' => THEME_ASSET . '/img/qr/weixin.png',
        'type' => 'upload'
    );


    // - 开启QQ登录
    $options[] = array(
        'name' => __( 'QQ Login', 'tt' ),
        'desc' => __( 'QQ login ', 'tt' ),
        'id' => 'tt_enable_qq_login',
        'std' => false,
        'type' => 'checkbox'
    );


	// - QQ开放平台应用ID
    $options[] = array(
        'name' => __( 'QQ Open ID', 'tt' ),
        'desc' => __( 'Your QQ open application ID', 'tt' ),
        'id' => 'tt_qq_openid',
        'std' => '',
        'type' => 'text'
    );


    // - QQ开放平台应用KEY
    $options[] = array(
        'name' => __( 'QQ Open Key', 'tt' ),
        'desc' => __( 'Your QQ open application key', 'tt' ),
        'id' => 'tt_qq_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 开启微博登录
    $options[] = array(
        'name' => __( 'Weibo Login', 'tt' ),
        'desc' => __( 'Weibo login access', 'tt' ),
        'id' => 'tt_enable_weibo_login',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 微博开放平台Key
    $options[] = array(
        'name' => __( 'Weibo Open Key', 'tt' ),
        'desc' => __( 'Your weibo open application key', 'tt' ),
        'id' => 'tt_weibo_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 微博开放平台Secret
    $options[] = array(
        'name' => __( 'Weibo Open Secret', 'tt' ),
        'desc' => __( 'Your weibo open application secret', 'tt' ),
        'id' => 'tt_weibo_opensecret',
        'std' => '',
        'type' => 'text'
    );


    // - 开启微信登录
    $options[] = array(
        'name' => __( 'Weixin Login', 'tt' ),
        'desc' => __( 'Weixin login access', 'tt' ),
        'id' => 'tt_enable_weixin_login',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 微信开放平台Key
    $options[] = array(
        'name' => __( 'Weixin Open Key', 'tt' ),
        'desc' => __( 'Your weixin open application key', 'tt' ),
        'id' => 'tt_weixin_openkey',
        'std' => '',
        'type' => 'text'
    );


    // - 微信开放平台Secret
    $options[] = array(
        'name' => __( 'Weixin Open Secret', 'tt' ),
        'desc' => __( 'Your weixin open application secret', 'tt' ),
        'id' => 'tt_weixin_opensecret',
        'std' => '',
        'type' => 'text'
    );

    // - 开放平台接入新用户角色
    $options[] = array(
        'name' => __('Open User Default Role', 'tt'),
        'desc' => __('Choose the role and capabilities for the new connected user from open', 'tt'),
        'id' => 'tt_open_role',
        'std' => 'contributor',
        'type' => 'select',
        'options' => array(
            'editor' => __('Editor', 'tt'),
            'author' => __('Author', 'tt'),
            'contributor' => __('Contributor', 'tt'),
            'subscriber' => __('Subscriber', 'tt'),
        )
    );



	// 主题设置 - 广告设置
	$options[] = array(
		'name' => __( 'Ad', 'tt' ),
		'type' => 'heading'
	);


    // - 开启导航栏下方大横幅广告
    $options[] = array(
        'name' => __( '开启导航栏下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_nav_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 导航栏下方大横幅广告
    $options[] = array(
        'name' => __( '导航栏下方横幅广告', 'tt' ),
        'desc' => __( '多个页面可用', 'tt' ),
        'id' => 'tt_nav_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启幻灯下方大横幅广告
    $options[] = array(
        'name' => __( '开启幻灯下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_slide_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 幻灯下方大横幅广告
    $options[] = array(
        'name' => __( '幻灯下方大横幅广告', 'tt' ),
        'desc' => __( '仅首页幻灯开启时可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_slide_bottom_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启置顶分类下方大横幅广告
    $options[] = array(
        'name' => __( '开启置顶分类下方大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_fc_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 置顶分类下方大横幅广告
    $options[] = array(
        'name' => __( '置顶分类下方大横幅广告', 'tt' ),
        'desc' => __( '仅首页置顶分类开启时可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_fc_bottom_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启底部大横幅广告
    $options[] = array(
        'name' => __( '开启底部大横幅广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 底部大横幅广告
    $options[] = array(
        'name' => __( '底部大横幅广告', 'tt' ),
        'desc' => __( '多个页面可用, 标准尺寸960*90', 'tt' ),
        'id' => 'tt_bottom_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启文章文字上方广告
    $options[] = array(
        'name' => __( '开启文章文字上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_content_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 文章文字上方广告
    $options[] = array(
        'name' => __( '文章文字上方广告', 'tt' ),
        'desc' => __( '标准尺寸640*60', 'tt' ),
        'id' => 'tt_post_content_top_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启文章文字下方广告
    $options[] = array(
        'name' => __( '开启文章文字下方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_content_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 文章文字下方广告
    $options[] = array(
        'name' => __( '文章文字下方广告', 'tt' ),
        'desc' => __( '标准尺寸640*60', 'tt' ),
        'id' => 'tt_post_content_bottom_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启相关文章上方广告
    $options[] = array(
        'name' => __( '开启相关文章上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_relates_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 相关文章上方广告
    $options[] = array(
        'name' => __( '相关文章上方广告', 'tt' ),
        'desc' => __( '标准尺寸760*90', 'tt' ),
        'id' => 'tt_post_relates_top_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启评论框上方广告
    $options[] = array(
        'name' => __( '开启评论框上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_post_comment_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 评论框上方广告
    $options[] = array(
        'name' => __( '评论框上方广告', 'tt' ),
        'desc' => __( '标准尺寸760*90', 'tt' ),
        'id' => 'tt_post_comment_top_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启下载页面内容区上方广告
    $options[] = array(
        'name' => __( '开启下载页面内容区上方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_dl_top_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 下载页面内容区上方广告1
    $options[] = array(
        'name' => __( '下载页面内容区上方广告1', 'tt' ),
        'desc' => __( '双矩形广告位-左, 标准尺寸350*300', 'tt' ),
        'id' => 'tt_dl_top_banner_1',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );

    // - 下载页面内容区上方广告2
    $options[] = array(
        'name' => __( '下载页面内容区上方广告2', 'tt' ),
        'desc' => __( '双矩形广告位-右, 标准尺寸350*300', 'tt' ),
        'id' => 'tt_dl_top_banner_2',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启下载页面内容区下方广告
    $options[] = array(
        'name' => __( '开启下载页面内容区下方广告', 'tt' ),
        'desc' => __('开启', 'tt'),
        'id' => 'tt_enable_dl_bottom_banner',
        'std' => false,
        'type' => 'checkbox'
    );

    // - 底部大横幅广告
    $options[] = array(
        'name' => __( '下载页面内容区下方广告', 'tt' ),
        'desc' => __( '仅适用于下载页面内容区下方, 标准尺寸760*90', 'tt' ),
        'id' => 'tt_dl_bottom_banner',
        'std' => '这里是广告代码',
        'raw' => true,
        'type' => 'textarea'
    );


    // 主题设置 - 积分系统设置
    $options[] = array(
        'name' => __('Credit', 'tt'),
        'type' => 'heading'
    );

    // - 积分价格
    $options[] = array(
        'name' => __( '积分价格(元/100积分)', 'tt' ),
        'desc' => __('注意: 积分充值最小单位为100, 此价格为100个积分的价格'),
        'id' => 'tt_hundred_credit_price',
        'std' => 1,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 每日签到积分奖励
    $options[] = array(
        'name' => __( '每日签到积分奖励', 'tt' ),
        'desc' => '',
        'id' => 'tt_daily_sign_credits',
        'std' => '10',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 注册奖励积分
    $options[] = array(
        'name' => __( '注册奖励积分', 'tt' ),
        'desc' => __( '新用户注册时默认赠送的积分数量', 'tt' ),
        'id' => 'tt_reg_credit',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 访问推广奖励积分
    $options[] = array(
        'name' => __( '访问推广奖励积分', 'tt' ),
        'desc' => __( '通过分享链接推广其他用户访问本站时奖励的积分数量', 'tt' ),
        'id' => 'tt_rec_view_credit',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 注册推广奖励积分
    $options[] = array(
        'name' => __( '注册推广奖励积分', 'tt' ),
        'desc' => __( '通过分享链接推广其他用户注册本站用户时奖励的积分数量', 'tt' ),
        'id' => 'tt_rec_reg_credit',
        'std' => '30',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 投稿奖励积分
    $options[] = array(
        'name' => __( '投稿奖励积分', 'tt' ),
        'desc' => __( '用户向本站投稿文章通过时奖励的积分', 'tt' ),
        'id' => 'tt_rec_post_credit',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 评论奖励积分
    $options[] = array(
        'name' => __( '评论奖励积分', 'tt' ),
        'desc' => __( '用户在站内发表评论一次奖励的积分', 'tt' ),
        'id' => 'tt_rec_comment_credit',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 访问推广次数限制
    $options[] = array(
        'name' => __( '访问推广次数限制', 'tt' ),
        'desc' => __( '每日通过访问推广最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_view_num',
        'std' => '50',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 注册推广次数限制
    $options[] = array(
        'name' => __( '注册推广次数限制', 'tt' ),
        'desc' => __( '每日通过注册推广最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_reg_num',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 投稿积分奖励次数限制
    $options[] = array(
        'name' => __( '投稿积分奖励次数限制', 'tt' ),
        'desc' => __( '每日通过投稿最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_post_num',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    // - 评论积分奖励次数限制
    $options[] = array(
        'name' => __( '评论积分奖励次数限制', 'tt' ),
        'desc' => __( '每日通过评论最多获得积分奖励的次数', 'tt' ),
        'id' => 'tt_rec_comment_num',
        'std' => '10',
        'class' => 'mini',
        'type' => 'text'
    );


    // 主题设置 - 会员系统设置
	$options[] = array(
		'name' => __( 'Membership', 'tt' ),
		'type' => 'heading'
	);

    // - 月费会员价格
    $options[] = array(
        'name' => __( '月费会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_monthly_vip_price',
        'std' => 8,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 年费会员价格
    $options[] = array(
        'name' => __( '年费会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_annual_vip_price',
        'std' => 80,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 永久会员价格
    $options[] = array(
        'name' => __( '永久会员价格', 'tt' ),
        'desc' => '',
        'id' => 'tt_permanent_vip_price',
        'std' => 159,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 月费会员默认折扣
    $options[] = array(
        'name' => __( '月费会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_monthly_vip_discount',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 年费会员默认折扣
    $options[] = array(
        'name' => __( '年费会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_annual_vip_discount',
        'std' => 90,
        'class' => 'mini',
        'type' => 'text'
    );


    // - 永久会员默认折扣
    $options[] = array(
        'name' => __( '永久会员默认折扣 (%)', 'tt' ),
        'desc' => '',
        'id' => 'tt_permanent_vip_discount',
        'std' => 80,
        'class' => 'mini',
        'type' => 'text'
    );

    // - 可投稿分类

    $options[] = array(
        'name' => __('可投稿分类', 'tt'),
        'desc' => __('选择允许用户投稿的分类, 至少选择一个', 'tt'),
        'id' => 'tt_contribute_cats',
        'std' => '',
        'type' => 'multicheck',
        'options' => $options_categories
    );

    // - 投稿最少字数
    $options[] = array(
        'name' => __( '投稿最少字数', 'tt' ),
        'desc' => '',
        'id' => 'tt_contribute_post_words_min',
        'std' => 100,
        'class' => 'mini',
        'type' => 'text'
    );


	// 主题设置 - 商店设置
	$options[] = array(
		'name' => __( 'Shop', 'tt' ),
		'type' => 'heading'
	);


    // - 开启商品系统
    $options[] = array(
        'name' => __( 'Enable Shop', 'tt' ),
        'desc' => __( 'After enable this, users can create orders and buy something those the site provided', 'tt' ),
        'id' => 'tt_enable_shop',
        'std' => true,
        'type' => $theme_pro ? 'checkbox' : 'disabled'
    );


    // - 商品链接的链接前缀
    $options[] = array(
        'name' => __( 'Products Archive Link Slug', 'tt' ),
        'desc' => __( 'The special prefix in product archive link', 'tt' ),
        'id' => 'tt_product_archives_slug',
        'std' => 'shop',
        'class' => 'mini',
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 商品链接模式
    $options[] = array(
        'name' => __( 'Product Permalink Mode', 'tt' ),
        'desc' => __( 'The link mode for the rewrite product permalink', 'tt' ),
        'id' => 'tt_product_link_mode',
        'std' => 'post_id',
        'type' => $theme_pro ? 'select' : 'disabled',
        'class' => 'mini',
        'options' => array(
            'post_id' => __( 'Post ID', 'tt' ),
            'post_name' => __( 'Post Name', 'tt' )
        )
    );


    // - 商品首页关键词
    $options[] = array(
        'name' => __( 'Shop Home Keywords', 'tt' ),
        'desc' => __( 'The keywords of shop homepage, good for SEO', 'tt' ),
        'id' => 'tt_shop_keywords',
        'std' => __('商城,数字市场,数字资源', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );

    // - 商品首页横幅图片
    $options[] = array(
        'name' => __( 'Banner背景图', 'tt' ),
        'desc' => '',
        'id' => 'tt_shop_hero_bg',
        'std' => THEME_ASSET . '/img/super-hero-shop.jpg',
        'type' => 'upload'
    );

    // - 商品首页横幅大标题
    $options[] = array(
        'name' => __( '商店主页横幅标题', 'tt' ),
        'desc' => __( '商店主页横幅标题文字', 'tt' ),
        'id' => 'tt_shop_title',
        'std' => __('SHOP', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 商品首页横幅小标题
    $options[] = array(
        'name' => __( '商店主页横幅副标题', 'tt' ),
        'desc' => __( 'The sub title displayed in the banner of shop homepage', 'tt' ),
        'id' => 'tt_shop_sub_title',
        'std' => __('使用Marketing创建一个美观简单的数字市场', 'tt'),
        'type' => $theme_pro ? 'text' : 'disabled'
    );


    // - 支付方式
    $options[] = array(
        'name' => __('支付方式', 'tt'),
        'desc' => __('选择支付方式后保存设置即可看到当前选择的支付选项', 'tt'),
        'id' => 'tt_pay_channel',
        'std' => 'alipay',
        'type' => 'select', //$theme_pro ? 'select' : 'disabled',
        'options' => array(
            'alipay' => __('Alipay', 'tt'),  // 支付宝
            'apsv' => __('Alipay Supervisor免签约支付', 'tt'), // Alipay Supervisor 扫码支付
            'youzan' => __('有赞自主店铺服务', 'tt'),
        ),
    );
        // 支付方式识别###############################################################################
        $select_pay_channel=tt_get_option('tt_pay_channel');
        switch ($select_pay_channel)
        {
        case "alipay":
             // - 支付宝收款帐户
            $options[] = array(
                'name' => __('支付宝收款帐户邮箱', 'tt'),
                'desc' => __('支付宝收款帐户邮箱,要收款必填并务必保持正确', 'tt'),
                'id' => 'tt_alipay_email',
                'std' => '',
                'type' => $theme_pro ? 'text' : 'disabled',
            );

            // - 站点服务支付宝
            $options[] = array(
                'name' => __('Site Alipay', 'tt'),
                'desc' => __('The qrcode image of Alipay account which is dedicated for the site', 'tt'),
                'id' => 'tt_site_alipay_qr',
                'std' => THEME_ASSET.'/img/qr/alipay.png',
                'type' => 'upload',
            );

            // - 支付宝商家身份ID
            $options[] = array(
                'name' => __('支付宝商家身份ID', 'tt'),
                'desc' => __('合作身份者id，以2088开头的16位纯数字', 'tt'),
                'id' => 'tt_alipay_partner',
                'std' => '',
                'type' => $theme_pro ? 'text' : 'disabled',
            );

            // - 支付宝商家身份key
            $options[] = array(
                'name' => __('支付宝商家身份key', 'tt'),
                'desc' => __('支付宝商家身份安全检验码，以数字和字母组成的32位字符', 'tt'),
                'id' => 'tt_alipay_key',
                'std' => '',
                'type' => $theme_pro ? 'text' : 'disabled',
            );

            // - 支付宝商家收款类型
            $options[] = array(
                'name' => __('支付宝商家收款类型', 'tt'),
                'desc' => __('支付宝商家收款类型, 支持即时到账, 双功能和担保交易, 注意：切换类型后必须对应修改商家身份key', 'tt'),
                'id' => 'tt_alipay_service',
                'std' => 'create_direct_pay_by_user',
                'type' => $theme_pro ? 'select' : 'disabled',
                'options' => array(
                    'create_direct_pay_by_user' => __('即时到账', 'tt'),  // 即时到账
                    'trade_create_by_buyer' => __('双功能', 'tt'), // 双功能
                    'create_partner_trade_by_buyer' => __('担保交易', 'tt'), // 担保交易
                ),
            );
            break;
        case "apsv":

            // - Alipay Supervisor APP ID
            $options[] = array(
                'name' => __('Alipay Supervisor APP ID', 'tt'),
                'desc' => __('You should buy Alipay Supervisor first and then get the app id', 'tt'),
                'id' => 'tt_apsv_appid',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // - Alipay Supervisor APP Key
            $options[] = array(
                'name' => __('Alipay Supervisor APP Key', 'tt'),
                'desc' => __('You should buy Alipay Supervisor first and then get the app key', 'tt'),
                'id' => 'tt_apsv_appkey',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // - Alipay Supervisor Secret
            $options[] = array(
                'name' => __('Alipay Supervisor Secret', 'tt'),
                'desc' => __('A random string which verify the legitimacy of a request from Alipay Supervisor, should conform to Alipay Supervisor configuration', 'tt'),
                'id' => 'tt_apsv_secret',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );
            break;
        case "youzan":
             // - 有赞云client id
            $options[] = array(
                'name' => __('有赞云Client ID', 'tt'),
                'desc' => __('有赞云应用client_id, 参考https://console.youzanyun.com/application/setting', 'tt'),
                'id' => 'tt_youzan_client_id',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // - 有赞云client secret
            $options[] = array(
                'name' => __('有赞云Client Secret', 'tt'),
                'desc' => __('有赞云应用client_secret, 参考https://console.youzanyun.com/application/setting', 'tt'),
                'id' => 'tt_youzan_client_secret',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // - 有赞云应用绑定的店铺ID
            $options[] = array(
                'name' => __('有赞云应用绑定的店铺ID', 'tt'),
                'desc' => __('有赞云应用绑定的店铺ID, 参考https://console.youzanyun.com/application/setting 基本信息部分', 'tt'),
                'id' => 'tt_youzan_kdt_id',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // 使用有赞云推送中转服务
            $options[] = array(
                'name' => __('使用有赞云推送中转服务', 'tt'),
                'desc' => __('如果你需要使用外置的推送解析服务，请开启此选项，并向主题方咨询购买服务，主要解决问题是单独服务稳定性，以及有赞云推送不支持LetEncrypt证书的网址，只能中转推送', 'tt'),
                'id' => 'tt_enable_youzan_helper',
                'std' => false,
                'type' => 'checkbox',
            );

            // - 有赞云辅助推送校验secret
            $options[] = array(
                'name' => __('有赞云辅助推送校验secret', 'tt'),
                'desc' => __('有赞云辅助推送校验secret, 请保持该secret私有, 并与辅助中配置的SELF_SECRET一致', 'tt'),
                'id' => 'tt_youzan_self_secret',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );

            // - 有赞云辅助接口地址
            $options[] = array(
                'name' => __('有赞云辅助接口地址', 'tt'),
                'desc' => __('有赞云辅助接口地址, 用于生成专用的收款二维码', 'tt'),
                'id' => 'tt_youzan_util_api',
                'std' => '',
                'type' => 'text', //$theme_pro ? 'text' : 'disabled'
            );
            break;
        }
    
        // 支付方式识别###############################################################################

   
    
    // - 自动关闭过期订单
    $options[] = array(
        'name' => __('订单状态维护', 'tt'),
        'desc' => __('自动关闭多少天以上未支付订单, 设置为0则不启用自动状态维护', 'tt'),
        'id' => 'tt_maintain_orders_deadline',
        'std' => '0',
        'class' => 'mini',
        'type' => 'text', //$theme_pro ? 'text' : 'disabled'
    );


	// 主题设置 - 辅助设置(包含短链接、SMTP工具等)
	$options[] = array(
		'name' => __( 'Auxiliary', 'tt' ),
		'type' => 'heading'
	);


	// - Memcache/redis/...内存对象缓存
    $options[] = array(
        'name' => __( 'Object Cache', 'tt' ),
        'desc' => __( 'Object cache support, accelerate your site', 'tt' ),
        'id' => 'tt_object_cache',
        'std' => 'none',
        'type' => 'select',
        'options' => array(
            'memcache' => __( 'Memcache', 'tt' ),  //TODO: add tutorial url
            'redis' => __( 'Redis', 'tt' ),
            'none'  => __('None', 'tt')
        )
    );


    if (of_get_option('tt_object_cache')=='memcache'):
    // - Memcache Host
    $options[] = array(
        'name' => __( 'Memcache Host', 'tt' ),
        'desc' => __( 'Memcache server host', 'tt' ),
        'id' => 'tt_memcache_host',
        'std' => '127.0.0.1',
        'type' => 'text'
    );


    // - Memcache Port
    $options[] = array(
        'name' => __( 'Memcache Port', 'tt' ),
        'desc' => __( 'Memcache server port', 'tt' ),
        'id' => 'tt_memcache_port',
        'std' => 11211,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    if (of_get_option('tt_object_cache')=='redis'):
    // - Redis Host
    $options[] = array(
        'name' => __( 'Redis Host', 'tt' ),
        'desc' => __( 'Redis server host', 'tt' ),
        'id' => 'tt_redis_host',
        'std' => '127.0.0.1',
        'type' => 'text'
    );


    // - Redis Port
    $options[] = array(
        'name' => __( 'Redis Port', 'tt' ),
        'desc' => __( 'Redis server port', 'tt' ),
        'id' => 'tt_redis_port',
        'std' => 6379,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    // - Separator
//    $options[] = array(
//        'name' => __( 'Mailer Separator', 'tt' ),
//        'class'=> 'option-separator',
//        'type' => 'info'
//    );

    // - SMTP/PHPMail
    $options[] = array(
        'name' => __( 'SMTP/PHPMailer', 'tt' ),
        'desc' => __( 'Use SMTP or PHPMail as default mailer', 'tt' ),
        'id' => 'tt_default_mailer',
        'std' => 'php',
        'type' => 'select',
        'options' => array(
            'php' => __('PHP', 'tt'),
            'smtp' => __('SMTP', 'tt')
        )
    );


    if (of_get_option('tt_default_mailer')==='smtp'):
    // - SMTP 主机
    $options[] = array(
        'name' => __( 'SMTP Host', 'tt' ),
        'desc' => __( 'Your SMTP service host', 'tt' ),
        'id' => 'tt_smtp_host',
        'std' => '',
        'placeholder' => 'e.g smtp.163.com',
        'type' => 'text'
    );


    // - SMTP 端口
    $options[] = array(
        'name' => __( 'SMTP Port', 'tt' ),
        'desc' => __( 'Your SMTP service port', 'tt' ),
        'id' => 'tt_smtp_port',
        'std' => 465,
        'class' => 'mini',
        'type' => 'text'
    );


    // - SMTP 安全
    $options[] = array(
        'name' => __( 'SMTP Secure', 'tt' ),
        'desc' => __( 'Your SMTP server secure protocol', 'tt' ),
        'id' => 'tt_smtp_secure',
        'std' => 'ssl',
        'type' => 'select',
        'options' => array(
            'auto' => __('Auto', 'tt'),
            'ssl' => __('SSL', 'tt'),
            'tls' => __('TLS', 'tt'),
            'none' => __('None', 'tt')
        )
    );


    // - SMTP 用户名
    $options[] = array(
        'name' => __( 'SMTP Username', 'tt' ),
        'desc' => __( 'Your SMTP username', 'tt' ),
        'id' => 'tt_smtp_username',
        'std' => '',
        'type' => 'text'
    );


    // - SMTP 密码
    $options[] = array(
        'name' => __( 'SMTP Password', 'tt' ),
        'desc' => __( 'Your SMTP password', 'tt' ),
        'id' => 'tt_smtp_password',
        'std' => '',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 你的姓名
    $options[] = array(
        'name' => __( 'Your Name', 'tt' ),
        'desc' => __( 'Your display name as the sender', 'tt' ),
        'id' => 'tt_smtp_name',
        'std' => $blog_name,
        'class' => 'mini',
        'type' => 'text'
    );
    endif;


    if (of_get_option('tt_default_mailer')!=='smtp'):
    // - PHP Mail 发信人姓名
    $options[] = array(
        'name' => __( 'PHP Mail Sender Display Name', 'tt' ),
        'desc' => __( 'The Sender display name when using PHPMail send mail', 'tt' ),
        'id' => 'tt_mail_custom_sender',
        'std' => $blog_name,
        'class' => 'mini',
        'type' => 'text'
    );


    // - PHP Mail 发信人地址
    $options[] = array(
        'name' => __( 'PHP Mail Sender Address', 'tt' ),
        'desc' => __( 'You can use fake mail address when use PHPMail', 'tt' ),
        'id' => 'tt_mail_custom_address',
        'std' => '',
        'placeholder' => 'e.g no-reply@domain.com',
        'type' => 'text'
    );
    endif;


    // - 短链接前缀
    $options[] = array(
        'name' => __( 'Short Link Prefix', 'tt' ),
        'desc' => __( 'Use short link instead long link or even convert external link to internal link', 'tt' ),
        'id' => 'tt_short_link_prefix',
        'std' => 'go',
        'class' => 'mini',
        'type' => 'text'
    );


    // - 短链接记录
    $options[] = array(
        'name' => __( 'Short Link Records', 'tt' ),
        'desc' => __( 'One line for one record, please conform to the sample', 'tt' ),
        'id' => 'tt_short_link_records',
        'std' => 'baidu | http://www.baidu.com' . PHP_EOL,
        'raw' => true,
        'type' => 'textarea'
    );


    // - 开启登录邮件提醒
    $options[] = array(
        'name' => __( 'Login Email Notification', 'tt' ),
        'desc' => __( 'Enable email notification when a successfully login event happened', 'tt' ),
        'id' => 'tt_login_success_notify',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 开启登录错误邮件提醒
    $options[] = array(
        'name' => __( 'Login Failure Email Notification', 'tt' ),
        'desc' => __( 'Enable email notification when a login failure event happened', 'tt' ),
        'id' => 'tt_login_failure_notify',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 启用订单相关邮件提醒
    $options[] = array(
        'name' => __( 'Order Related Notification', 'tt' ),
        'desc' => __( 'Enable order related notifications', 'tt' ),
        'id' => 'tt_order_events_notify',
        'std' => true,
        'type' => 'checkbox'
    );

    // - 启用评论相关邮件提醒
    $options[] = array(
        'name' => __( 'Comment Related Notification', 'tt' ),
        'desc' => __( '启用评论邮件提醒(如果你的邮件发送较慢，将影响评论提交速度)', 'tt' ),
        'id' => 'tt_comment_events_notify',
        'std' => true,
        'type' => 'checkbox'
    );


    // - 主题静态资源CDN路径
    $options[] = array(
        'name' => __('主题静态资源CDN路径', 'tt'),
        'desc' => __('主题程序的JS/CSS/IMG的CDN存放路径URL, css/js/img文件夹位于该路径下, 默认为本站Tint主题assets文件夹的路径, 更改为CDN的assets路径将从CDN加速主题的JS/CSS/IMG文件', 'tt'),
        'id' => 'tt_tint_static_cdn_path',
        'std' => THEME_ASSET,
        'type' => 'text'
    );


    // 主题反馈
  //    $options[] = array(
   //       'name' => __( 'Feedback', 'tt' ),
   //       'type' => 'heading'
   //   );

   //   // 交流论坛
   //   $options[] = array(
   //       'name' => __( '交流论坛', 'tt' ),
   //       'desc' => sprintf(__( '<br><h2><a href="%s" target="_blank">Tint主题/WordPress交流论坛</a></h2>', 'tt'), 'https://elune.me'),
   //       'type' => 'info'
   //   );

  //    // 联系作者
  //    $options[] = array(
  //        'name' => __( 'Contact Author', 'tt' ),
    //      'desc' => sprintf(__( '<br><h2>Email: chinash2010@gmail.com</h2><br><h2>Wechat & Alipay & QQ(below)</h2><br><img src="%s"><img src="%s"><img src="%s"> ', 'tt' ), THEME_ASSET . '/img/qr/weixin.png', THEME_ASSET . '/img/qr/alipay.png', THEME_ASSET . '/img/qr/qq.png'),
   //       'type' => 'info'
  //    );

   //   // 相关作品
   //   $options[] = array(
   //       'name' => __( 'Related Works', 'tt' ),
    //      'desc' => sprintf(__( '<br><h2>Alipay Supervisor (<a href="%s" target="_blank">View Detail</a>)</h2><br><p>A toolkit for helping improve payment experience</p>', 'tt'), TT_SITE . '/shop/apsv.html'),
    //      'type' => 'info'
   //   );

    //  // 相关作品2
    //  $options[] = array(
    //      'name' => "",
    //      'desc' => sprintf(__( '<br><h2>Alipay Supervisor 桌面版 (<a href="%s" target="_blank">查看详情</a>)</h2><br><p>支付宝免签约工具桌面版客户端</p>', 'tt'), TT_SITE . '/shop/apsv-gui.html'),
     //     'type' => 'info'
    //  );


    // 其他 - 主题调试/更新
    //TODO: 版本升级 升级日志
    $options[] = array(
        'name' => __( 'Others', 'tt' ),
        'type' => 'heading'
    );


    // - 开启调试
    $options[] = array(
        'name' => __( 'Debug Mode', 'tt' ),
        'desc' => __( 'Enable debug will force display php errors, disable theme cache, enable some private links or functions, etc.', 'tt' ),
        'id' => 'tt_theme_debug',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 单独暂停缓存
    $options[] = array(
        'name' => __( 'Disable Cache', 'tt' ),
        'desc' => __( 'Stop cache, user always get the latest content', 'tt' ),
        'id' => 'tt_disable_cache',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 主题专用私有Token
    $options[] = array(
        'name' => __('Marketing Token', 'tt'),
        'desc' => __('Private token for theme, maybe useful somewhere.', 'tt'),
        'id' => 'tt_private_token',
        'std' => Utils::generateRandomStr(5),
        'class' => 'mini',
        'type' => 'text'
    );


    // - 刷新固定链接链接
    $options[] = array(
        'name'  =>  __('Refresh Rewrite Rules', 'tt'),
        'desc'  =>  sprintf(__('Please Click to <a href="%1$s/m/refresh?token=%2$s" target="_blank">Refresh Rewrite Rules</a> if you have encounter some 404 errors', 'tt'), $blog_home, of_get_option('tt_private_token')),
        'type'  => 'info'
    );


    // - 登录API后缀
    $options[] = array(
        'name' => __( '登录API后缀', 'tt' ),
        'desc' => __( '请变更默认值降低密码爆破攻击风险', 'tt' ),
        'id' => 'tt_session_api',
        'std' => 'session',
        'type' => 'text'
    );


    // - QQ邮我链接ID
    $options[] = array(
        'name' => __( 'QQ Mailme ID', 'tt' ),
        'desc' => __( 'The id of qq mailme, visit `http://open.mail.qq.com` for detail', 'tt' ),
        'id' => 'tt_mailme_id',
        'std' => '',
        'type' => 'text'
    );


    // - QQ邮件列表ID
    $options[] = array(
        'name' => __( 'QQ Mail list ID', 'tt' ),
        'desc' => __( 'The id of qq mailme, visit `http://list.qq.com` for detail', 'tt' ),
        'id' => 'tt_maillist_id',
        'std' => '',
        'type' => 'text'
    );


    // - Head自定义代码
    $options[] = array(
        'name' => __( 'Head Custom Code', 'tt' ),
        'desc' => __( 'Custom code loaded on page head', 'tt' ),
        'id' => 'tt_head_code',
        'std' => '',
        'type' => 'textarea',
        'raw' => true
    );


    

//其他扩展
   $options[] = array(
        'name' => __( '扩展', 'tt' ),
       'type' => 'heading'
   );

   // - 文章自带下载按钮禁用
    $options[] = array(
        'name' => __( '禁用文章自带下载按钮', 'tt' ),
        'desc' => __( '当在文章侧边栏显示下载信息的时候，建议禁用', 'tt' ),
        'id' => 'tt_disable_post_btn_dw',
        'std' => true,
        'type' => 'checkbox'
    );



   // - 外链转内链
    $options[] = array(
        'name' => __( 'Disable External Links', 'tt' ),
        'desc' => __( 'Convert external links in post content, excerpt or comments to internal links', 'tt' ),
        'id' => 'tt_disable_external_links',
        'std' => false,
        'type' => 'checkbox'
    );


    // - 外链白名单
    $options[] = array(
        'name' => __( 'External Link Whitelist', 'tt' ),
        'desc' => __( 'External links which will not be converted', 'tt' ),
        'id' => 'tt_external_link_whitelist',
        'std' => '',
        'row' => 5,
        'type' => 'textarea'
    );


      // 熊掌号功能
    $options[] = array(
        'name' => __( '熊掌号功能', 'tt' ),
        'desc' => __( '启用熊掌号功能(此选项为熊掌号功能总开关)', 'tt' ),
        'id' => 'tt_enable_k_xzhid',
        'std' => false,
        'type' => 'checkbox'
    );
  // 熊掌号ID
    $options[] = array(
        'name' => __( '熊掌号ID', 'tt' ),
        'desc' => __( '请填写熊掌号ID，到http://ziyuan.baidu.com/xzh/home/index获取', 'tt' ),
        'id' => 'tt_k_id',
        'std' => '熊掌号ID',
        'class' => 'mini',
       'type' => 'text'
    );
        // 熊掌号文章页底部关注功能
    $options[] = array(
        'name' => __( '熊掌号文章页底部关注功能', 'tt' ),
        'desc' => __( '启用熊掌号文章页底部关注功能', 'tt' ),
        'id' => 'tt_enable_k_xzhwzgz',
        'std' => false,
        'type' => 'checkbox'
    );
    // 熊掌号Json_LD数据
    $options[] = array(
        'name' => __( '熊掌号Json_LD数据', 'tt' ),
        'desc' => __( '启用熊掌号Json_LD数据', 'tt' ),
        'id' => 'tt_enable_k_xzhld',
        'std' => false,
        'type' => 'checkbox'
    );
    
             //开启游客评论
    $options[] = array(
        'name' => __( '开启游客评论', 'tt' ),
        'desc' => __( '启用游客评论', 'tt' ),
        'id' => 'tt_enable_k_ykpl',
        'std' => true,
        'type' => 'checkbox'
    );
        
       //开启文章内容自动标签链接
    $options[] = array(
        'name' => __( '开启文章内容自动标签链接', 'tt' ),
        'desc' => __( '启用文章内容自动标签链接（此开关生效时间较长，如未生效请多清理几次缓存）', 'tt' ),
        'id' => 'tt_enable_k_post_tag_link',
        'std' => true,
        'type' => 'checkbox'
    );
          //开启免登录下载免费附件
    $options[] = array(
        'name' => __( '开启免登录下载文章免费附件', 'tt' ),
        'desc' => __( '启用免登录下载文章免费附件', 'tt' ),
        'id' => 'tt_enable_k_nologindownload',
        'std' => false,
        'type' => 'checkbox'
    );

	///////////////////////////////////////////////////////////////////////////


	return $options;
}