<?php
namespace Application\Actions\Helper;

use Poirot\AaResponder\AbstractAResponder;

class CycleAction extends AbstractAResponder
{
    protected static $_cycle_actions = [
        # $action_unique_identifier =>
    ];

    protected $action;
    protected $steps;
    /** @var array using on reset */
    protected $steps_reset;
    protected $step_counter = 1;

    /** @var CycleAction */
    protected $dependent;


    /**
     * Cycling execution of given action on defined steps
     *
     * @param string|callable $action
     * @param int|array       $steps
     * @param bool            $reset
     *
     * @return CycleAction Clone this
     */
    function __invoke($action = null, $steps = 1, $reset = true)
    {
        $this->__registerCycleAction($action, $steps);

        /** @var CycleAction $currentCycle */
        $currentCycle = self::$_cycle_actions[$this->__getCycleIdentifier()];

        ## always store last cycle step number and reset on invoke if necessary
        if ($reset && $currentCycle->getCounter() >= max($currentCycle->steps))
            $currentCycle->reset();

        if ($currentCycle->hasMetStep()) {
            ## step met and must be invoked
            $currentCycle->call();
        }

        $currentCycle->increment();
        return $currentCycle;
    }

    function call()
    {
        $action = $this->action;
        if (is_string($action))
            $action = function () {
                echo $this->action;
            };

        return call_user_func($action);
    }

    function __toString()
    {
        return (string) $this->call();
    }

    /**
     * Increment step to next value
     *
     * @return $this
     */
    function increment()
    {
        $this->step_counter++;

        return $this;
    }

    /**
     * Get Current Counter Step
     *
     * @return int
     */
    function getCounter()
    {
        return $this->step_counter;
    }

    /**
     * Reset Step Counter
     *
     * @return $this
     */
    function reset()
    {
        $this->step_counter = 1;
        $this->steps        = $this->steps_reset;

        return $this;
    }

    /**
     * Has Current Step Met On Cycle
     *
     * @return bool
     */
    function hasMetStep()
    {
        if ($this->dependent) {
            return in_array($this->dependent->getCounter(), $this->steps);
        }

        return in_array($this->getCounter(), $this->steps);
    }

    /**
     * Set Dependent Cycle Action
     *
     * @param CycleAction $cycle
     *
     * @return $this
     */
    function setDependent(CycleAction $cycle)
    {
        $this->dependent = $cycle;

        return $this;
    }

    protected function __registerCycleAction($action, $steps)
    {
        if (!is_callable($action) && !is_string($action))
            throw new \InvalidArgumentException(sprintf(
                'Action must be string or callable function (%s) given.'
                , \Poirot\Core\flatten($action)
            ));

        if (is_int($steps))
            $steps = [$steps];

        if (!is_array($steps))
            throw new \InvalidArgumentException(sprintf(
                'Unknow cycle steps provided. given (%s)'
                , \Poirot\Core\flatten($steps)
            ));

        // ...

        $this->action = $action;
        if (is_array($steps) && current($steps) instanceof CycleAction)
            ## cycle('</div>', [$cycle, 1])
            $this->setDependent(array_shift($steps));

        $this->steps       = $steps;
        $this->steps_reset = $steps;

        if (!isset(self::$_cycle_actions[$this->__getCycleIdentifier()]))
            self::$_cycle_actions[$this->__getCycleIdentifier()] = clone $this;
    }

    /**
     * Generate unique identifier for cycle call
     *
     * @return string
     */
    protected function __getCycleIdentifier()
    {
        $identifier = md5(\Poirot\Core\flatten($this->action));

        return $identifier;
    }
}
