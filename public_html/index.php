<?php

require __DIR__ . '/../main/start.php';

foreach (CONFIG_ROUTE_MAP as $route => $controller) {
	if (str_starts_with($route, '/')) {
		if (REQUEST_PATH === $route) {
			require_controller($controller);
			exit;
		}
	} elseif ($route === '*') {
		require_controller($controller);
		exit;
	} else {
		push_error("Ошибка конфигурации: Недопустимый маршрут '$route'.");
	}
}

push_error('Ошибка конфигурации: Не найден контроллер для обработки запроса.');
