<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal;

use Doctrine\DBAL\Logging\DebugStack;

/**
 * Class SqlLogger
 */
class SqlLogger extends DebugStack
{
    /**
     * Вернет логированные запросы
     *
     * @param bool $withValues подставлять ли реальные значения в логированные запросы
     *
     * @return array
     */
    public function getQueries($withValues = false)
    {
        return array_values(
            array_map(
                function ($a) use ($withValues) {
                    if ($withValues && isset($a['params']) && is_array($a['params'])) {
                        // make correct NULLs
                        array_walk(
                            $a['params'],
                            function (&$param) {
                                if ($param === null) {
                                    $param = 'NULL';
                                }
                            }
                        );

                        return strtr($a['sql'], $a['params']);
                    } else {
                        return $a['sql'];
                    }
                },
                $this->queries
            )
        );
    }

    /**
     * Вернет типы логирванных запросы, с опциональным присоединением использованных параметров
     *
     * @param bool $withParams добавить ли логированные параметры к записям
     *
     * @return array [ ['select', [':foo'=>'foo', ':bar'=>121 ...]] ]
     */
    public function getQueryTypesWithParams($withParams = true)
    {
        $queries = [];
        $kwRe = '/^\s*[`"]?(select|update|insert|delete|drop|truncate|start|rollback|commit)[`"]?\b/i';
        foreach ($this->queries as $a) {
            $matches = [];
            $query = [];
            if (preg_match($kwRe, $a['sql'], $matches)) {
                $query[0] = strtolower($matches[1]);
                if ($withParams) {
                    $query = [
                        strtolower($matches[1]),
                        isset($a['params']) && is_array($a['params']) ? $a['params'] : []
                    ];
                } else {
                    $query = strtolower($matches[1]);
                }
                $queries[] = $query;
            } else {
                $queries[] = false;
            }
        }

        return $queries;
    }

    /**
     * Вернет логированные запросы с ограничением по типу
     *
     * @param string $type select|update|insert|delete
     *
     * @return array
     */
    public function getOnlyQueries($type)
    {
        return array_values(
            array_filter(
                $this->getQueries(),
                function ($q) use ($type) {
                    return preg_match('/^' . $type . '\s+/i', $q);
                }
            )
        );
    }

    /**
     * Сбросит лог запросов
     */
    public function resetQueries()
    {
        $this->queries = [];
    }
}
