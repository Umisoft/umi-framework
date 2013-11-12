<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\ns;

use umi\http\request\Request;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\entity\ns\SessionNamespace;
use umi\session\ISessionManager;
use umi\session\toolbox\SessionTools;
use utest\session\SessionTestCase;

/**
 * Тест сессий
 */
class SessionNamespaceTest extends SessionTestCase
{
    /**
     * @var ISessionManager $manager
     */
    protected $manager;
    /**
     * @var ISessionNamespace $session
     */
    protected $session;

    protected function setUpFixtures()
    {
        $_SESSION = [];
        session_id(md5(time()));

        $request = new Request();
        $this->resolveOptionalDependencies($request);

        /**
         * @var SessionTools $sessionTools
         */
        $sessionTools = $this->getTestToolkit()
            ->getToolbox(SessionTools::NAME);

        $this->session = new SessionNamespace('test');
        $this->resolveOptionalDependencies($this->session);

        $this->manager = $sessionTools->getManager();
    }

    public function testSession()
    {
        $this->assertEquals('test', $this->session->getName(), 'Ожидается, что имя сессии не установлено.');

        $this->assertEmpty(
            $this->session->toArray(),
            'Ожидается, что сессия стартует пустой'
        );

        $this->session['key'] = 'value';
        $this->assertEquals(
            1,
            count($this->session),
            'Ожидается, что в сессии одно значение'
        );

        $this->assertEquals(
            ['key' => 'value'],
            $this->session->toArray(),
            'Ожидается, что в сессии будет одно значение'
        );

        $this->assertTrue(
            isset($this->session['key']),
            'Ожидается, что значение будет найдено в сессии'
        );

        unset($this->session['key']);
        $this->assertEmpty($this->session->toArray(), 'Ожидается, что сессия будет пустой');
    }

    public function testSessionArray()
    {
        $this->session['key'] = 'value';

        $this->assertEquals(
            [
                'test' => [
                    'meta'   => [],
                    'values' => [
                        'key' => 'value'
                    ]
                ]
            ],
            $_SESSION
        );

        $this->session->clear();
    }

    public function testSessionReading()
    {
        $_SESSION['test2'] = [
            'meta'   => [],
            'values' => [
                'option' => 'value'
            ]
        ];

        $ns = new SessionNamespace('test2', $this->manager);
        $this->assertEquals('value', $ns['option'], 'Ожидается, что значения сесси будут подхвачены.');
    }

    public function testTraversable()
    {
        $this->session['key1'] = 'val1';
        $this->session['key2'] = 'val2';

        $session = [];

        foreach ($this->session as $key => $val) {
            $session[$key] = $val;
        }

        $this->assertEquals(['key1' => 'val1', 'key2' => 'val2'], $session);

        $this->session->clear();

        $this->assertEmpty($this->session->toArray(), 'Ожидается, что в сессии не будет значений');
    }

    public function testMetadata()
    {
        $this->session->setMetadata('test', 'value');
        $this->assertEmpty($this->session->toArray(), 'Ожидается, что значения метаданных не храняться в контейнере');

        $this->assertEquals('value', $this->session->getMetadata('test'), 'Ожидается, что метаданные будут сохранены');

        $this->session->clear();
        $this->assertNull($this->session->getMetadata('test'), 'Ожидается, что значение будет удалено');
    }

    public function testLazyStart()
    {
        $this->assertEquals(
            ISessionManager::STATUS_INACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия не была запущена.'
        );

        $this->session
            ->setMetadata('key', 'val');

        $this->assertEquals(
            ISessionManager::STATUS_INACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия не была запущена.'
        );

        $this->session->get('key');
        $this->session->has('key');

        $this->assertEquals(
            ISessionManager::STATUS_INACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия не была запущена.'
        );

        $this->session->set('k', 'v');

        $this->assertEquals(
            ISessionManager::STATUS_ACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия была запущена.'
        );

        $this->assertEquals(
            [
                'test' => [
                    'meta'   => [
                        'key' => 'val'
                    ],
                    'values' => [
                        'k' => 'v'
                    ]
                ]
            ],
            $_SESSION
        );
    }
}
