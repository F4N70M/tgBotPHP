<?php
spl_autoload_register(
	function ($class) {
		$class = str_replace('\\', '/', $class);

		preg_match("#Bot/(.+)#", $class, $matches);
		if ($matches)
			$TgClass = $matches[1];
		$file  = __DIR__ . '/Bot/' . $TgClass . '.php';
		if (is_file($file)) {
			// echo "<pre>";
			// print_r("autoload.php : ".$class);
			// echo "</pre>";
			require_once $file;
			return;
		}
	}
);