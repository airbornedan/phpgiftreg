<?php
defined('_JEXEC') or die;

require dirname(__DIR__) . '/_common.php';
$items = giftregSorted($this->items, ['recipient' => 'owner', 'description' => 'description', 'ranking' => 'ranking',
	'source' => 'source', 'price' => 'price']);
$th    = fn ($key, $label) => '<th>' . giftregSortLink('shoplist', '', $key, $label) . '</th>';
$total = array_sum(array_map(fn ($i) => (float) $i->price, $items));
?>
<div class="giftreg">
	<?php echo giftregNav('shoplist'); ?>
	<div class="gr-well">
		<h1>My Shopping List</h1>
		<table class="gr-table gr-table-bordered gr-table-striped">
			<thead>
				<tr>
					<?php echo $th('recipient', 'Recipient') . $th('description', 'Description') . $th('ranking', 'Ranking')
						. $th('source', 'Source') . $th('price', 'Price'); ?>
					<th class="gr-noprint">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($items as $i) : ?>
					<tr>
						<td><?php echo $this->escape($i->owner); ?></td>
						<td><?php echo giftregTitle($i); ?></td>
						<td nowrap><?php echo giftregStars((int) $i->ranking); ?></td>
						<td><?php echo giftregSource($i); ?></td>
						<td align="right"><?php echo giftregPrice($i->price); ?></td>
						<td align="right" nowrap class="gr-noprint">
							<?php if ($i->bought) : ?>
								<?php echo giftregButton('release', ['item' => $i->id], 'Return Item', 'return.png'); ?>
							<?php else : ?>
								<?php echo giftregButton('release', ['item' => $i->id], 'Release Item', 'unlocked.png'); ?>
								<?php echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', 'credit-card-3.png'); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<h5><?php echo count($items); ?> item(s), <?php echo giftregPrice($total); ?> total.</h5>
	</div>
	<div class="gr-row gr-noprint">
		<div class="gr-span6"><div class="gr-well"><a href="#" onclick="window.print();return false;">Send to printer</a></div></div>
		<div class="gr-span6"></div>
	</div>
</div>
