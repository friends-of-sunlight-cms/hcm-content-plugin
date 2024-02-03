<?php

namespace SunlightExtend\HcmContent;

use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Util\Form;
use Sunlight\Util\Request;

class ConfigAction extends BaseConfigAction
{
    protected function getFields(): array
    {
        $config = $this->plugin->getConfig();

        return [
            'page_template' => [
                'label' => _lang('hcm-content.config.page_template'),
                'input' => '<textarea name="page_template" class="areasmallwide">' . Request::post('page_template', $config['page_template']) . '</textarea>'
                . '<p class="hint">' . _lang('hcm-content.config.page_template.hint') . '</p>',
                'type' => 'text',
            ],
            'article_template' => [
                'label' => _lang('hcm-content.config.article_template'),
                'input' => '<textarea name="article_template" class="areasmallwide">' . Request::post('article_template', $config['article_template']) . '</textarea>'
                . '<p class="hint">' . _lang('hcm-content.config.article_template.hint') . '</p>',
                'type' => 'text',
            ]
        ];
    }
}