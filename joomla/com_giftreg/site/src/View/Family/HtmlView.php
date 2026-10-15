<?php
namespace Hughes\Component\Giftreg\Site\View\Family;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public $me;
	public $people;
	public $upcoming;
	public $notices;

	public function display($tpl = null)
	{
		$this->me       = (int) Factory::getApplication()->getIdentity()->id;
		$this->people   = Registry::people();
		$this->upcoming = Registry::upcoming();
		$this->notices  = Registry::notices($this->me);
		parent::display($tpl);
	}
}
