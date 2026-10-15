<?php
defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;

require dirname(__DIR__) . '/_common.php';
?>
<div class="giftreg">
	<?php echo giftregNav(''); ?>
	<h2 class="h4"><?php echo $this->escape($this->person->name); ?>'s list</h2>
	<?php if (!$this->items) : ?>
		<p>Nothing on this list yet.</p>
	<?php else : ?>
		<table class="table table-sm align-middle">
			<thead><tr><th>Item</th><th>Wants it</th><th>Price</th><th>Where</th><th></th></tr></thead>
			<?php foreach ($this->items as $i) :
				$mine = (int) $i->shopper_id === $this->me;
				$taken = $i->shopper_id && !$mine; ?>
				<tr class="<?php echo $taken ? 'text-muted' : ''; ?>">
					<td><?php echo giftregTitle($i); ?>
						<?php if ($i->comment) : ?><br><small><?php echo nl2br($this->escape($i->comment)); ?></small><?php endif; ?></td>
					<td><?php echo giftregStars((int) $i->ranking); ?></td>
					<td><?php echo giftregPrice($i->price); ?></td>
					<td><?php echo $this->escape($i->source); ?></td>
					<td class="text-nowrap">
						<?php if ($taken) : ?>
							<i><?php echo $i->bought ? 'Bought' : 'Reserved'; ?> by <?php echo $this->escape($i->shopper); ?>.</i>
						<?php elseif ($mine) : ?>
							<i><b><?php echo $i->bought ? 'Bought' : 'Reserved'; ?> by you.</b></i>
							<?php if ($i->bought) : echo giftregButton('release', ['item' => $i->id], 'Return Item', '', '', 'return.png');
							else : echo giftregButton('release', ['item' => $i->id], 'Release Item', '', '', 'unlocked.png');
								echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', '', '', 'credit-card-3.png'); endif; ?>
						<?php else : ?>
							<i>Available.</i>
							<?php echo giftregButton('reserve', ['item' => $i->id], 'Reserve Item', '', '', 'locked.png'); ?>
							<?php echo giftregButton('buy', ['item' => $i->id], 'Purchase Item', '', '', 'credit-card-3.png'); ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<div class="alert alert-info">
			<?php echo giftregIcon('locked.png', 'Reserve'); ?> = Reserve, <?php echo giftregIcon('unlocked.png', 'Release'); ?> = Release,
			<?php echo giftregIcon('credit-card-3.png', 'Purchase'); ?> = Purchase, <?php echo giftregIcon('return.png', 'Return'); ?> = Return
		</div>
	<?php endif; ?>
</div>
