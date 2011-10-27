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
					<button class="button" id="start_game">
						Начать игру!
					</button>
				</div>
			</div>
		</li>
		<li class="step_2">
			<div id="current_question">
				<h3>sadsd</h3>
				<ol class="answers"></ol>
			</div>
			<div class="hint"></div>
		</li>
		<li class="step_3">
			<h2>Результат игры:</h2>
			<ul class="offset">
				<li id="r_0">
					<div class="title"></div>
					<div class="result"></div>
					<div class="time"></div>
				</li>
				<li id="r_1">
					<div class="title"></div>
					<div class="result"></div>
					<div class="time"></div>
				</li>
				<li id="r_2">
					<div class="title"></div>
					<div class="result"></div>
					<div class="time"></div>
				</li>
				<li id="r_3">
					<div class="title"></div>
					<div class="result"></div>
					<div class="time"></div>
				</li>
			</ul>
		</li>
	</ul>
</div>
<div class="footer"></div>
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
		this.finish = false;
		this.jObject = null;
	}
	Teams = function() {
		this.our_team = 0;
		this.list = [];
	}
	Question = function(id, title) {
		this.id = id;
		this.title = title;
		this.getAnswers = function() {
			$(".step_2 h3").html(this.title);
			$(".step_2 .answers").html();
			var jqxhr = $.post("backend/route.php", {
				"type" : "Answer",
				"cmd" : "get_list_answers",
				"question_id" : this.id
			}, function(dat) {
				var app = "";
				for(x in dat) {
					app += "<li id='" + x + "'>" + dat[x] + "</li>";
				}
				$(".step_2 .answers").html(app);
			}, "json").error(function(dat) { alert(dat.Errors);
			})
		}
	}
	/*GLOBAL VARS*/
	var theme = new Theme();
	theme.getId();
	var our_team = new Team();
	var teams_list = new Teams();
	var opponents_list = [];
	var questions_list = [];
	var qurrent_question = 0;
	var opponent_int, result_int, ready_int;
	var ready_count = 0;
	var hints = ["Ну быстрее же!", "А быстрее можете?", "Правильного ответа здесь нет)", "Правильный ответ №2", "Правильный ответ №1", "Правильный ответ №3", "Последний ответ правильный!", "Ну вооот...", "Ну ведь не правильно же ответили..."];
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
				opponents_list.push(x);
				$("#opponents").children(".t_" + c).children("span").html(dat[x]);
				$("#opponents").children(".t_" + c).attr("id", "te_" + x);
				c++;
			}
			if(c == 3) {
				clearInterval(opponent_int);
			}
		}, "json").error(function(dat) { alert(dat.Errors);
		})
	}

	function readyOpp() {
		for(var i = 0; i < opponents_list.length; i++) {
			//alert(opponents_list[i]);
			if(opponents_list[i] != undefined) {
				var jqxhr = $.post("backend/route.php", {
					"type" : "Teams",
					"cmd" : "get_ready",
					"team_id" : opponents_list[i]
				}, function(dat) {
					if(parseInt(dat.result) > 0) {
						ready_count++;
						$("#te_" + dat.result).children("b").attr("class", "isready");
					}
				}, "json").error(function(dat) { alert(dat.Errors);
				})
			}
		}
	}

	set_question = function() {
		if(qurrent_question < questions_list.length) {
			if(qurrent_question > 0) {
				$(".step_2 .hint").html(hints[Math.floor(hints.length * Math.random())])
			}
			questions_list[qurrent_question].getAnswers();
		} else {
			our_team.finish = true;
			teams_list.list.push(our_team);
			for(x in opponents_list) {
				var opp = new Team();
				opp.id = x;
				opp.title = opponents_list[x];
				teams_list.list.push(opp);
			}
			$(".step_2").fadeOut("normal");
			var jqxhr = $.post("backend/route.php", {
				"type" : "Teams",
				"cmd" : "finish_game",
				"team_id" : our_team.id
			}, function(dat) {

			}, "json").error(function(dat) { alert(dat.Errors);
			})
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
			$("#we_are_ready").fadeOut("normal");
		}, "json").error(function(dat) { alert(dat.Errors);
		}).success(function() {
			$(".step_1_1 h2 span").css("color", "#337B00");
			readyOpp();
			ready_int = setInterval(function() {
				if(ready_count == 3) {
					clearInterval(ready_int);
					$("#start_game").fadeIn("normal");
				} else {
					ready_count = 0;
				}
				readyOpp();

			}, 8000);
		})
	});

	$("#start_game").click(function() {
		var jqxhr = $.post("backend/route.php", {
			"type" : "Teams",
			"cmd" : "start_game",
			"team_id" : our_team.id
		}, function(dat) {
			$("#we_are_ready").fadeOut("normal");
		}, "json").error(function(dat) { alert(dat.Errors);
		}).success(function() {
			var jqxhr = $.post("backend/route.php", {
				"type" : "Question",
				"cmd" : "get_list_questions",
				"theme_id" : theme.id
			}, function(dat) {
				for(x in dat) {
					questions_list.push(new Question(x, dat[x]))
				}
				set_question();
				$(".step_1").fadeOut("normal");
				$(".step_2").fadeIn("normal");
			}, "json").error(function(dat) { alert(dat.Errors);
			}).success(function() {
				$(".active").removeClass("active");
				$(".disable:first").attr("class", "enable active");
			})
		})
	})
	$(".answers li").live("click", function() {
		var jqxhr = $.post("backend/route.php", {
			"type" : "Answer",
			"cmd" : "reply",
			"answer_id" : $(this).attr("id"),
			"team_id" : our_team.id,
			"question_id" : questions_list[qurrent_question].id
		}, function(dat) {
			our_team.result = dat.result;
		}, "json").error(function(dat) { alert(dat.Errors);
		}).success(function() {
			qurrent_question++;
			set_question();
		})
	})
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