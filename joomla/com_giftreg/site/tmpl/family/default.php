<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

require dirname(__DIR__) . '/_common.php';
?>
<div class="giftreg">
	<?php echo giftregNav('family'); ?>

	<?php if ($this->notices) : ?>
		<div class="alert alert-warning">
			<?php foreach ($this->notices as $n) : ?>
				<p class="mb-1"><?php echo $this->escape($n->message); ?></p>
			<?php endforeach; ?>
			<?php echo giftregButton('dismissNotices', [], 'Got it', 'btn-warning'); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->upcoming) : ?>
		<h2 class="h4">Coming up</h2>
		<ul>
			<?php foreach ($this->upcoming as $e) : ?>
				<li><?php echo $this->escape($e->name . ': ' . $e->description); ?>,
					<?php echo $e->next->format('M j'); ?>
					(<?php echo $e->days === 0 ? 'today' : ($e->days === 1 ? 'tomorrow' : 'in ' . $e->days . ' days'); ?>)</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<h2 class="h4">Lists</h2>
	<table class="table table-sm">
		<?php foreach ($this->people as $p) : ?>
			<tr>
				<td>
					<?php if ((int) $p->id === $this->me) : ?>
						<a href="<?php echo Route::_('index.php?option=com_giftreg&view=mylist'); ?>"><?php echo $this->escape($p->name); ?></a> (you)
					<?php else : ?>
						<a href="<?php echo Route::_('index.php?option=com_giftreg&view=shop&id=' . (int) $p->id); ?>"><?php echo $this->escape($p->name); ?></a>
					<?php endif; ?>
				</td>
				<td class="text-end"><?php echo (int) $p->items; ?> item<?php echo $p->items == 1 ? '' : 's'; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
