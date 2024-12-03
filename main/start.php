<?php

// Данный скрипт должен быть загружен первым во **всех** точках входа.

// Все пути должны быть относительно корня проекта.
chdir(__DIR__ . '/..');
set_include_path(getcwd());

require 'main/globals.php';

require 'config/config.php';
