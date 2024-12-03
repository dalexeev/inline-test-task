<?php

require_once 'models/db.php';

final class PostsTable {

public static function insert(array $posts, int $packet_max_size = 50): void {
	if ($packet_max_size <= 0) {
		push_error('$packet_max_size <= 0');
	}

	$values = "";
	foreach ($posts as $i => $post) {
		$id = DB::toSQL($post['id']);
		$userId = DB::toSQL($post['userId']);
		$title = DB::toSQL($post['title']);
		$body = DB::toSQL($post['body']);

		$values .= "($id,$userId,$title,$body)";

		if (($i + 1) % $packet_max_size === 0 || $i === count($posts) - 1) {
			DB::query("INSERT INTO `posts` (`id`, `userId`, `title`, `body`) VALUES $values");
			$values = "";
		} else {
			$values .= ",";
		}
	}
}

private function __construct() {}

} // class PostsTable
