<?php

declare(strict_types=1);

namespace Exercism\RobotSimulator;

use InvalidArgumentException;

class Robot
{
    public const DIRECTION_NORTH = 'north';
    public const DIRECTION_EAST = 'east';
    public const DIRECTION_SOUTH = 'south';
    public const DIRECTION_WEST = 'west';

    /** @var int[] */
    protected $position;

    /** @var string */
    protected $direction;

    public function __construct(array $position, string $direction)
    {
        $this->position = $position;
        $this->direction = $direction;
    }

    /**
     * Make protected properties read-only.
     * __get() is slow, but it's ok for the given task.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return property_exists(self::class, $name) ? $this->$name : null;
    }

    /**
     * Turn the Robot clockwise
     * @return Robot
     */
    public function turnRight() : self
    {
        $this->direction = self::listDirectionsClockwise()[$this->direction];
        return $this;
    }

    /**
     * Turn the Robot counterclockwise
     * @return Robot
     */
    public function turnLeft() : self
    {
        $this->direction = self::listDirectionsCounterClockwise()[$this->direction];
        return $this;
    }

    /**
     * Advance the Robot one step forward
     * @return Robot
     */
    public function advance() : self
    {
        switch ($this->direction) {
            case self::DIRECTION_NORTH:
                $this->position[1]++;
                break;

            case self::DIRECTION_EAST:
                $this->position[0]++;
                break;

            case self::DIRECTION_SOUTH:
                $this->position[1]--;
                break;

            case self::DIRECTION_WEST:
                $this->position[0]--;
                break;
        }
        return $this;
    }

    /**
     * Move the Robot according to instructions: R = Turn Right, L = Turn Left and A = Advance
     * @param $instructions
     * @return $this
     */
    public function instructions($instructions) : self
    {
        if (!preg_match('/^[LAR]+$/', $instructions)) {
            throw new InvalidArgumentException('Malformed instructions');
        }

        foreach ($this->mapInstructionsToActions($instructions) as $action) {
            $this->$action();
        }
        return $this;
    }

    /**
     * List all possible clockwise turn combinations
     * @return array
     */
    public static function listDirectionsClockwise() : array
    {
        return [
            self::DIRECTION_NORTH => self::DIRECTION_EAST,
            self::DIRECTION_EAST => self::DIRECTION_SOUTH,
            self::DIRECTION_SOUTH => self::DIRECTION_WEST,
            self::DIRECTION_WEST => self::DIRECTION_NORTH,
        ];
    }

    /**
     * List all possible counterclockwise turn combinations
     * @return array
     */
    public static function listDirectionsCounterClockwise() : array
    {
        return array_flip(self::listDirectionsClockwise());
    }

    /**
     * Translate instructions string to actions
     * @param string $stringInstructions
     * @return string[]
     */
    protected function mapInstructionsToActions($stringInstructions) : array
    {
        return array_map(static function ($x) {
            return [
                'L' => 'turnLeft',
                'R' => 'turnRight',
                'A' => 'advance',
            ][$x];
        }, str_split($stringInstructions));
    }
}