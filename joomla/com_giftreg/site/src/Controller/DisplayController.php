<?php
namespace Hughes\Component\Giftreg\Site\Controller;

defined('_JEXEC') or die;

use Hughes\Component\Giftreg\Site\Helper\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** Pages need a site login. Tasks (forms) are POST with a token and redirect back. */
class DisplayController extends BaseController
{
	protected $default_view = 'family';

	public function display($cachable = false, $urlparams = [])
	{
		if ($this->user()->guest) {
			$this->setRedirect(Route::_('index.php?option=com_users&view=login&return=' . base64_encode(Uri::getInstance()->toString()), false));
			return $this;
		}
		$view = $this->input->get('view', $this->default_view);
		if ($view === 'shop' && $this->input->getInt('id', 0) === (int) $this->user()->id) {
			$this->setRedirect($this->page('mylist'));       // no peeking at your own reservations
			return $this;
		}
		return parent::display(false, $urlparams);
	}

	public function saveItem()
	{
		$me   = $this->begin();
		$id   = $this->input->post->getInt('id', 0);
		$data = $this->input->post->getArray([
			'description' => 'string', 'price' => 'string', 'source' => 'string', 'url' => 'string',
			'ranking' => 'int', 'category_id' => 'int', 'comment' => 'string',
		]);
		if ($id && (int) (Registry::item($id)->user_id ?? 0) !== $me) {
			$id = -1;
		}
		if ($id >= 0 && trim($data['description']) !== '') {
			Registry::saveItem($me, $data, $id);
		}
		$this->setRedirect($this->page('mylist'));
	}

	public function deleteItem()
	{
		$me   = $this->begin();
		$item = Registry::item($this->input->post->getInt('id', 0));
		if ($item && (int) $item->user_id === $me) {
			Registry::deleteItem($item, $this->user()->name);
		}
		$this->setRedirect($this->page('mylist'));
	}

	public function reserve()
	{
		$this->allocate(0);
	}

	public function buy()
	{
		$this->allocate(1);
	}

	public function release()
	{
		$me = $this->begin();
		Registry::release($this->input->post->getInt('item', 0), $me);
		$this->back();
	}

	public function saveEvent()
	{
		$this->begin();
		Registry::saveEvent($this->input->post->getArray(['description' => 'string', 'eventdate' => 'string', 'recurring' => 'int']));
		$this->setRedirect($this->page('family') . '#dates');
	}

	public function deleteEvent()
	{
		$this->begin();
		Registry::deleteEvent($this->input->post->getInt('id', 0));
		$this->setRedirect($this->page('family') . '#dates');
	}

	public function dismissNotices()
	{
		Registry::dismissNotices($this->begin());
		$this->setRedirect($this->page('family'));
	}

	private function allocate(int $bought): void
	{
		$me = $this->begin();
		if (!Registry::allocate($this->input->post->getInt('item', 0), $me, $bought)) {
			$this->app->enqueueMessage('Someone else already has that one.', 'warning');
		}
		$this->back();
	}

	/** Every task: valid token and logged in. Returns the user id. */
	private function begin(): int
	{
		$this->checkToken();
		if ($this->user()->guest) {
			throw new \RuntimeException('Please log in.', 403);
		}
		return (int) $this->user()->id;
	}

	private function back(): void
	{
		$return = $this->input->post->getString('return');
		$this->setRedirect($return && Uri::isInternal($return) ? $return : $this->page('family'));
	}

	private function page(string $view): string
	{
		return Route::_('index.php?option=com_giftreg&view=' . $view, false);
	}

	private function user()
	{
		return Factory::getApplication()->getIdentity();
	}
}
