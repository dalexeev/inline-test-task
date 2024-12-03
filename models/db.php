<?php

final class DB {

private static PDO $pdo;

public static function query(string $query, array $params = []): PDOStatement {
	$req = self::_getPDO()->prepare($query);
	if (!$req) {
		push_error('Ошибка при подготовке SQL-запроса.');
	}

	$res = $req->execute($params);
	if (!$res) {
		push_error('Ошибка при выполнении SQL-запроса.');
	}

	return $req;
}

public static function beginTransaction(): void {
	if (!self::_getPDO()->beginTransaction()) {
		push_error('Не удалось начать транзакцию.');
	}
}

public static function inTransaction(): bool {
	return self::_getPDO()->inTransaction();
}

public static function commit(): void {
	if (!self::_getPDO()->commit()) {
		push_error('Не удалось зафиксировать транзакцию.');
	}
}

public static function rollBack(): void {
	if (!self::_getPDO()->rollBack()) {
		push_error('Не удалось откатить транзакцию.');
	}
}

/// Представляет `$value` в виде SQL-литерала, безопасно экранируя спецсимволы.
public static function toSQL(null|bool|int|float|string|DateTimeInterface $value): string {
	if (is_string($value)) {
		return self::_getPDO()->quote($value);
	} elseif (is_int($value) || is_float($value)) {
		return strval($value);
	} elseif ($value === true) {
		return '1';
	} elseif ($value === false) {
		return '0';
	} elseif ($value === null) {
		return 'NULL';
	} elseif ($value instanceof DateTimeInterface) {
		return self::_getPDO()->quote($value->format(self::DATE_TIME_FORMAT));
	}
}

private static function _getPDO(): PDO {
	if (isset(self::$pdo)) {
		return self::$pdo;
	}

	self::$pdo = new PDO(...CONFIG_DB_CREDENTIALS);
	self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return self::$pdo;
}

private function __construct() {}

} // class DB
