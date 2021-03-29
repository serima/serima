<?php

$posts = [];

// Zenn
$zenn =  simplexml_load_string(file_get_contents('https://zenn.dev/serima/feed'));
foreach ($zenn->channel->item as $item) {
    $posts[]  = [
        'title' => $item->title,
        'date' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
        'type' => 'zenn',
        'url' => $item->link,
    ];
}

// Qiita
$qiita = simplexml_load_string(file_get_contents('https://qiita.com/serima/feed.atom'));
foreach ($qiita->entry as $entry) {
    $posts[]  = [
        'title' => $entry->title,
        'date' => date('Y-m-d H:i:s', strtotime($entry->published)),
        'type' => 'qiita',
        'url' => $entry->url,
    ];
}

// note
$note = simplexml_load_string(file_get_contents('https://note.com/serima/rss'));
foreach ($note->channel->item as $item) {
    $posts[]  = [
        'title' => $item->title,
        'date' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
        'type' => 'note',
        'url' => $item->link,
    ];
}

// Speaker Deck
$speakerdeck = simplexml_load_string(file_get_contents('https://speakerdeck.com/serima.atom'));
foreach ($speakerdeck->entry as $entry) {
    $posts[]  = [
        'title' => $entry->title,
        'date' => date('Y-m-d H:i:s', strtotime($entry->published)),
        'type' => 'speakerdeck',
        'url' => $entry->link->attributes()->href,
    ];
}

$sort_arr = array_map("strtotime", array_column($posts, "date"));
array_multisort($sort_arr, SORT_DESC, $posts);

$dist_md = '';

foreach (array_splice($posts, 0, 7) as $post) {
    $dist_md .= "- ![](platform_icons/${post['type']}.png) [${post['title']}](${post['url']}) [![はてなブックマーク数](https://b.hatena.ne.jp/entry/image/${post['url']})](https://b.hatena.ne.jp/entry/${post['url']})\n";
}

file_put_contents('README.md', preg_replace('/<!--\[START POSTS\]-->.*<!--\[END POSTS\]-->/s', "<!--[START POSTS]-->\n${dist_md}<!--[END POSTS]-->", file_get_contents('README.md')));
