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
<?php $vm = SlideVM::getInstance(); ?>
<?php if($vm->isCache && $vm->cacheTime) { ?>
<!-- Slide cached <?php echo $vm->cacheTime; ?> -->
<?php } ?>


<?php if($data = $vm->modelData) { ?>

<div class="hero hero--animate" style="height: 850px">
	
	<div class="hero__image" style="background-image: url('<?php echo tt_get_option('tt_home_hero_bg', true)?>');">
		<div class="hero__image__overlay">
		</div>
		<!-- /.hero__image__overlay -->
	</div>
	<!-- /.hero__image -->
	<div class="hero__inner">
		<div class="hero__content">
			<h1><?php echo tt_get_option('home_banner_title');?></h1>
			<h2><?php echo tt_get_option('home_banner_dese');?></h2>
			<ul class="hero__categories">
				<?php 
					$inner = '';  $sort = '1 2 3 4 5'; $sort = array_unique(explode(' ', trim($sort)));  $i = 0;
				    foreach ($sort as $key => $value) {
				        if( tt_get_option('home_banner_img_'.$value) && tt_get_option('home_banner_title_'.$value) && tt_get_option('home_banner_href_'.$value)){

				            $inner .= '<li><a href="'.tt_get_option('home_banner_href_'.$value).'"><img src="'.tt_get_option('home_banner_img_'.$value).'" ><span>'.tt_get_option('home_banner_title_'.$value).'</span></a></li>';

				            $i++;
				        }
				    }
				    echo ''.$inner.''; 
			    ?>
			</ul>
			<!-- /.hero_categories -->
			<a href="<?php echo tt_get_option('home_banner_btn_href');?>" class="hero__btn">
						<?php echo tt_get_option('home_banner_btn_title');?>					
			</a><!-- /.hero__btn -->
		</div>
		<!-- /.hero__content -->
	</div>
	<!-- /.hero__inner -->
</div>

<?php } ?>