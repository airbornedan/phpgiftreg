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
					<td><small><?php echo Registry::RANKS[$i->ranking] ?? ''; ?></small></td>
					<td><?php echo giftregPrice($i->price); ?></td>
					<td><?php echo $this->escape($i->source); ?></td>
					<td class="text-nowrap">
						<?php if ($taken) : ?>
							<?php echo $i->bought ? 'Bought' : 'Reserved'; ?> by <?php echo $this->escape($i->shopper); ?>
						<?php elseif ($mine) : ?>
							<?php if (!$i->bought) : echo giftregButton('buy', ['item' => $i->id], 'I bought it', 'btn-success'); endif; ?>
							<?php echo giftregButton('release', ['item' => $i->id], $i->bought ? 'Undo bought' : 'Release'); ?>
						<?php else : ?>
							<?php echo giftregButton('reserve', ['item' => $i->id], 'Reserve', 'btn-primary'); ?>
							<?php echo giftregButton('buy', ['item' => $i->id], 'Bought'); ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>
