<?php
namespace Hughes\Component\Giftreg\Site\View\Mylist;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public $items;
	public $events;
	public $categories;
	public $edit;

	public function display($tpl = null)
	{
		$me               = (int) Factory::getApplication()->getIdentity()->id;
		$this->items      = Registry::items($me);
		$this->events     = Registry::events($me);
		$this->categories = Registry::categories();
		$edit             = Registry::item(Factory::getApplication()->getInput()->getInt('edit', 0));
		$this->edit       = $edit && (int) $edit->user_id === $me ? $edit : null;
		parent::display($tpl);
	}
}
