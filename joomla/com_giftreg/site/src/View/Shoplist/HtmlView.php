<?php
namespace Hughes\Component\Giftreg\Site\View\Shoplist;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public $items;

	public function display($tpl = null)
	{
		$this->items = Registry::shoppingList((int) Factory::getApplication()->getIdentity()->id);
		parent::display($tpl);
	}
}
