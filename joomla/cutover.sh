#!/bin/bash
# Switch hugheswebsite.com from the old /giftreg to com_giftreg. Run as root on the droplet from the
# unpacked bundle (com_giftreg/, gr_install.php, migrate.sql). Undo: see the last lines it prints.
set -euo pipefail
cd "$(dirname "$0")"
W=/var/www/html
B=/root/giftreg-cutover-$(date +%F-%H%M)
mkdir -p "$B"
cfg() { php -r "require '$W/configuration.php'; echo (new JConfig)->$1;"; }
JU=$(cfg user); JP=$(cfg password); JD=$(cfg db)
GU=$(php -r "require '$W/giftreg/includes/config.php'; \$o=getGlobalOptions(); echo \$o['pdo_username'];")
GP=$(php -r "require '$W/giftreg/includes/config.php'; \$o=getGlobalOptions(); echo \$o['pdo_password'];")

echo "== backups in $B"
mysqldump --single-transaction --no-tablespaces -u"$JU" -p"$JP" "$JD" | gzip > "$B/hughesDB.sql.gz"
mysqldump --single-transaction --no-tablespaces -u"$GU" -p"$GP" phpgiftreg | gzip > "$B/phpgiftreg.sql.gz"
ls -la "$B"

echo "== install com_giftreg"
rm -rf "$W/tmp/com_giftreg" && cp -r com_giftreg "$W/tmp/" && cp gr_install.php "$W/tmp/"
chown -R www-data:www-data "$W/tmp/com_giftreg" "$W/tmp/gr_install.php"
sudo -u www-data php "$W/tmp/gr_install.php" "$W/tmp/com_giftreg" 2>&1 | grep -E '^(OK|FAILED)|message' | grep -q '^OK' \
	|| { echo "install FAILED"; exit 1; }
rm -rf "$W/tmp/com_giftreg" "$W/tmp/gr_install.php"
echo OK

echo "== migrate"
if mysql -u"$JU" -p"$JP" -e 'SELECT 1 FROM phpgiftreg.users LIMIT 1' >/dev/null 2>&1; then
	mysql -u"$JU" -p"$JP" "$JD" < migrate.sql
else
	mysql -u"$GU" -p"$GP" "$JD" < migrate.sql
fi

echo "== swap /giftreg for a redirect"
mv -n "$W/giftreg" /var/www/giftreg_old
mkdir "$W/giftreg"
echo '<?php header("Location: /index.php?option=com_giftreg", true, 302);' > "$W/giftreg/index.php"
chown -R www-data:www-data "$W/giftreg"
curl -s -o /dev/null -w "giftreg -> %{http_code} %{redirect_url}\n" https://hugheswebsite.com/giftreg/

echo "== undo: rm -r $W/giftreg && mv /var/www/giftreg_old $W/giftreg"
