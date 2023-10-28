<?php

namespace SunlightExtend\HcmContent;

use Sunlight\Database\Database as DB;
use Sunlight\Database\TreeFilterInterface;
use Sunlight\Database\TreeReader;
use Sunlight\Page\Page;
use Sunlight\User;

class ContentPageTreeFilter implements TreeFilterInterface
{
    /** @var array */
    private $options;
    /** @var string */
    private $sql;

    /**
     * Supported options:
     * ------------------
     * - page_ids ([])     page ids
     * - check_visible (1) check page's visible column 1/0
     * - check_level (1)   check user and page level 1/0
     * - check_public (1)  check page's public column 1/0
     *
     * @param array{
     *     page_ids?: array,
     *     check_visible?: bool,
     *     check_level?: bool,
     *     check_public?: bool,
     * } $options see description
     */
    function __construct(array $options)
    {
        // defaults
        $options += [
            'page_ids' => [],
            'ord_start' => null,
            'ord_end' => null,
            'ord_level' => 0,
            'check_visible' => true,
            'check_level' => true,
            'check_public' => true,
        ];

        $this->options = $options;
        $this->sql = $this->compileSql($options);
    }

    function filterNode(array $node, TreeReader $reader): bool
    {
        return
            /* visibility */        (!$this->options['check_visible'] || $node['visible'])
            /* page level */        && (!$this->options['check_level'] || $node['level'] <= User::getLevel())
            /* page public */       && (!$this->options['check_public'] || User::isLoggedIn() || $node['public'])
            /* separator  check */  && $node['type'] != Page::SEPARATOR
            /* page ids */          && (!empty($this->options['page_ids']) && in_array($node['id'], $this->options['page_ids']));
    }

    function acceptInvalidNodeWithValidChild(array $invalidNode, array $validChildNode, TreeReader $reader): bool
    {
        if (
            ($this->options['ord_start'] !== null || $this->options['ord_end'] !== null)
            && $invalidNode['node_level'] == $this->options['ord_level']
        ) {
            // always reject invalid nodes which have been rejected by order-filtering at that level
            return false;
        }

        return true;
    }

    function getNodeSql(TreeReader $reader): string
    {
        return $this->sql;
    }

    private function compileSql(array $options): string
    {
        // base conditions
        $sql = '%__node__%.type!=' . Page::SEPARATOR;

        if ($options['check_visible']) {
            $sql .= ' AND %__node__%.visible=1';
        }

        if ($options['check_level']) {
            $sql .= ' AND %__node__%.level<=' . User::getLevel();
        }

        // order constraints
        if ($options['ord_start'] !== null || $options['ord_end'] !== null) {
            $ordSql = '';

            if ($options['ord_start'] !== null) {
                $ordSql .= '%__node__%.ord>=' . DB::val($options['ord_start']);
            }

            if ($options['ord_end'] !== null) {
                if ($options['ord_start'] !== null) {
                    $ordSql .= ' AND ';
                }

                $ordSql .= '%__node__%.ord<=' . DB::val($options['ord_end']);
            }

            $sql .= ' AND (%__node__%.node_level!=' . DB::val($options['ord_level']) . ' OR ' . $ordSql . ')';
        }

        return $sql;
    }
}