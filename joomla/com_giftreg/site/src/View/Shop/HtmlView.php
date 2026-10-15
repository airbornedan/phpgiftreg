<?php
namespace Hughes\Component\Giftreg\Site\View\Shop;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public $me;
	public $person;
	public $items;

	public function display($tpl = null)
	{
		$app          = Factory::getApplication();
		$this->me     = (int) $app->getIdentity()->id;
		$this->person = Registry::person($app->getInput()->getInt('id', 0));
		if (!$this->person) {
			throw new \RuntimeException('No such person.', 404);
		}
		$this->items = Registry::items((int) $this->person->id, $this->me);
		parent::display($tpl);
	}
}
