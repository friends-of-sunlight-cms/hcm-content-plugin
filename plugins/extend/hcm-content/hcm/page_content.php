<?php

use Sunlight\Core;
use Sunlight\Hcm;
use Sunlight\Page\Page;
use SunlightExtend\HcmContent\ContentPageTreeFilter;

return function ($ids = '') {
    Hcm::normalizeArgument($ids, 'string');

    if (empty($ids)) {
        return '';
    }

    // get page data by id
    $ids = array_map('intval', explode('-', $ids));
    $pages = Page::getFlatTree(
        null,
        null,
        new ContentPageTreeFilter(['page_ids' => $ids, 'check_visible' => false, 'check_level' => true]),
        ['perex', 'content']
    );

    // no pages found
    if (empty($pages)) {
        return '';
    }

    // sort by the requested order of id
    $result = [];
    foreach($ids as $id){
        if(array_key_exists($id, $pages)){
            $result[$id] = $pages[$id];
        }
    }

    $pluginConfig = Core::$pluginManager->getPlugins()->getExtend('hcm-content')->getConfig();

    // render
    $output = '';
    foreach ($result as $item) {
        if (!empty($pluginConfig['page_template'])) {
            $output .= strtr($pluginConfig['page_template'], [
                '%id%' => $item['id'],
                '%slug%' => $item['slug'],
                '%perex%' => Hcm::parse($item['perex']),
                '%content%' => Hcm::parse($item['content']),
            ]);
        } else {
            $output .= Hcm::parse($item['content']);
        }
    }
    return $output;
};