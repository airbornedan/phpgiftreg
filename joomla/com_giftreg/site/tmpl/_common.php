<?php
// Shared bits for every page: the nav bar, the old site's icons and a one-icon POST form.
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

if (!function_exists('giftregNav')) {
	function giftregNav(string $active): string
	{
		$out = '<nav class="nav nav-pills mb-3">';
		foreach (['family' => 'Family', 'mylist' => 'My list', 'shoplist' => 'My shopping'] as $view => $label) {
			$cls  = 'nav-link' . ($view === $active ? ' active' : '');
			$out .= '<a class="' . $cls . '" href="' . Route::_('index.php?option=com_giftreg&view=' . $view) . '">' . $label . '</a>';
		}
		return $out . '</nav>';
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
		$title = \Hughes\Component\Giftreg\Site\Helper\Registry::RANKS[$rank] ?? '';
		$out   = '<span class="text-nowrap" title="' . htmlspecialchars($title, ENT_QUOTES) . '">';
		for ($n = 1; $n <= 5; $n++) {
			$out .= '<img src="' . Uri::root(true) . '/media/com_giftreg/images/star_' . ($n <= $rank ? 'on' : 'off') . '.gif" alt="' . ($n <= $rank ? '*' : '') . '">';
		}
		return $out . '</span>';
	}

	/** A small form posting $task with hidden $fields; returns to this page. With $icon, the button is that icon. */
	function giftregButton(string $task, array $fields, string $label, string $class = 'btn-outline-secondary', string $confirm = '', string $icon = ''): string
	{
		$out = '<form method="post" class="d-inline" action="' . Route::_('index.php?option=com_giftreg') . '"'
			. ($confirm ? ' onsubmit="return confirm(\'' . htmlspecialchars($confirm, ENT_QUOTES) . '\')"' : '') . '>'
			. '<input type="hidden" name="task" value="' . $task . '">'
			. '<input type="hidden" name="return" value="' . htmlspecialchars(Uri::getInstance()->toString(), ENT_QUOTES) . '">';
		foreach ($fields as $k => $v) {
			$out .= '<input type="hidden" name="' . $k . '" value="' . (int) $v . '">';
		}
		$button = $icon
			? '<button class="btn btn-link p-0 border-0 align-baseline">' . giftregIcon($icon, $label) . '</button>'
			: '<button class="btn btn-sm ' . $class . '">' . $label . '</button>';
		return $out . HTMLHelper::_('form.token') . $button . '</form>';
	}

	function giftregPrice($price): string
	{
		return $price === null ? '' : '$' . number_format((float) $price, 2);
	}

	/** The description, then a link icon when the item has a URL. */
	function giftregTitle(object $item): string
	{
		$text = htmlspecialchars($item->description);
		$url  = $item->url;
		if ($url !== '' && preg_match('#^https?://#i', $url)) {
			$text .= ' <a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener">' . giftregIcon('link.png', 'URL') . '</a>';
		}
		return $text;
	}
}
