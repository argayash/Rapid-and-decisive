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
				<div class="align_center">
					<button class="button" id="we_are_ready" class="">
						Мы готовы!
					</button>
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
		this.getResult = function() {
			if(!this.finish) {
				var jqxhr = $.post("backend/route.php", {
					"type" : "Teams",
					"cmd" : "result_team",
					"team_id" : this.id
				}, function(dat) {
					for(x in teams_list.list) {
						if(parseInt(teams_list.list[x].id) == dat.id) {
							teams_list.list[x].result = dat.result;
							teams_list.list[x].times = dat.times;
							teams_list.list[x].jObject.children(".result").html(dat.result);
							if(parseInt(dat.times) > 0) {
								teams_list.list[x].finish = true;
								finish_count++;
								teams_list.list[x].jObject.children(".time").html(dat.times + " c.");
							}
						}
					}
				}, "json").error(function(dat) { alert(dat.Errors);
				})
			}
		}
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
	var oppon_object_list = new Teams();
	var opponents_count = 0;
	var questions_list = [];
	var qurrent_question = 0;
	var opponent_int, result_int, ready_int;
	var ready_count = 0, finish_count = 0;
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
				if(jQuery.inArray(x, opponents_list) < 0) {
					opponents_list.push(x);
					var opp = new Team();
					opp.id = x;
					opp.title = dat[x];
					oppon_object_list.list.push(opp);
					$("#opponents").children(".t_" + c).children("span").html(dat[x]);
					$("#opponents").children(".t_" + c).attr("id", "te_" + x);
					opponents_count++;
				}
				c++;
			}
			if(opponents_count == 3) {
				clearInterval(opponent_int);
				$("#we_are_ready").fadeIn("normal");
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
			our_team.jObject = $("#r_0");
			our_team.jObject.children(".title").html(our_team.title);
			teams_list.list.push(our_team);
			for(x in oppon_object_list.list) {
				var opp = new Team();
				opp.id = oppon_object_list.list[x].id;
				opp.title = oppon_object_list.list[x].title;
				var n = parseInt(x) + 1;
				opp.jObject = $("#r_" + n);
				opp.jObject.children(".title").html(opp.title);
				teams_list.list.push(opp);
			}
			$(".step_2").fadeOut("normal");
			$(".step_3").fadeIn("normal");
			$(".active").removeClass("active");
			$(".disable:first").attr("class", "enable active");
			var jqxhr = $.post("backend/route.php", {
				"type" : "Teams",
				"cmd" : "finish_game",
				"team_id" : our_team.id
			}, function(dat) {
				result_int = setInterval(function() {
					get_all_results();
				}, 5000);
			}, "json").error(function(dat) { alert(dat.Errors);
			})
		}
	}
	get_all_results = function() {
		if(finish_count < teams_list.list.length) {
			for(x in teams_list.list) {
				teams_list.list[x].getResult();
			}
		} else {
			clearInterval(result_int);
			var results = [];
			for(x in teams_list.list) {
				var res = parseInt(teams_list.list[x].result);
				if(res > 0) {
					var times = parseInt(teams_list.list[x].times);
					var val = (questions_list.length - res) * 100 + (res * times);
					results.push({
						jObj : teams_list.list[x].jObject,
						points : val
					});
				} else {
					teams_list.list[x].jObject.css("background", "#C83737");
				}
			}
			var ci = 0;
			var colors = ["#1BAE44", "#AADA2B", "#FFD332", "#FF7F2A"];
			while(results.length > 0) {
				var min = 99999, min_j = null, min_x = 9;
				for(x in results) {
					if(results[x].points < min) {
						min_j = results[x].jObj;
						min = results[x].points;
						min_x = x;
					}
				}
				min_j.css("background", colors[ci]);
				ci++;
				results.splice(min_x, 1);
			}

		}
	}

	$("#create_form").submit(function() {
		var title = $("#title").val();
		if(title) {
			var jqxhr = newTeam(title, theme.id);
			jqxhr.success(function() {
				if(our_team.id != 0) {
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