<?
//if (preg_match("/^[a-zA-Z0-9_]{4,50}$/", trim($_GET["theme"]))) {
?>
<div class="head">
	<div class="hgroup">
		<h1>Интерактивная игра</h1>
		<h2><?
		require_once "db.php";
		$result = mysql_query("select `id`,`title`,`description` from `themes` where `href`='" . trim($_GET["theme"]) . "'");
		$row = mysql_fetch_array($result);
		echo $row["title"];
		?></h2>
		<div id="theme_id" class="<? echo $row["id"];?>"></div>
		<div id="theme_description">
			<?
			echo $row["description"];
			?>
		</div>
	</div>
	<ol>
		<li class="enable active">
			<span>Регистрация команды</span>
		</li>
		<li class="disable">
			<span>Отвечайте на вопросы</span>
		</li>
		<li class="disable">
			<span>Каков результат?</span>
		</li>
	</ol>
</div>
<div class="main">
	<ul class="steps">
		<li class="step_1">
			<form id="create_form" class="input_form" method="post">
				<label for="title">название команды:</label>
				<div class="input_wrap">
					<input type="text" class="big" name="title" id="title"/>
				</div>
				<button>
					Зарегистрировать команду!
				</button>
			</form>
			<div class="step_1_1">
				<h2><span></span>, добро пожаловать на игру!</h2>
				<h2>Ваши соперники:</h2>
				<ol id="opponents">
					<li class="t_0">
						<span>&nbsp;</span><b class="notready"></b>
					</li>
					<li class="t_1">
						<span>&nbsp;</span><b class="notready"></b>
					</li>
					<li class="t_2">
						<span>&nbsp;</span><b class="notready"></b>
					</li>
				</ol>
				<button class="button" id="we_are_ready" class="">
					Мы готовы!
				</button>
				<div class="align_center">
					<button class="button" id="start_game"></button>
				</div>
			</div>
		</li>
		<li class="step_2">
			<h2>Создание вопросов для игры: <span id="step2_title"></span></h2>
			<ol id="qw_list" class="questions_list"></ol>
			<form id="create_question" class="input_form" method="post">
				<label for="question_title">текст вопроса:</label>
				<div class="input_wrap">
					<input type="text" class="normal" name="question_title" id="question_title"/>
					<input class="normal" id="question_upload" type="file" name="files[]" multiple>
				</div>
				<button>
					Добавить вопрос!
				</button>
			</form>
			<button id="on_step3" class="button">
				Дальше...
			</button>
		</li>
		<li class="step_3">
			<ul class="href_block">
				<li class="game">
					<a href="">Начать игру</a><span></span>
				</li>
				<li class="result">
					<a href="">Результаты игры</a><span></span>
				</li>
			</ul>
		</li>
	</ul>
</div>
<div class="footer"></div>
<div id="template_question" class="template">
	<h3></h3>
	<ol class="answers"></ol>
	<div class="answer_form">
		<label>ответ:</label>
		<input type="text" class="answer_title" name="answer" class="small"/>
		<input class="answer_file" type="file" name="files[]" multiple>
		<select class="answer_right">
			<option value="0">Неверный</option>
			<option value="1">Верный</option>
		</select>
		<button class="add_answer">
			Добавить ответ
		</button>
	</div>
</div>
<div id="template_answer" class="template"></div>
<script type="text/javascript">
	Theme = function() {
		this.id = 0;
		this.getId = function() {
			this.id = $("#theme_id").attr("class");
		}
	}
	Team = function() {
		this.id = 0;
		this.title = "";
		this.times = 0;
		this.result = 0;
		this.ready = false;
		this.finish = false;
		this.startGame = function() {

		}
		this.finishGame = function() {

		}
		this.getResult = function() {

		}
		this.jObject = null;

	}
	Teams = function() {
		this.our_team = 0;
		this.list = [];

	}
	/*GLOBAL VARS*/
	var theme = new Theme();
	theme.getId();
	var our_team = new Team();
	var teams_list = new Teams();
	var opponents_list = [];
	var opponent_int, result_int, ready_int;
	/*jQuery opp*/
	newTeam = function(title, theme_id) {
		var jqxhr = $.post("backend/route.php", {
			"type" : "Teams",
			"cmd" : "new_team",
			"title" : title,
			"theme_id" : theme_id
		}, function(dat) {
			our_team.id = dat.Id;
			our_team.title = dat.Title;
		}, "json").error(function(dat) { alert(dat.Errors);
		})
		return jqxhr
	}
	function getLen(a) {
		var c = 0;
		for(i in a) {
			if(a[i] != undefined)
				c++;
		}
		return c
	}

	function loadOpp() {
		var jqxhr = $.post("backend/route.php", {
			"type" : "Teams",
			"cmd" : "get_opponents",
			"team_id" : our_team.id,
			"theme_id" : theme.id
		}, function(dat) {
			var c = 0;
			for(x in dat) {
				opponents_list[x] = dat[x];
				$("#opponents").children(".t_" + c).children("span").html(dat[x]);
				$("#opponents").children(".t_" + c).attr("id", x);
				c++;
			}
			if(c == 3) {
				clearInterval(opponent_int);
			}
		}, "json").error(function(dat) { alert(dat.Errors);
		})
	}

	function readyOpp() {
		var c = 0, who = 0;
		for(x in opponents_list) {
			if(opponents_list[x] != undefined) {
				var jqxhr = $.post("backend/route.php", {
					"type" : "Teams",
					"cmd" : "get_ready",
					"team_id" : x
				}, function(dat) {
					if(parseInt(dat.result) == 1) {
						who++;
						$("#" + x).children("b").attr("class", "isready");
					}
					c++;
				}, "json").error(function(dat) { alert(dat.Errors);
				})
			}
		}
		if(who == 3) {
			clearInterval(ready_int);
		}
	}


	$("#create_form").submit(function() {
		var title = $("#title").val();
		if(title) {
			var jqxhr = newTeam(title, theme.id);
			jqxhr.success(function() {
				if(our_team.id != 0) {
					teams_list.list.push(our_team);
					$("#create_form").fadeOut("normal");
					$(".step_1_1 h2 span").html(our_team.title);
					$(".step_1_1").fadeIn("normal");
					loadOpp();
					if(getLen(opponents_list) < 3) {
						opponent_int = setInterval(function() {
							loadOpp();
						}, 5000);
					}
				} else {
					alert("В регистрации временно отказано!");
				}
			})
		} else {
			$(".input_wrap").css("background", "#3474AC");
		}
		return false;
	})

	$("#we_are_ready").click(function() {
		var jqxhr = $.post("backend/route.php", {
			"type" : "Teams",
			"cmd" : "team_ready",
			"team_id" : our_team.id
		}, function(dat) {

		}, "json").error(function(dat) { alert(dat.Errors);
		}).success(function() {
			$(".step_1_1 h2 span").css("color", "#337B00");
			readyOpp();
			ready_int = setInterval(function() {
				readyOpp();
			}, 5000);
		})
	});

</script>
<?
/*}
 else {
 ?>
 <div class="head">
 <div class="hgroup">
 <h1>Неа, нет такой игры!</h1>
 <h2> досвидушечки.... </h2>
 </div>
 </div>
 <? }
 *
 */
?>