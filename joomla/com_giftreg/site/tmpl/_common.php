<?php
// Shared bits for every page: the nav bar and a one-button POST form.
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

	/** A small form posting $task with hidden $fields; returns to this page. */
	function giftregButton(string $task, array $fields, string $label, string $class = 'btn-outline-secondary', string $confirm = ''): string
	{
		$out = '<form method="post" class="d-inline" action="' . Route::_('index.php?option=com_giftreg') . '"'
			. ($confirm ? ' onsubmit="return confirm(\'' . htmlspecialchars($confirm, ENT_QUOTES) . '\')"' : '') . '>'
			. '<input type="hidden" name="task" value="' . $task . '">'
			. '<input type="hidden" name="return" value="' . htmlspecialchars(Uri::getInstance()->toString(), ENT_QUOTES) . '">';
		foreach ($fields as $k => $v) {
			$out .= '<input type="hidden" name="' . $k . '" value="' . (int) $v . '">';
		}
		return $out . HTMLHelper::_('form.token') . '<button class="btn btn-sm ' . $class . '">' . $label . '</button></form>';
	}

	function giftregPrice($price): string
	{
		return $price === null ? '' : '$' . number_format((float) $price, 2);
	}

	/** Link the item's description to its URL when it has one. */
	function giftregTitle(object $item): string
	{
		$text = htmlspecialchars($item->description);
		$url  = $item->url;
		if ($url !== '' && preg_match('#^https?://#i', $url)) {
			return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener">' . $text . '</a>';
		}
		return $text;
	}
}
