<?php
defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\HTML\HTMLHelper;
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
			<?php echo giftregButton('dismissNotices', [], 'Acknowledge', '', '', 'delete.png'); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->upcoming) : ?>
		<h2 class="h4">Coming up</h2>
		<ul>
			<?php foreach ($this->upcoming as $e) : ?>
				<li><?php echo $this->escape(($e->name ? $e->name . ': ' : '') . $e->description); ?>,
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
						<a href="<?php echo Route::_('index.php?option=com_giftreg&view=shop&id=' . (int) $p->id); ?>"><?php echo giftregIcon('store.png', 'Shop for ' . $p->name); ?></a>
					<?php endif; ?>
				</td>
				<td class="text-end"><?php echo (int) $p->items; ?> item<?php echo $p->items == 1 ? '' : 's'; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h2 class="h4" id="dates">Family dates</h2>
	<p class="text-muted">Anyone can add or remove these. Each shows under Coming up in the <?php echo Registry::EVENT_DAYS; ?> days before.</p>
	<?php if ($this->events) : ?>
		<ul>
			<?php foreach ($this->events as $ev) : ?>
				<li><?php echo $this->escape($ev->description); ?>,
					<?php echo HTMLHelper::_('date', $ev->eventdate, $ev->recurring ? 'F j' : 'F j, Y', null); ?>
					<?php echo $ev->recurring ? '(every year)' : ''; ?>
					<?php echo giftregButton('deleteEvent', ['id' => $ev->id], 'Delete Event', '', 'Remove this date?', 'bin.png'); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<form method="post" action="<?php echo Route::_('index.php?option=com_giftreg'); ?>" class="row g-2 align-items-end">
		<div class="col-md-5"><label class="form-label">What</label><input class="form-control" name="description" required maxlength="100" placeholder="Birthday"></div>
		<div class="col-md-3"><label class="form-label">Date</label><input class="form-control" name="eventdate" type="date" required></div>
		<div class="col-md-2 form-check ms-2"><input class="form-check-input" type="checkbox" name="recurring" value="1" checked id="gr-rec">
			<label class="form-check-label" for="gr-rec">Every year</label></div>
		<div class="col-md-1">
			<input type="hidden" name="task" value="saveEvent"><?php echo HTMLHelper::_('form.token'); ?>
			<button class="btn btn-primary">Add</button></div>
	</form>
</div>
