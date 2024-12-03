<?php

require_once 'models/db.php';

final class CommentsTable {

public static function insert(array $comments, int $packet_max_size = 50): void {
	if ($packet_max_size <= 0) {
		push_error('$packet_max_size <= 0');
	}

	$values = "";
	foreach ($comments as $i => $comment) {
		$id = DB::toSQL($comment['id']);
		$postId = DB::toSQL($comment['postId']);
		$name = DB::toSQL($comment['name']);
		$email = DB::toSQL($comment['email']);
		$body = DB::toSQL($comment['body']);

		$values .= "($id,$postId,$name,$email,$body)";

		if (($i + 1) % $packet_max_size === 0 || $i === count($comments) - 1) {
			DB::query("INSERT INTO `comments` (`id`, `postId`, `name`, `email`, `body`) VALUES $values");
			$values = "";
		} else {
			$values .= ",";
		}
	}
}

public static function findByText(string $fts_query): array {
	$fts_query = DB::toSQL($fts_query);

	$res = DB::query(
		"SELECT c.`name`, c.`email`, c.`body`, p.`title` AS `postTitle`
		FROM `comments` c
		JOIN `posts` p ON c.`postId` = p.`id`
		WHERE MATCH (c.`body`) AGAINST ($fts_query IN BOOLEAN MODE)
		ORDER BY c.`postId`"
	);

	return $res->fetchAll();
}

private function __construct() {}

} // class CommentsTable
