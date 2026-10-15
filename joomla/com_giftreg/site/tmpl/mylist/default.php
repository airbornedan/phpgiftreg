<?php
defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

require dirname(__DIR__) . '/_common.php';
$e = $this->edit;
$v = fn ($field, $default = '') => $this->escape($e->$field ?? $default);
?>
<div class="giftreg">
	<?php echo giftregNav('mylist'); ?>

	<h2 class="h4">My list</h2>
	<?php if ($this->items) : ?>
		<table class="table table-sm align-middle">
			<thead><tr><th>Item</th><th>Wants it</th><th>Price</th><th>Where</th><th></th></tr></thead>
			<?php foreach ($this->items as $i) : ?>
				<tr>
					<td><?php echo giftregTitle($i); ?>
						<?php if ($i->comment) : ?><br><small><?php echo nl2br($this->escape($i->comment)); ?></small><?php endif; ?></td>
					<td><small><?php echo Registry::RANKS[$i->ranking] ?? ''; ?></small></td>
					<td><?php echo giftregPrice($i->price); ?></td>
					<td><?php echo $this->escape($i->source); ?></td>
					<td class="text-nowrap">
						<a class="btn btn-sm btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_giftreg&view=mylist&edit=' . (int) $i->id); ?>#item-form">Edit</a>
						<?php echo giftregButton('deleteItem', ['id' => $i->id], 'Delete', 'btn-outline-danger', 'Delete this item?'); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p>Your list is empty. Add something below.</p>
	<?php endif; ?>

	<h3 class="h5" id="item-form"><?php echo $e ? 'Edit item' : 'Add an item'; ?></h3>
	<form method="post" action="<?php echo Route::_('index.php?option=com_giftreg'); ?>" class="row g-2 mb-4">
		<div class="col-md-6"><label class="form-label">What</label>
			<input class="form-control" name="description" required maxlength="255" value="<?php echo $v('description'); ?>"></div>
		<div class="col-md-2"><label class="form-label">Price</label>
			<input class="form-control" name="price" inputmode="decimal" value="<?php echo $v('price'); ?>"></div>
		<div class="col-md-4"><label class="form-label">Where to get it</label>
			<input class="form-control" name="source" maxlength="255" value="<?php echo $v('source'); ?>"></div>
		<div class="col-md-6"><label class="form-label">Link</label>
			<input class="form-control" name="url" type="url" maxlength="1024" value="<?php echo $v('url'); ?>"></div>
		<div class="col-md-3"><label class="form-label">How much I want it</label>
			<select class="form-select" name="ranking">
				<?php foreach (Registry::RANKS as $r => $label) : ?>
					<option value="<?php echo $r; ?>" <?php echo (int) ($e->ranking ?? 3) === $r ? 'selected' : ''; ?>><?php echo $r . ' - ' . $label; ?></option>
				<?php endforeach; ?>
			</select></div>
		<div class="col-md-3"><label class="form-label">Category</label>
			<select class="form-select" name="category_id"><option value="0"></option>
				<?php foreach ($this->categories as $id => $title) : ?>
					<option value="<?php echo $id; ?>" <?php echo (int) ($e->category_id ?? 0) === $id ? 'selected' : ''; ?>><?php echo $this->escape($title); ?></option>
				<?php endforeach; ?>
			</select></div>
		<div class="col-12"><label class="form-label">Notes (size, colour...)</label>
			<textarea class="form-control" name="comment" rows="2"><?php echo $v('comment'); ?></textarea></div>
		<div class="col-12">
			<input type="hidden" name="task" value="saveItem">
			<input type="hidden" name="id" value="<?php echo (int) ($e->id ?? 0); ?>">
			<?php echo HTMLHelper::_('form.token'); ?>
			<button class="btn btn-primary"><?php echo $e ? 'Save' : 'Add'; ?></button>
			<?php if ($e) : ?><a class="btn btn-link" href="<?php echo Route::_('index.php?option=com_giftreg&view=mylist'); ?>">Cancel</a><?php endif; ?>
		</div>
	</form>

</div>
