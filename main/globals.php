<?php

/// Путь текущего запроса относительно корня сайта.
define('REQUEST_PATH', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));

function require_controller(string $controller, array $args = []): void {
	require "controllers/$controller.php";
}

function require_view(string $view, array $args = []): void {
	require "views/$view.php";
}

/// Экранирует специальные символы HTML в `$value`.
function html(string|int|null $value): string {
	return htmlspecialchars(strval($value), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
}

function push_error(string $message): never {
	trigger_error($message, E_USER_ERROR); // TODO: Deprecated in PHP 8.4.
	exit;
}

function push_warning(string $message): void {
	trigger_error($message, E_USER_WARNING);
}

function push_notice(string $message): void {
	trigger_error($message, E_USER_NOTICE);
}

function push_deprecated(string $message): void {
	trigger_error($message, E_USER_DEPRECATED);
}

function get_plural(int $n, string $form0, string $form1, string $form2): string {
	if ($n % 10 === 1 && $n % 100 !== 11) {
		return $form0;
	} elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)) {
		return $form1;
	} else {
		return $form2;
	}
}
