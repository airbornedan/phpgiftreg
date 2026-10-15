-- One-time copy of the old phpgiftreg data into com_giftreg's tables.
-- Run in the Joomla database (prefix jos_) on the same MySQL server as `phpgiftreg`.
-- Re-runnable: it empties the new tables first.
-- Users are matched by username. Items of unmatched users are skipped (only the old shared
-- "admin" test items). Events from "admin" and the user-less Christmas become family
-- dates (user_id 0). Old messages are not copied.

SET @now = UTC_TIMESTAMP();

DELETE FROM jos_giftreg_notices;
DELETE FROM jos_giftreg_allocs;
DELETE FROM jos_giftreg_items;
DELETE FROM jos_giftreg_events;

CREATE TEMPORARY TABLE gr_usermap (old_id int PRIMARY KEY, new_id int NOT NULL)
SELECT u.userid AS old_id, j.id AS new_id
FROM phpgiftreg.users u
JOIN jos_users j ON j.username = CONVERT(u.username USING utf8mb4) COLLATE utf8mb4_unicode_ci;

INSERT INTO jos_giftreg_items (id, user_id, description, price, source, url, ranking, category_id, comment, created, modified)
SELECT i.itemid, m.new_id, i.description, NULLIF(i.price, 0), i.source, COALESCE(i.url, ''),
       LEAST(GREATEST(i.ranking, 1), 5), NULLIF(i.category, 0), i.comment, @now, @now
FROM phpgiftreg.items i
JOIN gr_usermap m ON m.old_id = i.userid;

-- One shopper per item; a reserved+bought pair for the same shopper becomes bought.
INSERT IGNORE INTO jos_giftreg_allocs (item_id, user_id, bought, created)
SELECT a.itemid, m.new_id, MAX(a.bought), @now
FROM phpgiftreg.allocs a
JOIN jos_giftreg_items n ON n.id = a.itemid
JOIN gr_usermap m ON m.old_id = a.userid
WHERE m.new_id <> n.user_id
GROUP BY a.itemid, m.new_id;

INSERT INTO jos_giftreg_events (user_id, description, eventdate, recurring)
SELECT COALESCE(m.new_id, 0), e.description, e.eventdate, e.recurring
FROM phpgiftreg.events e
LEFT JOIN phpgiftreg.users u ON u.userid = e.userid
LEFT JOIN gr_usermap m ON m.old_id = e.userid
WHERE m.new_id IS NOT NULL OR e.userid IS NULL OR u.username = 'admin';

SELECT (SELECT COUNT(*) FROM jos_giftreg_items) AS items, (SELECT COUNT(*) FROM jos_giftreg_allocs) AS allocs,
       (SELECT COUNT(*) FROM jos_giftreg_events) AS events;
