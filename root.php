<?php


$passMD5 = "c6cd5c4d7411e292ec8e44ea7908f1bd";

session_start();


if (array_key_exists("password", $_POST)) :
	$_SESSION["auth"] = md5($_POST['password']);
	header("Location: ".$_SERVER["REQUEST_URI"]);
endif;

// var_dump($_POST);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>Document</title>
</head>
<body>
<?php

if (array_key_exists("auth", $_SESSION) && $_SESSION['auth'] == $passMD5) :


	// Объявить константу корневой директории проекта
	define("ROOT_DIR", str_replace('\\', '/', __DIR__));

	$config = json_decode(file_get_contents(ROOT_DIR."/config.json"), true);


	// Подключить автоподгрузчик класссов
	require_once(ROOT_DIR."/autoload.php");
	// Подключить бота
	$Bot = new Bot\Core($config["api"]["token"], $config["bot"]);



	// if (array_key_exists("dictionary", $_POST)) :

	// 	$dictionaryText = mb_strtolower($_POST["dictionary"]);

	// 	$f = fopen($dictionaryFile, 'w');
	// 	$result = (bool) fwrite($f, $dictionaryText);
	// 	fclose($f);
	// 	// echo $result ? "Сохранено" : "Ошибка";

	// endif;

	// Информация о боте
	?>

	<form method="post">
		<!-- Token -->
		<h2>Token</h2>
		<div>
			<input type="text" name="config[api][token]" value="<?=$config["api"]["token"];?>">
		</div>
		<!-- Webhook info -->
		<h2>Webhook info</h2>
		<?php
		foreach ($Bot->getWebhookInfo() as $key => $value):
		?>
			<div>
				<label><?=$key;?></label>
				<input type="text" value="<?=$value;?>" disabled>
			</div>
		<?php
		endforeach;
		?>
		<!-- Config -->
		<h2>Config</h2>
		<?php
		foreach ($config as $key => $value):
		?>
			<?php
			preg_match_all("#\n#iu", print_r($value, true), $matches);
			?>
			<div>
				<label><?=$key;?></label>
				<textarea rows="<?=min(count($matches[0]),10);?>" disabled><?php print_r($value);?></textarea>
				<!-- <input type="text" value="<?=min(count($matches[0]),10);?>" disabled> -->
			</div>
		<?php
		endforeach;
		?>
		<!-- <div>
			<textarea id="dictionary" rows="10" placeholder="Введите ключевые слова (каждое с новой строки)"><?php print_r($config);?></textarea>
		</div> -->
		<!-- _POST -->
		<!-- <div>
			<label for="dictionary">_POST</label>
			<textarea id="dictionary" rows="20" placeholder="Введите ключевые слова (каждое с новой строки)"><?php print_r($_POST);?></textarea>
		</div> -->

		<button type="submit">Сохранить</button>
	</form>

	<form method="post">
		<input type="hidden" name="password" value="">
		<button type="submit">Выйти</button>
	</form>
	<?php

else:

	?>
	<form method="post">
		<input type="password" name="password" placeholder="Пароль" required>
		<button type="submit">Войти</button>
	</form>
	<?php

endif;

?>

<style>
body
{
	margin: 0;
	padding: 2rem;
}
body
{
	background: #bbc4ce;
/*	color: #eee;*/
	/*  font-family: monospace;*/
	font-size: 14px;
	font-family: sans-serif;
    display: grid;
    grid-gap: 1rem;
}
form
{
	display: grid;
/*	grid-gap: 1rem;*/
	max-width: 30rem;
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
}
h2
{
	margin-top: 1rem;
	margin-bottom: 1rem;
}
label
{
	display: block;
	margin-bottom: .25rem;
}
textarea, input, button
{
	margin-bottom: .5rem;
}
textarea, input,
textarea:disabled, input:disabled
{
	box-sizing: border-box;
	width: 100%;
    background-color: #464d57;
    color: white;
	border: none;
	padding: .5rem 1rem;
	border-radius: .25rem;
	font-family: sans-serif;
}
*:disabled
{
	cursor: not-allowed;
}
textarea
{
	resize: none;
}
button, input[type=submit]
{
	border: none;
	border-radius: .25rem;
	padding: .5rem 1rem;
	background-color: #505b68;
	color: #fff;
}
</style>


	
</body>
</html