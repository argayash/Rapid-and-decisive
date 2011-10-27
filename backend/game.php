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
	Team = function() {
		this.Id = 0;
		this.times = 0;
		this.result = 0;
		this.ready = 0;
		this.title = 0;
		this.newTeam = function() {

		}
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
	}
</script>