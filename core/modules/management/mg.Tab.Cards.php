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
<?php global $tt_mg_vars; $tt_user_id = $tt_mg_vars['tt_user_id']; $tt_page = $tt_mg_vars['tt_paged']; ?>
<div class="col col-right cards">
	<?php $vm = MgCardsVM::getInstance($tt_page); ?>
	<?php if($vm->isCache && $vm->cacheTime) { ?>
		<!-- Manage cards cached <?php echo $vm->cacheTime; ?> -->
	<?php } ?>
	<?php $data = $vm->modelData; $cards = $data->cards; $count = $data->count; $max_pages = $data->max_pages; ?>
	<div class="mg-tab-box cards-tab">
		<div class="tab-content">
			<!-- 添加卡券 -->
			<section class="mg-card clearfix">
				<header><h2><?php _e('Add Card', 'tt'); ?></h2></header>
				<div class="form-group info-group clearfix">
					<div class="form-inline">
						<div class="form-group">
							<div class="input-group active">
								<div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;"><?php _e('Quantity', 'tt'); ?></div>
								<input class="form-control" type="number" name="card_quantity" value="10" aria-required="true" required>
							</div>
							<div class="input-group active">
								<div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;"><?php _e('Denomination(Unit: Cent)', 'tt'); ?></div>
								<input class="form-control" type="number" min="1" name="card_denomination" value="100" aria-required="true" required>
							</div>
							<div class="input-group active">
								<div class="input-group-addon" style="background-color: #788b90;border-color: #788b90;"><?php _e('Delimiter', 'tt'); ?></div>
								<input class="form-control" type="text" name="card_code_delimiter" value="|" aria-required="true" required>
							</div>
						</div>
						<button class="btn btn-inverse" type="submit" id="generate-cards"><?php _e('GENERATE', 'tt'); ?></button>
					</div>
					<p class="help-block"><?php _e('一次生成数量100以内，卡号卡密分隔符请遵循卡券平台的导入格式要求', 'tt'); ?></p>
				</div>
				<div id="genCards">
					<div class="list"></div>
					<button class="btn btn-primary" id="download-cards"><?php _e('DOWNLOAD', 'tt'); ?></button>
				</div>
			</section>
			<!-- 卡券列表 -->
			<section class="mg-cards clearfix">
				<header><h2><?php _e('Cards List', 'tt'); ?></h2></header>
				<?php if($count > 0) { ?>
					<div class="table-wrapper">
						<table class="table table-striped table-framed table-centered">
							<thead>
							<tr>
								<th class="th-cid"><?php _e('Card Sequence', 'tt'); ?></th>
								<th class="th-code"><?php _e('Card No.', 'tt'); ?></th>
								<th class="th-type"><?php _e('Card Secret', 'tt'); ?></th>
								<th class="th-discount"><?php _e('Denomination', 'tt'); ?></th>
								<th class="th-status"><?php _e('Status', 'tt'); ?></th>
								<th class="th-effect"><?php _e('Create Time', 'tt'); ?></th>
								<th class="th-actions"><?php _e('Actions', 'tt'); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php $seq = 0; ?>
							<?php foreach ($cards as $card){ ?>
								<?php $seq++; ?>
								<tr id="cid-<?php echo $card->id; ?>">
									<td><?php echo $seq; ?></td>
									<td><?php echo $card->card_id; ?></td>
									<td><?php echo $card->card_secret; ?></td>
									<td><?php echo sprintf(__('%0.2f YUAN', 'tt'), $card->denomination / 100); ?></td>
									<td><?php if($card->status == 1){_e('Not Used', 'tt');}else{_e('Used', 'tt');} ?></td>
									<td><?php echo $card->create_time ?></td>
									<td>
										<div class="card-actions">
											<a class="delete-card" href="javascript:;" data-card-action="delete" data-card-id="<?php echo $card->id; ?>" title="<?php _e('Delete the card', 'tt'); ?>"><?php _e('Delete', 'tt'); ?></a>
										</div>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
					<?php if($max_pages > 1) { ?>
						<div class="pagination-mini clearfix">
							<?php if($tt_page == 1) { ?>
								<div class="col-md-3 prev disabled"><a href="javascript:;"><?php _e('← 上一页', 'tt'); ?></a></div>
							<?php }else{ ?>
								<div class="col-md-3 prev"><a href="<?php echo $data->prev_page; ?>"><?php _e('← 上一页', 'tt'); ?></a></div>
							<?php } ?>
							<div class="col-md-6 page-nums">
								<span class="current-page"><?php printf(__('Current Page %d', 'tt'), $tt_page); ?></span>
								<span class="separator">/</span>
								<span class="max-page"><?php printf(__('Total %d Pages', 'tt'), $max_pages); ?></span>
							</div>
							<?php if($tt_page != $data->max_pages) { ?>
								<div class="col-md-3 next"><a href="<?php echo $data->next_page; ?>"><?php _e('下一页 →', 'tt'); ?></a></div>
							<?php }else{ ?>
								<div class="col-md-3 next disabled"><a href="javascript:;"><?php _e('下一页 →', 'tt'); ?></a></div>
							<?php } ?>
						</div>
					<?php } ?>
				<?php }else{ ?>
					<div class="empty-content">
						<span class="tico tico-ticket"></span>
						<p><?php _e('Nothing found here', 'tt'); ?></p>
						<!--                        <a class="btn btn-info" href="/">--><?php //_e('Back to home', 'tt'); ?><!--</a>-->
					</div>
				<?php } ?>
			</section>
		</div>
	</div>
</div>
