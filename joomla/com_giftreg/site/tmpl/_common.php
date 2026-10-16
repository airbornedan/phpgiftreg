<?php
// Shared bits for every page, in the old phpgiftreg look: the navbar, its icons, sortable headers, one-icon POST forms.
defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

if (!function_exists('giftregNav')) {
	function giftregPage(string $view, string $extra = ''): string
	{
		return Route::_('index.php?option=com_giftreg&view=' . $view . $extra);
	}

	/** The old navbar; also loads the theme. */
	function giftregNav(string $active): string
	{
		Factory::getApplication()->getDocument()->getWebAssetManager()
			->registerAndUseStyle('com_giftreg.site', 'com_giftreg/giftreg.css')
			->registerAndUseScript('com_giftreg.site', 'com_giftreg/giftreg.js', [], ['defer' => true]);
		$li = fn ($view, $label) => '<li' . ($view === $active ? ' class="gr-active"' : '') . '><a href="' . giftregPage($view) . '">' . $label . '</a></li>';
		return '<div class="gr-navbar"><div class="gr-navbar-inner">'
			. '<a class="gr-brand" href="' . giftregPage('family') . '">Gift Registry</a>'
			. '<ul class="gr-nav">' . $li('shoplist', 'My Shopping List') . '</ul>'
			. '<ul class="gr-nav gr-pull-right">'
			. '<li><a href="' . Route::_('index.php?option=com_users&view=profile&layout=edit') . '">Update Profile</a></li>'
			. $li('events', 'Manage Events') . '</ul>'
			. '</div></div>';
	}

	/** One of the old phpgiftreg icons, with $title as tooltip and alt text. */
	function giftregIcon(string $name, string $title): string
	{
		$t = htmlspecialchars($title, ENT_QUOTES);
		return '<img src="' . Uri::root(true) . '/media/com_giftreg/images/' . $name . '" alt="' . $t . '" title="' . $t . '">';
	}

	/** The ranking as five stars, like the old site. */
	function giftregStars(int $rank): string
	{
		$out = '<span style="white-space:nowrap" title="' . htmlspecialchars(Registry::RANKS[$rank] ?? '', ENT_QUOTES) . '">';
		for ($n = 1; $n <= 5; $n++) {
			$out .= '<img src="' . Uri::root(true) . '/media/com_giftreg/images/star_' . ($n <= $rank ? 'on' : 'off') . '.gif" alt="' . ($n <= $rank ? '*' : '') . '">';
		}
		return $out . '</span>';
	}

	/** A form posting $task with hidden $fields, shown as the icon $icon titled $label; returns to this page. */
	function giftregButton(string $task, array $fields, string $label, string $icon, string $confirm = ''): string
	{
		$out = '<form method="post" class="gr-inline" action="' . Route::_('index.php?option=com_giftreg') . '"'
			. ($confirm ? ' onsubmit="return confirm(\'' . htmlspecialchars(addslashes($confirm), ENT_QUOTES) . '\')"' : '') . '>'
			. '<input type="hidden" name="task" value="' . $task . '">'
			. '<input type="hidden" name="return" value="' . htmlspecialchars(Uri::getInstance()->toString(), ENT_QUOTES) . '">';
		foreach ($fields as $k => $v) {
			$out .= '<input type="hidden" name="' . $k . '" value="' . (int) $v . '">';
		}
		return $out . HTMLHelper::_('form.token') . '<button class="gr-icon">' . giftregIcon($icon, $label) . '</button></form>';
	}

	function giftregPrice($price): string
	{
		return $price === null ? '' : '$' . number_format((float) $price, 2);
	}

	/** The description, its "..." comment button and a link icon, as on the old lists. */
	function giftregTitle(object $item): string
	{
		$out  = htmlspecialchars($item->description);
		$tail = '';
		if (trim((string) $item->comment) !== '') {
			$tail .= ' <a class="gr-btn gr-btn-small gr-pop" href="#" data-gr-comment="' . htmlspecialchars($item->comment, ENT_QUOTES) . '">...</a>';
		}
		if (preg_match('#^https?://#i', (string) $item->url)) {
			$tail .= ' <a href="' . htmlspecialchars($item->url) . '" target="_blank" rel="noopener">' . giftregIcon('link.png', 'URL') . '</a>';
		}
		return $out . ($tail ? '<span class="gr-nowrap">' . $tail . '</span>' : '');
	}

	/** Where to get it, linked to the item's URL when it has one. */
	function giftregSource(object $item): string
	{
		$text = htmlspecialchars((string) $item->source);
		if ($text !== '' && preg_match('#^https?://#i', (string) $item->url)) {
			return '<a href="' . htmlspecialchars($item->url) . '" target="_blank" rel="noopener">' . $text . '</a>';
		}
		return $text;
	}

	/** A column header that sorts the page by $key. */
	function giftregSortLink(string $view, string $extra, string $key, string $label): string
	{
		return '<a href="' . giftregPage($view, $extra . '&sort=' . $key) . '">' . $label . '</a>';
	}

	/** Sort rows by ?sort= (one of $keys: name => property); best-ranked first when unset or unknown. */
	function giftregSorted(array $rows, array $keys): array
	{
		$sort = Factory::getApplication()->getInput()->getCmd('sort', '');
		$prop = $keys[$sort] ?? null;
		if ($prop === null) {
			return $rows;
		}
		usort($rows, function ($a, $b) use ($prop) {
			if ($prop === 'ranking') {
				return $b->ranking <=> $a->ranking;
			}
			return is_numeric($a->$prop) || is_numeric($b->$prop)
				? (float) $a->$prop <=> (float) $b->$prop
				: strcasecmp((string) $a->$prop, (string) $b->$prop);
		});
		return $rows;
	}
}
