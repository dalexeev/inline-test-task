<?php

require_once 'models/tables/comments.php';

const QUERY_WORD_MIN_LENGTH = 3;

$QUERY_WORD_MIN_LENGTH_MESSAGE = sprintf(
	'Запрос должен состоять как минимум из %d %s.',
	QUERY_WORD_MIN_LENGTH,
	get_plural(
		QUERY_WORD_MIN_LENGTH,
		'буквенно-числового символа',
		'подряд идущих буквенно-числовых символов',
		'подряд идущих буквенно-числовых символов',
	),
);

$query = $_GET['query'] ?? '';
$query_words = [];
$comments = [];
$empty_message = 'Комментарии не найдены.';

if (empty($query)) {
	$empty_message = 'Пустой запрос.';
} elseif (mb_strlen($query) < QUERY_WORD_MIN_LENGTH) {
	$empty_message = $QUERY_WORD_MIN_LENGTH_MESSAGE;
} else {
	preg_match_all('/[\pL\d]+/u', $query, $matches, PREG_SET_ORDER);

	$operators = [];
	foreach ($matches as $match) {
		if (mb_strlen($match[0]) >= QUERY_WORD_MIN_LENGTH) {
			// https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
			// `+` обозначает логическое И, `*` позволяет находить префиксы.
			$query_words[] = $match[0];
			$operators[] = "+{$match[0]}*";
		}
	}

	if (empty($operators)) {
		$empty_message = $QUERY_WORD_MIN_LENGTH_MESSAGE;
	} else {
		$fts_query = implode(' ', array_unique($operators));
		$comments = CommentsTable::findByText($fts_query);
	}
}

require_view('comments', [
	'query' => $query,
	'query_words' => $query_words,
	'comments' => $comments,
	'empty_message' => $empty_message,
]);
