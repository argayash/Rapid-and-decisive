<?php
class Answer extends Object {
	//------------------------------ Property ----------------------------------
	//Текст ответа
	private $Title;
	//Описание игры
	private $Question_id;
	//Ссылка на игру
	private $Right;
	//

	public function getTitle() {
		return stripslashes($this -> Title);
	}

	public function setTitle($Value) {
		$this -> Title = $Value;
	}

	public function getQuestion_id() {
		return $this -> Question_id;
	}

	public function setQuestion_id($Value) {
		$this -> Question_id = $Value;
	}

	public function getRight() {
		return $this -> Right;
	}

	public function setRight($Value) {
		$this -> Right = $Value;
	}

	public function new_answer($title, $question_id, $right) {
		require_once 'db.php';

		$healthy = array("script", "class", "id");
		$yummy = array("quote", "user_class", "user_id");

		$title = str_replace($healthy, $yummy, addslashes($title));
		$this -> setTitle($title);

		$this -> setQuestion_id(intval($question_id));
		$right = intval($right);
		if (!($right == 0 || $right == 1))
			$right = 0;
		$this -> setRight(intval($right));

		$result = mysql_query("insert into `answer` (`title`,`question_id`,`right`) values ('" . $title . "','" . $this -> getQuestion_id() . "','" . $right . "')");
		if ($result) {
			$result = mysql_query("select max(`id`) from `answer`");
			$row = mysql_fetch_row($result);
			$this -> setId(intval($row[0]));
		}
	}

	public function getListAnswers($question_id) {
		require_once 'db.php';
		$question_id = intval($question_id);
		$result = mysql_query("select `id`,`title` from `answer` where `question_id`='" . $question_id . "'");
		$list = array();
		while ($row = mysql_fetch_row($result)) {
			$list[$row[0]] = $row[1];
		}
		return ass_array_shuffle($list);
	}

	public function Reply($answer_id, $team_id, $question_id) {
		require_once 'db.php';

		$answer_id = intval($answer_id);
		$team_id = intval($team_id);
		$question_id = intval($question_id);

		$result = mysql_query("select `result` from `teams` where `id`='" . $team_id . "'");
		$row = mysql_fetch_row($result);
		$res = intval($row[0]);

		$result = mysql_query("select `right` from `answer` where `id`='" . $answer_id . "' and `question_id`='" . $question_id . "'");
		$row = mysql_fetch_row($result);
		$res += intval($row[0]);

		$result = mysql_query("update `teams` set `result`='" . $res . "' where `id`='" . $team_id . "'");
		if ($result)
			return $res;
		else
			return 0;
	}

	public function del_answer($id) {
		require_once 'db.php';
		$id = intval($id);
		if ($result = mysql_query("delete from `answer` where `id`='" . $id . "'"))
			return true;
	}

	//-------------------------------- Body ------------------------------------
}
?>