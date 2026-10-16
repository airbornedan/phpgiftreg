<?php
namespace Hughes\Component\Giftreg\Site\View\Events;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public $events;

	public function display($tpl = null)
	{
		$this->events = Registry::events();
		parent::display($tpl);
	}
}
