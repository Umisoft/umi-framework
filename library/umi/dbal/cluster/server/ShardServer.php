<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster\server;

/**
 * Shard-сервер позволяет выполнять запросы на выборку и модификацию данных,
 * может использоваться моделями для шардинга данных.
 */
class ShardServer extends BaseServer implements IShardServer
{
}
