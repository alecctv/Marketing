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
 * Class CreditsRank
 */
class CreditsRank extends WP_Widget {
    function __construct() {
        parent::__construct(false, __('TT-User Credits Rank', 'tt'), array( 'description' => __('TT-Show user credits rank', 'tt') ,'classname' => 'widget_credits-rank wow bounceInRight'));
    }

    function widget($args, $instance) {
        $vm = WidgetCreditsRankVM::getInstance($instance['num']);
        if($vm->isCache && $vm->cacheTime) {
            echo '<!-- Credits rank widget cached ' . $vm->cacheTime . ' -->';
        }
        $rank_items = $vm->modelData;
        ?>
        <?php echo $args['before_widget']; ?>
        <?php if($instance['title']) { echo $args['before_title'] . $instance['title'] . $args['after_title']; } ?>
        <div class="widget-content">
            <ul>
                <?php foreach ($rank_items as $rank_item) { ?>
                    <li>
                        <span class="index">
                            <?php if ($rank_item->index == 1) { ?>
                                <img src="<?php echo THEME_ASSET.'/img/rank/Rank-1.png'; ?>">
                             <?php } else if ($rank_item->index == 2) { ?>
                                <img src="<?php echo THEME_ASSET.'/img/rank/Rank-2.png'; ?>">
                             <?php } else if ($rank_item->index == 3) { ?>
                                <img src="<?php echo THEME_ASSET.'/img/rank/Rank-3.png'; ?>">
                             <?php } else { 
                                echo '<span class="num">' . $rank_item->index . '.</span>';
                              } ?>
                        </span>
                        <span class="avatar"><img src="<?php echo $rank_item->avatar; ?>" /></span>
                        <span class="name"><a href="<?php echo $rank_item->link; ?>"><?php echo $rank_item->display_name; ?></a></span>
                        <span class="credits"><span class="num"><?php echo $rank_item->credits; ?></span><?php _e('CREDITS', 'tt'); ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php echo $args['after_widget']; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
        // TODO 清除小工具缓存

        return $new_instance;
    }

    function form($instance) {
        $title = esc_attr(isset($instance['title']) ? $instance['title'] : __('CREDITS RANK', 'tt'));
        $num = absint(isset($instance['num']) ? $instance['num'] : 5);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title：','tt'); ?><input class="input-lg" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number：','tt'); ?></label><input class="input-lg" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text"  value="<?php echo $num; ?>" /></p>
        <?php
    }
}

/* 注册小工具 */
if ( ! function_exists( 'tt_register_widget_credits_rank' ) ) {
    function tt_register_widget_credits_rank() {
        register_widget( 'CreditsRank' );
    }
}
add_action( 'widgets_init', 'tt_register_widget_credits_rank' );
