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
<!-- Footer -->
<footer class="footer simple-footer" style=" text-align: center; ">
    <div class="foot-copyright">&copy;&nbsp;<?php echo tt_copyright_year(); ?><?php echo ' ' . get_bloginfo('name') . ' All Right Reserved · <b style="color: #ff4425;"></b>&nbsp;<a href="https://yunsheji.cc/" title="云设计" rel="link" target="_blank">云设计</a> & Design by <a href="https://yunsheji.cc/" rel="link" title="云设计">云设计.</a>'; ?>
    </div>
</footer>

<script>POWERMODE.colorful = true;POWERMODE.shake = false;document.body.addEventListener('input', POWERMODE);</script>
<?php if(tt_get_option('tt_foot_code')) { echo tt_get_option('tt_foot_code'); } ?>
<?php wp_footer(); ?>
<!--<?php echo get_num_queries();?> queries in <?php timer_stop(1); ?> seconds.-->
</body>
</html>