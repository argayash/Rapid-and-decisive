<div class="head">
	<div class="hgroup">
		<h1>Создание интерактивной игры</h1>
	</div>
	<ol>
		<li class="enable active">
			<span>Спланируйте мероприятие</span>
		</li>
		<li class="disable">
			<span>Напишите вопросы</span>
		</li>
		<li class="disable">
			<span>Проведите игру</span>
		</li>
	</ol>
</div>
<div class="main">
	<ul class="steps">
		<li class="step_1">
			<form id="create_form" class="input_form" method="post">
				<label for="title">название игры:</label>
				<div class="input_wrap">
					<input type="text" class="big" name="title" id="title"/>
				</div>
				<label for="description">описание:</label>
				<div class="input_wrap" id="dop">
					<textarea name="description" id="description"></textarea>
					<input class="normal" id="fileupload" type="file" name="files[]" multiple>
				</div>
				<button>
					Создать игру!
				</button>
			</form>
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
	var theme = {
		title : "",
		href : "",
		id : 0
	}
	$(function() {
		$('#fileupload').fileupload({
			dataType : 'json',
			url : 'upload.php',
			done : function(e, data) {
				$("#fileupload").fadeOut("normal").after("<img class='tumbnail' url='http://localhost" + data.result[0].url + "' src='http://localhost" + data.result[0].thumbnail_url + "' alt='' />");
			}
		});
		$('#question_upload').fileupload({
			dataType : 'json',
			url : 'upload.php',
			done : function(e, data) {
				$("#question_upload").fadeOut("normal").after("<img class='tumbnail question_img' url='http://localhost" + data.result[0].url + "' src='http://localhost" + data.result[0].thumbnail_url + "' alt='' />");
			}
		});
		$("#create_form").submit(function() {
			var img = "";
			if($("#dop .tumbnail").attr("src") != undefined) {
				img = "<img src='" + $("#dop .tumbnail").attr("url") + "' alt=''/>";
			}
			var description = "<div>" + $("#description").val() + "</div>" + img;
			var title = $("#title").val();
			var jqxhr = $.post("backend/route.php", {
				"type" : "Theme",
				"cmd" : "new_theme",
				"title" : title,
				"description" : description
			}, function(dat) {
				theme.id = dat.Id;
				theme.title = dat.Title;
				theme.href = dat.Href;
				var href = location.href;
				$(".step_3 .game a").attr("href", href + "?part=game&theme=" + theme.href);
				$(".step_3 .game span").html(href + "?part=game&theme=" + theme.href);
				$(".step_3 .result a").attr("href", href + "?part=result&theme=" + theme.href);
				$(".step_3 .result span").html(href + "?part=result&theme=" + theme.href);
			}, "json").error(function(dat) { alert(dat.Errors);
			})
			$("#step2_title").html(theme.title);
			$(".step_1").fadeOut("slow");
			$(".step_2").fadeIn("slow");
			$(".active").removeClass("active");
			$(".disable:first").attr("class", "enable active");
			return false;
		});
		$("#create_question").submit(function() {
			var tmp = $("#template_question").clone();
			var img = "";
			if($(".question_img").attr("src") != undefined) {
				img = "<img src='" + $(".question_img").attr("url") + "' alt=''/>";
			}
			var title = $("#question_title").val() + img;
			$(".question_img").remove();
			var jqxhr = $.post("backend/route.php", {
				"type" : "Question",
				"cmd" : "new_question",
				"title" : title,
				"theme_id" : theme.id
			}, function(dat) {
				title = dat.Title;
				tmp.children("h3").html(title);
				tmp.children(".answer_form").attr("question_id", dat.Id);
				$("#qw_list").append("<li>" + tmp.html() + "</li>");
				$("#question_upload").fadeIn("normal");
				$("#qw_list .answer_file").fileupload({
					dataType : 'json',
					url : 'upload.php',
					done : function(e, data) {
						$(this).after("<img class='tumbnail' url='http://localhost" + data.result[0].url + "' src='http://localhost" + data.result[0].thumbnail_url + "' alt='' />");
					}
				});
				$("#question_title").val("");
			}, "json").error(function(dat) { alert(dat.Errors);
			});
			return false
		});
		$(".add_answer").live("click", function() {
			var parent = $(this).parent(".answer_form");

			var img = "";
			if(parent.children(".tumbnail").attr("src") != undefined) {
				img = "<img src='" + parent.children(".tumbnail").attr("url") + "' alt=''/>";
			}
			var title = "<div>" + parent.children(".answer_title").val() + "</div>" + img;
			var right = parent.children(".answer_right").val();
			var jqxhr = $.post("backend/route.php", {
				"type" : "Answer",
				"cmd" : "new_answer",
				"title" : title,
				"question_id" : parent.attr("question_id"),
				"right" : right
			}, function(dat) {
				var classe = "";
				if(parseInt(right) == 1) {
					classe = "true";
				}
				var mp = parent.parent("li");
				mp.children(".answers").append("<li class='" + classe + "'>" + dat.Title + "</li>");
				parent.children(".tumbnail").remove();
				parent.children(".answer_title").val("");
			}, "json").error(function(dat) { alert(dat.Errors);
			})
		});
		$("#on_step3").click(function() {
			$(".step_2").fadeOut("slow");
			$(".step_3").fadeIn("slow");
			$(".active").removeClass("active");
			$(".disable:first").attr("class", "enable active");
		})
	});

</script>