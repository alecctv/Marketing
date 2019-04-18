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
<!-- 判断是否开启公告 -->
<?php if(tt_get_option('tt_enable_homepage_bulletins')) { ?>
<?php $now_stamp = time(); $close_time = isset($_COOKIE['tt_close_bulletins']) ? intval($_COOKIE['tt_close_bulletins']) : 0; if($now_stamp - $close_time < 3600*24) return; ?>
<?php $vm = HomeBulletinsVM::getInstance(); ?>
<?php $data = $vm->modelData; $count = $data->count; $bulletins = $data->bulletins; ?>
<?php if($count > 0 && $bulletins) { ?>

<section id="home-information">
    <div class="information-bar">
        <div class="slide-container">
          <ul class="js-slide-list">
              <?php foreach ($bulletins as $bulletin) { ?>
              <li class="information-bar__inner bulletin">
                  <div class="information-bar__text">
                       <span class="information-baricon"><i class="tico tico-bullhorn2"></i></span>
                       <?php printf('<span>[%1$s]</span> %2$s', $bulletin['modified'], $bulletin['title']); ?>
                  </div>
                  <a href="<?php echo $bulletin['permalink']; ?>" target="_blank" rel="nofollow">查看详情</a>
              </li>
              <?php } ?>
          </ul>
        </div>
    </div>
</section>

<script type="text/javascript">
var doscroll = function() {
  var $parent = $('.js-slide-list');
  var $first = $parent.find('li:first');
  var height = $first.height();
  $first.animate({
    marginTop: -height + 'px'
  }, 500, function() {
    $first.css('marginTop', 0).appendTo($parent);
  });
};
setInterval(function() {
  doscroll()
}, 10000);
</script>
<?php } ?>

<?php } else { ?>
<section id="home-information">
    <div class="information-bar">
        <div class="slide-container">
          <ul class="js-slide-list">
              <li class="information-bar__inner bulletin">
                  <div class="information-bar__text">
                       <span class="information-baricon"><i class="tico tico-bullhorn2"></i></span>
                       本站正式上线！请到后台新建公告文章，将在这里显示！
                  </div>
                  <a href="#" target="_blank" rel="nofollow">查看详情</a>
              </li>
          </ul>
        </div>
    </div>
</section>
<?php } ?>