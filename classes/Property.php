<?php

namespace Neat\Object;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionProperty;

class Property
{
    /**
     * @var ReflectionProperty
     */
    protected $reflection;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * Property constructor
     *
     * @param ReflectionProperty $reflection
     * @note Activates the reflection's accessible flag
     */
    public function __construct(ReflectionProperty $reflection)
    {
        $reflection->setAccessible(true);

        $this->reflection = $reflection;

        if (preg_match('/\\s@var\\s([\\w\\\\]+)(?:\\|null)?\\s/', $reflection->getDocComment(), $matches)) {
            $this->type = strtr(ltrim($matches[1], '\\'), [
                'integer' => 'int',
                'boolean' => 'bool',
            ]);
        }
    }

    /**
     * Get name
     *
     * @return string
     */
    public function name()
    {
        return $this->reflection->getName();
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Get Value
     *
     * @param object $object
     * @return mixed
     */
    public function get($object)
    {
        $value = $this->reflection->getValue($object);
        if ($value === null) {
            return null;
        }

        switch ($this->type) {
            case 'bool':
                return $value ? 1 : 0;
            case 'int':
                return (int)$value;
            case 'DateTime':
            case 'DateTimeImmutable':
                if (!$value instanceof DateTimeInterface) {
                    $value = new DateTime($value);
                }

                return $value->format('Y-m-d H:i:s');
            default:
                return $value;
        }
    }

    /**
     * Set value
     *
     * @param object $object
     * @param mixed  $value
     * @return void
     */
    public function set($object, $value)
    {
        if ($value !== null) {
            switch ($this->type) {
                case 'bool':
                case 'int':
                    settype($value, $this->type);
                    break;
                case 'DateTime':
                    $value = new DateTime($value);
                    break;
                case 'DateTimeImmutable':
                    $value = new DateTimeImmutable($value);
                    break;
            }
        }

        $this->reflection->setValue($object, $value);
    }

    /**
     * Is static?
     *
     * @return bool
     */
    public function static()
    {
        return $this->reflection->isStatic();
    }

    /**
     * Get doc block
     *
     * @return string|false
     */
    public function docBlock()
    {
        return $this->reflection->getDocComment();
    }
}
