<?
session_start();
$part = trim($_GET["part"]);
$include = null;
switch($part){
	case "game":{
		$include = "game.php";
	}; break;
	case "result":{
		$include = "result.php";
	}; break;
	default:{
		$include = "admin.php";
			}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8" />
		<meta name="keywords" content="игра, интерактивная игра, игра для школы, игра для групп" />
		<meta name="description" content="Интерактивная игра Быстрые и решительные, игра нескольких команд" />
		<meta name="author" content="Митрофанов Николай (mitrofanovnk@gmail.com)" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="style.css" type="text/css" media="all" />
		<link rel="stylesheet" href="jquery-ui-1.8.16.css" type="text/css" media="all" />
		<link rel="stylesheet" href="jquery.fileupload-ui.css" type="text/css" media="all" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.16.min.js"></script>
		<script type="text/javascript" src="js/jquery.fileupload.js"></script>
		<script type="text/javascript" src="js/jquery.iframe-transport.js"></script>
		<title>"Быстрые и решительные"</title>
	</head>
	<body>
		<div class="wrap">
			<?
			if (file_exists("backend/" . $include)) {
				include "backend/" . $include;
			} else {
				echo "<h1>Ошибка файловой системы!</h1>";
			}
			?>
		</div>
	</body>
</html>