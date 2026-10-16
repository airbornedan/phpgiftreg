<?php
defined('_JEXEC') or die;

require dirname(__DIR__) . '/_common.php';
$id = '&id=' . (int) $this->person->id;
foreach ($this->items as $i) {
	$mine      = (int) $i->shopper_id === $this->me;
	$i->status = !$i->shopper_id ? 'Available.' : ($i->bought ? 'Bought by ' : 'Reserved by ') . ($mine ? 'you.' : $i->shopper . '.');
}
$items = giftregSorted($this->items, ['ranking' => 'ranking', 'description' => 'description', 'category' => 'category',
	'price' => 'price', 'source' => 'source', 'status' => 'status']);
$th = fn ($key, $label) => '<th>' . giftregSortLink('shop', $id, $key, $label) . '</th>';
?>
<div class="giftreg">
	<?php echo giftregNav(''); ?>
	<h1>Shopping List for <?php echo $this->escape($this->person->name); ?></h1>
	<div class="gr-row">
		<div class="gr-offset6">
			<div class="gr-alert gr-alert-info">
				<?php echo giftregIcon('locked.png', 'Reserve'); ?> = Reserve, <?php echo giftregIcon('unlocked.png', 'Release'); ?> = Release,
				<?php echo giftregIcon('credit-card-3.png', 'Purchase'); ?> = Purchase, <?php echo giftregIcon('return.png', 'Return'); ?> = Return
			</div>
		</div>
	</div>
	<div class="gr-well">
		<table class="gr-table gr-table-bordered gr-table-striped">
			<thead>
				<tr>
					<?php echo $th('ranking', 'Rank') . $th('description', 'Description') . $th('category', 'Category')
						. $th('price', 'Price') . $th('source', 'Store/Location') . $th('status', 'Status'); ?>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($items as $i) : $mine = (int) $i->shopper_id === $this->me; ?>
					<tr valign="top">
						<td nowrap><?php echo giftregStars((int) $i->ranking); ?></td>
						<td><?php echo giftregTitle($i); ?></td>
						<td><?php echo $this->escape($i->category); ?></td>
						<td align="right"><?php echo giftregPrice($i->price); ?></td>
						<td><?php echo giftregSource($i); ?></td>
						<td><i><?php echo $mine ? '<b>' . $this->escape($i->status) . '</b>' : $this->escape($i->status); ?></i></td>
						<td align="right" nowrap>
							<?php if (!$i->shopper_id) : ?>
								<?php echo giftregButton('reserve', ['item' => $i->id], 'Reserve Item', 'locked.png'); ?>
								<?php echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', 'credit-card-3.png'); ?>
							<?php elseif ($mine && $i->bought) : ?>
								<?php echo giftregButton('release', ['item' => $i->id], 'Return Item', 'return.png'); ?>
							<?php elseif ($mine) : ?>
								<?php echo giftregButton('release', ['item' => $i->id], 'Release Item', 'unlocked.png'); ?>
								<?php echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', 'credit-card-3.png'); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="gr-row gr-noprint">
		<div class="gr-span6"><div class="gr-well"><a href="#" onclick="window.print();return false;">Send to printer</a></div></div>
		<div class="gr-span6"></div>
	</div>
</div>
