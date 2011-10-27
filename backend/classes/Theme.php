<?php
//класс ножка
class Theme extends Object {
	//------------------------------ Property ----------------------------------
	//Название игры
	private $Title;
	//Описание игры
	private $Description;
	//Ссылка на игру
	private $Href;
	//

	public function getTitle() {
		return stripslashes($this -> Title);
	}

	public function setTitle($Value) {
		$this -> Title = $Value;
	}

	public function getDescription() {
		return stripslashes($this -> Description);
	}

	public function setDescription($Value) {
		$this -> Description = $Value;
	}

	public function getHref() {
		return $this -> Href;
	}

	public function setHref($Value) {
		$this -> Href = $Value;
	}

	public function new_theme($title, $description) {
		require_once 'db.php';
		$result = mysql_query("select max(`id`) from `themes`");
		$row = mysql_fetch_row($result);
		$this -> setHref("theme_" . (intval($row[0]) + 1));

		$title = addslashes(htmlspecialchars(trim($title)));
		$this -> setTitle($title);

		$healthy = array("script", "class", "id");
		$yummy = array("quote", "user_class", "user_id");

		$description = str_replace($healthy, $yummy, addslashes($description));
		$this -> setDescription($description);

		$result = mysql_query("insert into `themes` (`title`,`description`,`href`) values ('" . $title . "','" . $description . "','" . $this -> getHref() . "')");
		if ($result) {
			$result = mysql_query("select max(`id`) from `themes`");
			$row = mysql_fetch_row($result);
			$this -> setId(intval($row[0]));
		}
	}

	public function del_theme($id) {
		require_once 'db.php';
		$id = intval($id);
		if ($result = mysql_query("delete from `themes` where `id`='" . $id . "'"))
			return true;
	}

	public function update_href($id, $href) {
		require_once 'db.php';
		$id = intval($id);
		$yummy = array(".", "'", "\"", ",", "`", "<", ">", " ", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")");
		$href = str_replace($yummy, "", addslashes($href));
		$result = mysql_query("update `themes` set `href`='" . $href . "' where `id`='" . $id . "'");
		if ($result)
			return true;
		else
			return false;
	}

	//-------------------------------- Body ------------------------------------
}
?>