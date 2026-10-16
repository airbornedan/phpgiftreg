<?php
// Install an extension folder from the command line and print the installer's messages (the Joomla CLI fails silently).
const _JEXEC = 1;
define('JPATH_BASE', '/var/www/html');
require JPATH_BASE . '/includes/defines.php';
require JPATH_BASE . '/includes/framework.php';
$container = \Joomla\CMS\Factory::getContainer();
$container->alias('session', 'session.cli')->alias('JSession', 'session.cli')
	->alias(\Joomla\CMS\Session\Session::class, 'session.cli')
	->alias(\Joomla\Session\Session::class, 'session.cli')
	->alias(\Joomla\Session\SessionInterface::class, 'session.cli');
$app = $container->get(\Joomla\Console\Application::class);
\Joomla\CMS\Factory::$application = $app;
require_once JPATH_LIBRARIES . "/namespacemap.php"; $m = new \JNamespacePsr4Map(); $m->create(); $m->load();
\Joomla\CMS\Log\Log::addLogger(["logger" => "echo"], \Joomla\CMS\Log\Log::ALL);
$ok = \Joomla\CMS\Installer\Installer::getInstance()->install($argv[1]);
echo $ok ? "OK\n" : "FAILED\n";
foreach ($app->getMessageQueue() as $m) echo $m['type'], ': ', strip_tags($m['message']), "\n";
