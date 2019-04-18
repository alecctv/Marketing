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

/**
 * Class Postmeta
 */
class Postmeta extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'widget_text widget_postmeta', 'description' => __('文章资源详细信息，下载按钮等', 'tt'));
        $control_ops = array('width' => 400);
        parent::__construct('Postmeta', __('TT-文章资源信息', 'tt'), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = null;
        }

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $titleText = empty($instance['titleText']) ? '' : $instance['titleText'];
        $hideBtn = !empty($instance['hideBtn']) ? true : false;


        echo $before_widget;


        if($title) {
            echo $before_title . $title . $after_title;
        }
        // widget fun
        global $post;
        $postmeta_demo = get_post_meta( $post->ID, 'tt_postmeta_demo', true ); //演示地址
        $postmeta_time = get_the_modified_time(get_the_time('Y-m-d G:i:s')); //演示地址
        $postmeta_ver = get_post_meta( $post->ID, 'tt_postmeta_ver', true );  // 版本
        $postmeta_type = get_post_meta( $post->ID, 'tt_postmeta_type', true ); // 文件类型
        $postmeta_size = get_post_meta( $post->ID, 'tt_postmeta_size', true ); // 大小
        $postmeta_view = absint(get_post_meta( $post->ID, 'views', true )); // 查看量

        // 相关下载
        $free_download = get_post_meta($post->ID, 'tt_free_dl', true);
        $sale_download1 = get_post_meta($post->ID, 'tt_sale_dl', true);
        $sale_download2 = get_post_meta($post->ID, 'tt_sale_dl2', true);
        if(!empty($free_download) || !empty($sale_download1) || !empty($sale_download2)) {
            $download_url = tt_url_for('download', $post->ID);
        }

        // widget content
        echo '<div class="widget-content info">';
        ?>
        
        <?php if(isset($download_url) && $download_url) { ?>
            <a class="btn btn-download" href="<?php echo $download_url; ?>" target="_blank"><?php _e('点击下载', 'tt'); ?></a>
        <?php } ?>

        <?php if ($postmeta_demo) { ?>
            <a class="btn btn-demo" href="<?php echo $postmeta_demo; ?>" target="_blank"><?php _e('演示地址', 'tt'); ?></a>
        <?php }?>
        <table id="isa-edd-specs">
                  <tbody>

                    <tr>
                      <td><font>最近更新：</font></td>
                      <td><font><?php echo $postmeta_time; ?></font></td>
                    </tr>

                    <?php if($postmeta_ver){ ?>
                    <tr><td><font>当前版本：</font></td>
                        <td><font><?php echo $postmeta_ver; ?></font></td>
                    </tr>
                    <?php } ?>

                    <?php if($postmeta_type){ ?>
                    <tr>
                      <td><font>文件格式：</font></td>
                      <td><font><?php echo $postmeta_type; ?></font></td>
                    </tr>
                    <?php } ?>

                    <?php if($postmeta_size){ ?>
                    <tr>
                      <td><font>文件大小：</font> </td>
                      <td><font><?php echo $postmeta_size; ?></font> </td>
                    </tr>
                    <?php } ?>
                    
                    <?php if($postmeta_view){ ?>
                    <tr>
                      <td><font>热度：</font></td>
                      <td><font><?php echo $postmeta_view; ?></font></td>
                    </tr>
                    <?php } ?>
                  
                    
                  </tbody>
        </table>

        
        
        <?php
        echo '</div>' . $after_widget;

    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => ''
        ));
        $title = $instance['title'];
        ?>

        <style>
            .monospace {
                font-family: Consolas, Lucida Console, monospace;
            }
            .etw-credits {
                font-size: 0.9em;
                background: #F7F7F7;
                border: 1px solid #EBEBEB;
                padding: 4px 6px;
            }
        </style>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'tt'); ?>:</label>
            <input class="input-lg" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
       
        <?php
    }
}

/* 注册小工具 */
if ( ! function_exists( 'tt_register_widget_postmeta' ) ) {
    function tt_register_widget_postmeta() {
        register_widget( 'Postmeta' );
    }
}
add_action( 'widgets_init', 'tt_register_widget_postmeta' );