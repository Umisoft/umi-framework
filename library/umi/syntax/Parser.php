<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\syntax;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\syntax\exception\InvalidGrammarException;
use umi\syntax\exception\LengthException;
use umi\syntax\exception\SyntaxException;
use umi\syntax\token\IToken;
use umi\syntax\token\ITokenAware;
use umi\syntax\token\TTokenAware;

/**
 * Парсер. Производит синтаксический анализ лексем языка.
 */
class Parser implements IParser, ITokenAware, ILocalizable
{

    use TLocalizable;
    use TTokenAware;

    /**
     * Действие окончания анализа.
     */
    const END = 'F';
    /**
     * Действие свертки.
     */
    const REDUCE = 'R';
    /**
     * Действие сдига.
     */
    const SHIFT = 'S';
    /**
     * Токен конца выражения - End Of Expression
     */
    const TOKEN_EOE = 'EOE';

    /**
     * @var IToken $current текущий символ(лексема)
     */
    protected $current;
    /**
     * @var array $states стек состояний
     */
    protected $states = [];
    /**
     * @var IToken[] $stack стек символов(лексем)
     */
    protected $stack = [];
    /**
     * @var IToken[] $queue очередь символов(лексем)
     */
    protected $queue = [];
    /**
     * @var array $rules правила языка
     */
    protected $rules;
    /**
     * @var array $grammar грамматика языка
     */
    protected $grammar;

    /**
     * Конструктор.
     * @param array $rules правила языка
     * @param array $grammar грамматика языка
     */
    public function __construct(array $grammar, array $rules)
    {
        $this->grammar = $grammar;
        $this->rules = $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $tokens)
    {
        $this->initParse($tokens);

        while ($this->queue) {
            $this->current = reset($this->queue);

            list($action, $rule) = $this->getAction();

            if ($action == self::END) {
                if ($this->states == [1]) {
                    return $this->current;
                } else {
                    throw new SyntaxException($this->translate(
                        'Unexpected end of expression. States stack should be equal [1]([{stack}]).',
                        ['stack' => implode(', ', $this->states)]
                    ));
                }
            }

            $this->doAction($action, $rule);
        }

        throw new InvalidGrammarException($this->translate(
            'Cannot read current token - queue is empty. Did you forget add "F" action?'
        ));
    }

    /**
     * Делает выбранное действие
     * @param string $action действие, которое необходимо выполнить
     * @param int $stateOrRule параметр действия: состояние или правило
     * @throws InvalidGrammarException
     */
    protected function doAction($action, $stateOrRule)
    {
        if ($action == 'S') {
            $this->shift($stateOrRule);
        } elseif ($action == 'R') {
            $this->reduce($stateOrRule);
        } else {
            throw new InvalidGrammarException($this->translate(
                'Invalid grammar action "{action}" for {state}-{current}. Available actions: "F", "S", "R".',
                [
                    'action'  => $action,
                    'state'   => $this->getState(),
                    'current' => $this->current->getName()
                ]
            ));
        }
    }

    /**
     * Выполняет действие сдвига. Переводит ДКА в указанное состояние.
     * Добавляет выбранное состояние в стек.
     * Добавляет текущий символ в стек.
     * @param int $state требуемое состояние
     */
    protected function shift($state)
    {
        $this->states[] = $state;
        $this->stack[] = array_shift($this->queue);
        $this->current = null;
    }

    /**
     * Выполняет действие свертки.
     * Убирает N состояний из стека.
     * Убирает N токенов из стека.
     * Добавляет в начало очереди созданный по правилу нетерминальный символ.
     * @param int $rule правило для свертки
     */
    protected function reduce($rule)
    {
        list($name, $reduce) = $this->getRule($rule);

        $this->current = null;
        $value = null;

        if ($reduce > 0) {
            $this->states = array_slice($this->states, 0, -$reduce);
            $value = array_slice($this->stack, -$reduce);
            $this->stack = array_slice($this->stack, 0, -$reduce);
        }

        array_unshift($this->queue, $this->createSyntaxNonterminal($name, $value));
    }

    /**
     * Возвращает заданное правило.
     * @param int $rule правило
     * @throws LengthException если правило записано неверно
     * @return array
     */
    protected function getRule($rule)
    {
        $set = $this->rules[$rule];

        if (count($set) != 2) {
            throw new LengthException($this->translate(
                'Invalid "{rule}" rule. Rule should be in format: [NAME, N].',
                ['rule' => $rule]
            ));
        }

        return $set;
    }

    /**
     * Возвращает действие для текущего состояния/токена.
     * @throws SyntaxException если в грамматике не содиржится заданного правила
     * @throws InvalidGrammarException если в грамматике не существует заданного состояния
     * @return array действие и параметр
     */
    protected function getAction()
    {
        $state = $this->getState();
        $token = $this->current->getName();

        if (isset($this->grammar[$state][$token])) {
            return $this->grammar[$state][$token];
        } else {
            if (!isset($this->grammar[$state])) {
                throw new InvalidGrammarException($this->translate(
                    'Unexpected state "{state}". Did you forget add this state?',
                    ['state' => $state]
                ));
            } else {
                $tokens = implode(',', array_keys($this->grammar[$state]));
                throw new SyntaxException($this->translate(
                    'Unexpected "{token}" token found. Only {tokens} tokens are possible.',
                    ['token' => $token, 'tokens' => $tokens]
                ));
            }

        }
    }

    /**
     * Возвращает текущее состояние ДКА.
     * @return int
     */
    protected function getState()
    {
        return (int) $this->states[count($this->states) - 1];
    }

    /**
     * Инициализирует стеки и очередь для парсинга.
     * @param array $tokens
     */
    private function initParse(array $tokens)
    {
        $this->queue = array_merge($tokens, [$this->createEoeToken()]);
        $this->states = [1];
        $this->stack = [];
    }

    /**
     * Создает терминал конца выражения.
     * @return IToken терминал
     */
    private function createEoeToken()
    {
        return $this->createSyntaxTerminal(self::TOKEN_EOE, self::TOKEN_EOE);
    }
}