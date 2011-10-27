<?
session_start();
require_once 'db.php';
require_once 'functions.php';
$type = trim($_POST["type"]);
if (!empty($type)) {
	$obj = new $type;
	switch($type) {
		case "Theme" :
			{
				switch($_POST["cmd"]) {
					case "new_theme" :
						$obj -> new_theme($_POST["title"], $_POST["description"]);
						print json_encode(array("Id" => $obj -> getId(), "Title" => $obj -> getTitle(), "Href" => $obj -> getHref()));
						break;
				}
			}
			break;
		case "Question" :
			{
				switch($_POST["cmd"]) {
					case "new_question" :
						$obj -> new_question($_POST["title"], $_POST["theme_id"]);
						print json_encode(array("Id" => $obj -> getId(), "Title" => $obj -> getTitle()));
						break;
					case "get_list_questions" :
						print json_encode($obj -> getListQuestions($_POST["theme_id"]));
						break;
				}
			}
			break;
		case "Answer" :
			{
				switch($_POST["cmd"]) {
					case "new_answer" :
						$obj -> new_answer($_POST["title"], $_POST["question_id"], $_POST["right"]);
						print json_encode(array("Id" => $obj -> getId(), "Title" => $obj -> getTitle()));
						break;
					case "get_list_answers" :
						print json_encode($obj -> getListAnswers($_POST["question_id"]));
						break;
					case "reply" :
						print json_encode(array("result" => $obj -> Reply($_POST["answer_id"], $_POST["team_id"], $_POST["question_id"])));
						break;
				}
			}
			break;
		case "Teams" :
			{
				switch($_POST["cmd"]) {
					case "new_team" :
						$obj -> new_team($_POST["title"], $_POST["theme_id"]);
						print json_encode(array("Id" => $obj -> getId(), "Title" => $obj -> getTitle()));
						break;
					case "get_opponents" :
						print json_encode($obj -> getOpponents($_POST["team_id"], $_POST["theme_id"]));
						break;
					case "get_ready" :
						$ready = intval($obj -> getReady($_POST["team_id"]));
						if ($ready == 1)
							$ready = $_POST["team_id"];
						else
							$ready = 0;
						print json_encode(array("result" => $ready));
						break;
					case "team_ready" :
						print json_encode(array("result" => $obj -> teamReady($_POST["team_id"])));
						break;
					case "start_game" :
						print json_encode(array("result" => $obj -> startGame($_POST["team_id"])));
						break;
					case "finish_game" :
						print json_encode(array("result" => $obj -> finishGame($_POST["team_id"])));
						break;
					case "result_team" :
						print json_encode($obj -> resultTeam($_POST["team_id"]));
						break;
				}
			}
			break;
	}
}
?>