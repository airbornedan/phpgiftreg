<?php
defined('_JEXEC') or die;

require dirname(__DIR__) . '/_common.php';
?>
<div class="giftreg">
	<?php echo giftregNav('shoplist'); ?>
	<?php if (!$this->items) : ?>
		<p>You haven't reserved anything yet.</p>
	<?php else : ?>
		<table class="table table-sm align-middle">
			<thead><tr><th>For</th><th>Item</th><th>Price</th><th>Where</th><th></th></tr></thead>
			<?php foreach ($this->items as $i) : ?>
				<tr>
					<td><?php echo $this->escape($i->owner); ?></td>
					<td><?php echo giftregTitle($i); ?></td>
					<td><?php echo giftregPrice($i->price); ?></td>
					<td><?php echo giftregSource($i); ?></td>
					<td class="text-nowrap">
						<?php if ($i->bought) : ?>
							<i>Bought.</i> <?php echo giftregButton('release', ['item' => $i->id], 'Return Item', '', '', 'return.png'); ?>
						<?php else : ?>
							<i>Reserved.</i>
							<?php echo giftregButton('release', ['item' => $i->id], 'Release Item', '', '', 'unlocked.png'); ?>
							<?php echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', '', '', 'credit-card-3.png'); ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>
