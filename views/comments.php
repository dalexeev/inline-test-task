<?php

$query = $args['query'];
$query_words = $args['query_words'];
$comments = $args['comments'];
$empty_message = $args['empty_message'];

$found_comments_message = sprintf(
	get_plural(
		count($comments),
		'Найден %d комментарий.',
		'Найдено %d комментария.',
		'Найдено %d комментариев.',
	),
	count($comments),
);

$query_words_escaped = [];
foreach ($query_words as $query_word) {
	$query_words_escaped[] = preg_quote($query_word, '/');
}

$words_regex = '/' . implode('|', $query_words_escaped) . '/iu';

$output_comment_body = function (string $text) use ($query_words, $words_regex): void {
	// NOTE: Мёртвый код. Либо список комментариев пуст и данная функция не вызывается,
	// либо список слов и соответственно регулярное выражение не пусты.
	// Просто предусмотрение гипотетического сценария вывода комментариев без фильтра.
	if (empty($query_words)) {
		echo nl2br(html($text));
		return;
	}

	$offset = 0;
	$len = strlen($text);
	while ($offset < $len) {
		if (preg_match($words_regex, $text, $matches, PREG_OFFSET_CAPTURE, $offset) !== 1) {
			break;
		}

		$match_text = $matches[0][0];
		$match_offset = $matches[0][1];

		echo nl2br(html(substr($text, $offset, $match_offset - $offset)));
		echo "<mark>";
		echo nl2br(html($match_text));
		echo "</mark>";

		$offset = $match_offset + strlen($match_text);
	}
	echo nl2br(html(substr($text, $offset)));
};

?>
<!DOCTYPE html>
<html lang="ru">
<head>
<title>Комментарии</title>
<link rel="stylesheet" href="/assets/style.css" />
</head>
<body>

<main>
<h1>Комментарии</h1>

<form method="get" action="/comments">
	<input
		type="text"
		name="query"
		value="<?= html($query) ?>"
		placeholder="Введите запрос"
		size="32"
	/>
	<button type="submit">Найти</button>
</form>

<hr />

<?php if (empty($comments)): ?>

<i><?= html($empty_message) ?></i>

<?php else: ?>

<i><?= html($found_comments_message) ?></i>

<?php foreach ($comments as $comment): ?>

<div class="comment">
	<div class="comment-title">
		<span class="comment-author"><?= html($comment['name']) ?></span>
		<span class="comment-email">&lt;<?= html($comment['email']) ?>&gt;</span>
		прокомментировал(а) запись
		<span class="comment-post">"<?= html($comment['postTitle']) ?>"</span><span>:</span>
	</div>
	<div class="comment-body"><?php $output_comment_body($comment['body']); ?></div>
</div>

<?php endforeach; ?>

<?php endif; ?>

</main>

</body>
</html>
