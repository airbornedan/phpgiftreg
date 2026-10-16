<?php
defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\HTML\HTMLHelper;

require dirname(__DIR__) . '/_common.php';
$items = giftregSorted($this->items, ['description' => 'description', 'ranking' => 'ranking', 'category' => 'category', 'price' => 'price']);
$date  = fn ($d) => $d ? HTMLHelper::_('date', $d, 'm/d/Y') : '';
?>
<div class="giftreg">
	<?php echo giftregNav('family'); ?>

	<section id="myitems">
		<div class="gr-well">
			<div class="gr-page-header"><h1>My Items</h1></div>
			<table class="gr-table gr-table-bordered gr-table-striped">
				<thead>
					<tr>
						<th><?php echo giftregSortLink('family', '', 'description', 'Description'); ?></th>
						<th><?php echo giftregSortLink('family', '', 'ranking', 'Ranking'); ?></th>
						<th><?php echo giftregSortLink('family', '', 'category', 'Category'); ?></th>
						<th><?php echo giftregSortLink('family', '', 'price', 'Price'); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($items as $i) : ?>
						<tr valign="top">
							<td><?php echo giftregTitle($i); ?></td>
							<td nowrap><?php echo giftregStars((int) $i->ranking); ?></td>
							<td><?php echo $this->escape($i->category); ?></td>
							<td align="right"><?php echo giftregPrice($i->price); ?></td>
							<td align="right" nowrap>
								<a href="<?php echo giftregPage('mylist', '&edit=' . (int) $i->id); ?>"><?php echo giftregIcon('pencil.png', 'Edit Item'); ?></a>
								<?php echo giftregButton('deleteItem', ['id' => $i->id], 'Delete Item', 'bin.png', 'Are you sure you want to delete "' . $i->description . '"?'); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<h5><a href="<?php echo giftregPage('mylist'); ?>">Add a new item</a></h5>
		</div>
	</section>

	<section id="otherstuff">
		<div class="gr-row">
			<div class="gr-span6">
				<div class="gr-well">
					<h3>People I'm shopping for</h3>
					<table class="gr-table gr-table-bordered gr-table-striped">
						<thead>
							<tr><th>Name</th><th>Last Updated</th><th># Items</th><th>&nbsp;</th></tr>
						</thead>
						<tbody>
							<?php foreach ($this->people as $p) : if ((int) $p->id === $this->me) continue; ?>
								<tr>
									<td><a href="<?php echo giftregPage('shop', '&id=' . (int) $p->id); ?>"><?php echo $this->escape($p->name); ?></a></td>
									<td align="right"><?php echo $date($p->updated); ?></td>
									<td align="right"><?php echo (int) $p->items; ?></td>
									<td align="right" nowrap>
										<?php if ($p->items) : ?>
											<a href="<?php echo giftregPage('shop', '&id=' . (int) $p->id); ?>"><?php echo giftregIcon('store.png', 'Shop for ' . $p->name); ?></a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="gr-span6">
				<div class="gr-well">
					<h3>Upcoming events (within <?php echo Registry::EVENT_DAYS; ?> days)</h3>
					<table class="gr-table gr-table-bordered gr-table-striped">
						<thead>
							<tr><th>Name</th><th>Event</th><th>Date</th><th>Days left</th></tr>
						</thead>
						<tbody>
							<?php foreach ($this->upcoming as $e) : ?>
								<tr>
									<td><?php echo $this->escape($e->name); ?></td>
									<td><?php echo $this->escape($e->description); ?></td>
									<td><?php echo $e->next->format('m/d/Y'); ?></td>
									<td><?php echo $e->days; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="gr-well">
					<h3>Messages</h3>
					<table class="gr-table gr-table-bordered gr-table-striped">
						<thead>
							<tr><th>Date</th><th>Message</th><th>&nbsp;</th></tr>
						</thead>
						<tbody>
							<?php foreach ($this->notices as $n) : ?>
								<tr>
									<td><?php echo $date($n->created); ?></td>
									<td><?php echo $this->escape($n->message); ?></td>
									<td align="right"><?php echo giftregButton('dismissNotices', ['id' => $n->id], 'Acknowledge', 'delete.png'); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>
