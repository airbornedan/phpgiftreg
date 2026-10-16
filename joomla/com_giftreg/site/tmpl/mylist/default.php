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
	<?php echo giftregNav(''); ?>
	<div class="gr-span8">
		<form method="post" action="<?php echo Route::_('index.php?option=com_giftreg'); ?>" class="gr-well gr-form-horizontal">
			<fieldset>
				<legend><?php echo $e ? 'Edit Item' : 'Add Item'; ?></legend>
				<div class="gr-control-group">
					<label class="gr-control-label" for="description">Description</label>
					<div class="gr-controls"><input id="description" name="description" type="text" class="gr-input-xlarge" placeholder="Description" maxlength="255" required value="<?php echo $v('description'); ?>"></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="category">Category</label>
					<div class="gr-controls">
						<select id="category" name="category_id" class="gr-input-xlarge">
							<option value="0">Uncategorized</option>
							<?php foreach ($this->categories as $id => $title) : ?>
								<option value="<?php echo $id; ?>" <?php echo (int) ($e->category_id ?? 0) === $id ? 'selected' : ''; ?>><?php echo $this->escape($title); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="price">Price ($)</label>
					<div class="gr-controls"><input id="price" name="price" type="text" class="gr-input-xlarge" placeholder="0.00" inputmode="decimal" value="<?php echo $v('price'); ?>"></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="source">Store/Retailer</label>
					<div class="gr-controls"><input id="source" name="source" type="text" class="gr-input-xlarge" placeholder="Source" maxlength="255" value="<?php echo $v('source'); ?>"></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="ranking">Ranking</label>
					<div class="gr-controls">
						<select id="ranking" name="ranking" size="4" class="gr-input-xlarge">
							<?php foreach (Registry::RANKS as $r => $label) : ?>
								<option value="<?php echo $r; ?>" <?php echo (int) ($e->ranking ?? 3) === $r ? 'selected' : ''; ?>><?php echo $r . ' - ' . $label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="url">URL (optional)</label>
					<div class="gr-controls"><input id="url" name="url" type="text" class="gr-input-xlarge" maxlength="1024" value="<?php echo $v('url'); ?>"></div>
				</div>
				<div class="gr-control-group">
					<label class="gr-control-label" for="comment">Comment</label>
					<div class="gr-controls"><textarea id="comment" name="comment" class="gr-input-xlarge" rows="2"><?php echo $v('comment'); ?></textarea></div>
				</div>
				<div class="gr-form-actions">
					<input type="hidden" name="task" value="saveItem">
					<input type="hidden" name="id" value="<?php echo (int) ($e->id ?? 0); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<button type="submit" class="gr-btn gr-btn-primary">Save</button>
					<a class="gr-btn" href="<?php echo giftregPage('family'); ?>">Cancel</a>
				</div>
			</fieldset>
		</form>
	</div>
</div>
