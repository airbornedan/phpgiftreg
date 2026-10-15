<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . "/includes/funcLib.php");
require_once(dirname(__FILE__) . "/includes/MySmarty.class.php");
$smarty = new MySmarty();
$opt = $smarty->opt();

$cookieArray = array();
foreach ($_COOKIE as $key=>$val) {
	if (strlen($key) === 32) {
		array_push($cookieArray, $val);
	}
}
 try {
 	$inClause = implode(',', array_fill(0, count($cookieArray), '?'));
	$stmt = $smarty->dbh()->prepare("SELECT username, time FROM hughesDB.jos_session WHERE username <> '' AND session_id in (" . $inClause . ")");
	foreach ($cookieArray as $k => $id) {
    	$stmt->bindValue(($k+1), $id);
    }

	$stmt->execute();
	if ($row = $stmt->fetch()) {
		$isAdmin = 0;
		if ($row["username"] === "admin1" || $row["username"] === "admin2") {
			$isAdmin = 1;
		}
		$mustAccessAfter = strtotime(' -1 day');
		if ($row["time"] > $mustAccessAfter) {
			$userName = $row["username"];
			// lookup userid from gifts table
			$stmt = $smarty->dbh()->prepare("SELECT userid, fullname FROM phpgiftreg.users WHERE username = ?");
			$stmt->bindParam(1, $userName, PDO::PARAM_STR);
			$stmt->execute();
			if ($row = $stmt->fetch()) {
				session_start();
				$_SESSION["userid"] = $row["userid"];
				$_SESSION["fullname"] = $row["fullname"];
				$_SESSION["admin"] = $isAdmin;

				header("Location: " . getFullPath("index.php"));
				exit;
			}
		}
	}
} catch (PDOException $e) {
	die("sql exception: " . $e->getMessage());
}

header("Location: /");
?>
