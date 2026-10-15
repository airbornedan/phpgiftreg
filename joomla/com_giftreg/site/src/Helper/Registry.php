<?php
namespace Hughes\Component\Giftreg\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * All gift-registry data access. Rules:
 * - everyone with a site login is family and may shop for anyone;
 * - an owner never sees who reserved or bought their items;
 * - deleting a reserved item leaves the shopper an in-app notice.
 */
class Registry
{
	public const RANKS = [
		1 => "Wouldn't mind it",
		2 => 'Would be nice to have',
		3 => 'Would make me happy',
		4 => 'I would really, really like this',
		5 => "I'd love to get this",
	];

	/** Days ahead an event shows on the family page. */
	public const EVENT_DAYS = 60;

	private static function db(): DatabaseInterface
	{
		return Factory::getContainer()->get(DatabaseInterface::class);
	}

	private static function now(): string
	{
		return Factory::getDate()->toSql();
	}

	public static function people(): array
	{
		$db = self::db();
		$q  = $db->getQuery(true)
			->select(['u.id', 'u.name', 'COUNT(i.id) AS items'])
			->from('#__users AS u')
			->join('LEFT', '#__giftreg_items AS i ON i.user_id = u.id')
			->where('u.block = 0')
			->group(['u.id', 'u.name'])
			->order('u.name');
		return $db->setQuery($q)->loadObjectList();
	}

	public static function person(int $id): ?object
	{
		$db = self::db();
		$q  = $db->getQuery(true)->select(['id', 'name'])->from('#__users')
			->where('id = :id')->where('block = 0')->bind(':id', $id, ParameterType::INTEGER);
		return $db->setQuery($q)->loadObject() ?: null;
	}

	public static function categories(): array
	{
		$db = self::db();
		return $db->setQuery($db->getQuery(true)->select(['id', 'title'])->from('#__giftreg_categories')->order('title'))
			->loadAssocList('id', 'title');
	}

	/** A person's items, best-wanted first. With $viewer set (someone else), adds the reservation state. */
	public static function items(int $owner, ?int $viewer = null): array
	{
		$db = self::db();
		$q  = $db->getQuery(true)
			->select(['i.*', 'c.title AS category'])
			->from('#__giftreg_items AS i')
			->join('LEFT', '#__giftreg_categories AS c ON c.id = i.category_id')
			->where('i.user_id = :owner')->bind(':owner', $owner, ParameterType::INTEGER)
			->order(['i.ranking DESC', 'i.description']);
		if ($viewer !== null && $viewer !== $owner) {
			$q->select(['a.user_id AS shopper_id', 'a.bought', 's.name AS shopper'])
				->join('LEFT', '#__giftreg_allocs AS a ON a.item_id = i.id')
				->join('LEFT', '#__users AS s ON s.id = a.user_id');
		}
		return $db->setQuery($q)->loadObjectList();
	}

	public static function item(int $id): ?object
	{
		$db = self::db();
		$q  = $db->getQuery(true)->select('*')->from('#__giftreg_items')
			->where('id = :id')->bind(':id', $id, ParameterType::INTEGER);
		return $db->setQuery($q)->loadObject() ?: null;
	}

	public static function saveItem(int $owner, array $data, int $id = 0): void
	{
		$db  = self::db();
		$row = (object) [
			'user_id'     => $owner,
			'description' => trim($data['description']),
			'price'       => $data['price'] === '' ? null : round((float) $data['price'], 2),
			'source'      => trim($data['source']),
			'url'         => trim($data['url']),
			'ranking'     => max(1, min(5, (int) $data['ranking'])),
			'category_id' => (int) $data['category_id'] ?: null,
			'comment'     => trim($data['comment']),
			'modified'    => self::now(),
		];
		if ($id) {
			$row->id = $id;
			$db->updateObject('#__giftreg_items', $row, 'id', true);
		} else {
			$row->created = $row->modified;
			$db->insertObject('#__giftreg_items', $row);
		}
	}

	/** Delete an item; whoever had it reserved or bought gets a notice. */
	public static function deleteItem(object $item, string $ownerName): void
	{
		$db = self::db();
		$q  = $db->getQuery(true)->select(['user_id', 'bought'])->from('#__giftreg_allocs')
			->where('item_id = :id')->bind(':id', $item->id, ParameterType::INTEGER);
		if ($alloc = $db->setQuery($q)->loadObject()) {
			self::notify((int) $alloc->user_id, sprintf('"%s", which you %s for %s, was taken off their list.',
				$item->description, $alloc->bought ? 'bought' : 'reserved', $ownerName));
		}
		foreach (['#__giftreg_allocs' => 'item_id', '#__giftreg_items' => 'id'] as $table => $col) {
			$q = $db->getQuery(true)->delete($table)->where("$col = :id")->bind(':id', $item->id, ParameterType::INTEGER);
			$db->setQuery($q)->execute();
		}
	}

	/** Reserve (bought = 0) or mark bought (1). Only if it's free, or already this shopper's. */
	public static function allocate(int $itemId, int $shopper, int $bought): bool
	{
		$item = self::item($itemId);
		if (!$item || (int) $item->user_id === $shopper) {
			return false;
		}
		$db = self::db();
		$q  = $db->getQuery(true)->select('user_id')->from('#__giftreg_allocs')
			->where('item_id = :id')->bind(':id', $itemId, ParameterType::INTEGER);
		$holder = $db->setQuery($q)->loadResult();
		if ($holder !== null && (int) $holder !== $shopper) {
			return false;
		}
		$row = (object) ['item_id' => $itemId, 'user_id' => $shopper, 'bought' => $bought, 'created' => self::now()];
		$holder === null ? $db->insertObject('#__giftreg_allocs', $row) : $db->updateObject('#__giftreg_allocs', $row, 'item_id');
		return true;
	}

	public static function release(int $itemId, int $shopper): void
	{
		$db = self::db();
		$q  = $db->getQuery(true)->delete('#__giftreg_allocs')
			->where('item_id = :id')->where('user_id = :u')
			->bind(':id', $itemId, ParameterType::INTEGER)->bind(':u', $shopper, ParameterType::INTEGER);
		$db->setQuery($q)->execute();
	}

	/** What this shopper has reserved or bought, grouped by recipient. */
	public static function shoppingList(int $shopper): array
	{
		$db = self::db();
		$q  = $db->getQuery(true)
			->select(['i.*', 'a.bought', 'o.name AS owner'])
			->from('#__giftreg_allocs AS a')
			->join('INNER', '#__giftreg_items AS i ON i.id = a.item_id')
			->join('INNER', '#__users AS o ON o.id = i.user_id')
			->where('a.user_id = :u')->bind(':u', $shopper, ParameterType::INTEGER)
			->order(['o.name', 'a.bought', 'i.description']);
		return $db->setQuery($q)->loadObjectList();
	}

	public static function events(int $owner): array
	{
		$db = self::db();
		$q  = $db->getQuery(true)->select('*')->from('#__giftreg_events')
			->where('user_id = :u')->bind(':u', $owner, ParameterType::INTEGER)->order('eventdate');
		return $db->setQuery($q)->loadObjectList();
	}

	public static function saveEvent(int $owner, array $data): void
	{
		$date = date_create($data['eventdate']);
		if (!$date || trim($data['description']) === '') {
			return;
		}
		$row = (object) [
			'user_id' => $owner, 'description' => trim($data['description']),
			'eventdate' => $date->format('Y-m-d'), 'recurring' => empty($data['recurring']) ? 0 : 1,
		];
		self::db()->insertObject('#__giftreg_events', $row);
	}

	public static function deleteEvent(int $id, int $owner): void
	{
		$db = self::db();
		$q  = $db->getQuery(true)->delete('#__giftreg_events')->where('id = :id')->where('user_id = :u')
			->bind(':id', $id, ParameterType::INTEGER)->bind(':u', $owner, ParameterType::INTEGER);
		$db->setQuery($q)->execute();
	}

	/** Everyone's events in the next EVENT_DAYS days; recurring ones roll to their next date. */
	public static function upcoming(): array
	{
		$db    = self::db();
		$q     = $db->getQuery(true)->select(['e.*', 'u.name'])->from('#__giftreg_events AS e')
			->join('INNER', '#__users AS u ON u.id = e.user_id AND u.block = 0');
		$today = new \DateTimeImmutable('today');
		$out   = [];
		foreach ($db->setQuery($q)->loadObjectList() as $e) {
			$next = new \DateTimeImmutable($e->eventdate);
			if ($e->recurring) {
				$next = $next->setDate((int) $today->format('Y'), (int) $next->format('m'), (int) $next->format('d'));
				if ($next < $today) {
					$next = $next->modify('+1 year');
				}
			}
			$days = (int) $today->diff($next)->format('%r%a');
			if ($days >= 0 && $days <= self::EVENT_DAYS) {
				$e->next = $next;
				$e->days = $days;
				$out[]   = $e;
			}
		}
		usort($out, fn ($a, $b) => $a->days <=> $b->days);
		return $out;
	}

	public static function notify(int $user, string $message): void
	{
		$row = (object) ['user_id' => $user, 'message' => $message, 'created' => self::now(), 'is_read' => 0];
		self::db()->insertObject('#__giftreg_notices', $row);
	}

	public static function notices(int $user): array
	{
		$db = self::db();
		$q  = $db->getQuery(true)->select('*')->from('#__giftreg_notices')
			->where('user_id = :u')->where('is_read = 0')->bind(':u', $user, ParameterType::INTEGER)->order('created DESC');
		return $db->setQuery($q)->loadObjectList();
	}

	public static function dismissNotices(int $user): void
	{
		$db = self::db();
		$q  = $db->getQuery(true)->update('#__giftreg_notices')->set('is_read = 1')
			->where('user_id = :u')->bind(':u', $user, ParameterType::INTEGER);
		$db->setQuery($q)->execute();
	}
}
