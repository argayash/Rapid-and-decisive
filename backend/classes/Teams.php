<?php
//класс ножка
class Teams extends Object {
	//------------------------------ Property ----------------------------------
	//Название игры
	private $Title;
	//Описание игры
	private $Theme_id;
	//Конец игры
	private $Is_finish;
	//time start
	private $Time_start;
	//time finish
	private $Time_finish;
	//Ссылка на игру
	private $Result;
	//готовность
	private $Ready;

	public function getTitle() {
		return stripslashes($this -> Title);
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

	public function getIs_finish() {
		return $this -> Is_finish;
	}

	public function setIs_finish($Value) {
		$this -> Is_finish = $Value;
	}

	public function getTime_start() {
		return $this -> Time_start;
	}

	public function setTime_start($Value) {
		$this -> Time_start = $Value;
	}

	public function getTime_finish() {
		return $this -> Time_finish;
	}

	public function setTime_finish($Value) {
		$this -> Time_finish = $Value;
	}

	public function getResult() {
		return $this -> Result;
	}

	public function setResult($Value) {
		$this -> Result = $Value;
	}

	public function getReady($id) {
		require_once 'db.php';
		$id = intval($id);
		$result = mysql_query("select `ready` from `teams` where `id`='" . $id . "'");
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	public function setReady($Value) {
		return $this -> Ready = $Value;
	}

	public function new_team($title, $theme_id) {
		require_once 'db.php';

		$theme_id = intval($theme_id);
		$title = addslashes(htmlspecialchars(trim($title)));
		$this -> setTitle($title);

		$result = mysql_query("select count(`id`) from `teams` where `theme_id`='" . $theme_id . "'");
		$row = mysql_fetch_row($result);
		if (intval($row[0]) < 4) {
			$result = mysql_query("insert into `teams` (`title`,`theme_id`) values ('" . $title . "','" . $theme_id . "')");
			if ($result) {
				$result = mysql_query("select max(`id`) from `teams`");
				$row = mysql_fetch_row($result);
				$this -> setId(intval($row[0]));
			}
		} else {
			$this -> setId(0);
		}
	}

	public function teamReady($id) {
		require_once 'db.php';
		$id = intval($id);
		$result = mysql_query("update `teams` set `ready`='1' where `id`='" . $id . "'");
		if ($result)
			return 1;
		else
			return 0;
	}

	public function getOpponents($team_id, $theme_id) {
		require_once 'db.php';
		$team_id = intval($team_id);
		$theme_id = intval($theme_id);
		$list = array();
		if ($result = mysql_query("select `id`,`title` from `teams` where `theme_id`='" . $theme_id . "' and `id`<>'" . $team_id . "'"))
			while ($row = mysql_fetch_row($result)) {
				$list[$row[0]] = $row[1];
			}
		return $list;
	}

	public function startGame($id) {
		require_once 'db.php';
		$id = intval($id);
		$result = mysql_query("update `teams` set `time_start`=NOW() where `id`='" . $id . "'");
		if ($result)
			return 1;
		else
			return 0;
	}

	public function finishGame($id) {
		require_once 'db.php';
		$id = intval($id);
		$result = mysql_query("update `teams` set `time_finish`=NOW() where `id`='" . $id . "'");
		if ($result)
			return 1;
		else
			return 0;
	}

	public function resultTeam($id) {
		require_once 'db.php';
		$id = intval($id);
		$result = mysql_query("select `result`, TIMESTAMPDIFF(SECOND, `time_start`, `time_finish`) as `time` from `teams` where `id`='" . $id . "'");
		$row = mysql_fetch_array($result);
		return array("id" => $id, "result" => $row["result"], "times" => intval($row["time"]));
	}

	public function del_team($id) {
		require_once 'db.php';
		$id = intval($id);
		if ($result = mysql_query("delete from `teams` where `id`='" . $id . "'"))
			return true;
	}

	//-------------------------------- Body ------------------------------------
}
?>