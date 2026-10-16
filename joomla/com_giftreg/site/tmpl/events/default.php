<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

require dirname(__DIR__) . '/_common.php';
?>
<div class="giftreg">
	<?php echo giftregNav('events'); ?>
	<div class="gr-well">
		<h1>Events</h1>
		<table class="gr-table gr-table-bordered gr-table-striped">
			<thead>
				<tr><th>Event date</th><th>Description</th><th>Recurring?</th><th>&nbsp;</th></tr>
			</thead>
			<tbody>
				<?php foreach ($this->events as $ev) : ?>
					<tr>
						<td><?php echo HTMLHelper::_('date', $ev->eventdate, 'm/d/Y', null); ?></td>
						<td><?php echo $this->escape($ev->description); ?></td>
						<td><?php echo $ev->recurring ? 'Yes' : 'No'; ?></td>
						<td align="right" nowrap><?php echo giftregButton('deleteEvent', ['id' => $ev->id], 'Delete Event', 'bin.png', 'Delete "' . $ev->description . '"?'); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<h5><a href="#eventform">Add a new event</a></h5>
	</div>
	<div class="gr-span8">
		<form id="eventform" method="post" action="<?php echo Route::_('index.php?option=com_giftreg'); ?>" class="gr-well gr-form-horizontal">
			<fieldset>
				<legend>Event Details</legend>
				<div class="gr-control-group">
					<label class="gr-control-label" for="description">Description</label>
					<div class="gr-controls"><input id="description" name="description" type="text" class="gr-input-xlarge" placeholder="Description" maxlength="100" required></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="eventdate">Event date</label>
					<div class="gr-controls"><input id="eventdate" name="eventdate" type="date" class="gr-input-xlarge" required></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="recurring">Recurring</label>
					<div class="gr-controls"><label class="gr-checkbox"><input id="recurring" name="recurring" type="checkbox" value="1" checked>Recurring yearly</label></div>
				</div>
				<div class="gr-form-actions">
					<input type="hidden" name="task" value="saveEvent">
					<?php echo HTMLHelper::_('form.token'); ?>
					<button type="submit" class="gr-btn gr-btn-primary">Add</button>
					<a class="gr-btn" href="<?php echo giftregPage('family'); ?>">Cancel</a>
				</div>
			</fieldset>
		</form>
	</div>
</div>
