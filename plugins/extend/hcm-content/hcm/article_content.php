<?php

use Sunlight\Article;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Hcm;
use Sunlight\Router;

return function ($id = '') {
    Hcm::normalizeArgument($id, 'string');

    if (empty($id)) {
        return '';
    }

    // get article slug from id
    if (is_numeric($id)) {
        $id = DB::result(DB::query('SELECT slug FROM ' . DB::table('article') . ' WHERE id = ' . DB::val($id)), 0);
    }

    // load article data
    $article = Article::find($id);
    if ($article === false) {
        return '';
    }

    $pluginConfig = Core::$pluginManager->getPlugins()->getExtend('hcm-content')->getConfig();

    // render
    $output = '';
    if (!empty($pluginConfig['article_template'])) {
        $thumbnail = null;
        if (isset($article['picture_uid'])) {
            $thumbnail = Article::getThumbnail($article['picture_uid']);
        }

        $output .= strtr($pluginConfig['article_template'], [
            '%id%' => $article['id'],
            '%slug%' => $article['slug'],
            '%perex%' => Hcm::parse($article['perex']),
            '%picture%' => $thumbnail !== null ? '<img class="perex-image" src="' . _e(Router::file($thumbnail)) . '" alt="' . $article['title'] . '">' : '',
            '%content%' => Hcm::parse($article['content']),
        ]);
    } else {
        $output .= Hcm::parse($article['perex']);
        $output .= Hcm::parse($article['content']);
    }
    return $output;
};
