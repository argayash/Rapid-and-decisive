<?php
//класс ножка
class Question extends Object {
	//------------------------------ Property ----------------------------------
	//Название игры
	private $Title;
	//Родительсая тема
	private $Theme_id;
	//

	public function getTitle() {
		return stripcslashes($this -> Title);
	}

	public function setTitle($Value) {
		$this -> Title = $Value;
	}

	public function getTheme_id() {
		return $this -> Theme_id;
	}

	public function setTheme_id($Value) {
		$this -> Theme_id = $Value;
	}

	public function new_question($title, $theme_id) {
		require_once 'db.php';

		$theme_id = intval($theme_id);

		$healthy = array("script", "class", "id");
		$yummy = array("quote", "user_class", "user_id");

		$title = str_replace($healthy, $yummy, addslashes($title));
		$this -> setTitle($title);

		$result = mysql_query("insert into `questions` (`title`,`theme_id`) values ('" . $title . "','" . $theme_id . "')");
		if ($result) {
			$result = mysql_query("select max(`id`) from `questions`");
			$row = mysql_fetch_row($result);
			$this -> setId(intval($row[0]));
		}
	}

	public function del_question($id) {
		require_once 'db.php';
		$id = intval($id);
		if ($result = mysql_query("delete from `questions` where `id`='" . $id . "'"))
			return true;
	}

	public function getListQuestions($theme_id) {
		$list = array();
		require_once 'db.php';
		$theme_id = intval($theme_id);
		$result = mysql_query("select `id`,`title` from `questions` where `theme_id`='" . $theme_id . "'");
		while ($row = mysql_fetch_row($result)) {
			$list[$row[0]] = $row[1];
		}
		return $list;
	}

	//-------------------------------- Body ------------------------------------
}
?>