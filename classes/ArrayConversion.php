<?php

namespace Neat\Object;

/**
 * Use ArrayConversion to use generic conversion from and to arrays
 */
trait ArrayConversion
{
    /**
     * Property fields
     *
     * @var \ReflectionProperty[]
     */
    protected static $propertyFields;

    /**
     * Property types
     *
     * @var string[]
     */
    protected static $propertyTypes;

    /**
     * No_Storage fields
     *
     * @var string[]
     */
    protected static $nostoreFields;

    /**
     * Gets a list of properties indexed lower_cased key
     *
     * @return \ReflectionProperty[]
     */
    private static function getPropertyFields()
    {
        if (!isset(static::$propertyFields[static::class])) {
            static::$propertyFields[static::class] = [];
            $class                                 = new \ReflectionClass(get_called_class());
            foreach ($class->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }
                $matches = null;
                if (preg_match_all('/(^|[A-Z])+([a-z0-9]|$)*/', $property->getName(), $matches)) {
                    $words = [];
                    foreach ($matches[0] as $key => $word) {
                        if (strlen($word) > 0) {
                            $words[] = strtolower($word);
                        }
                    }
                    $key = implode('_', $words);
                } else {
                    $key = strtolower($property->getName());
                }
                $property->setAccessible(true);
                static::$propertyFields[static::class][$key] = $property;
            }
        }

        return static::$propertyFields[static::class];
    }

    private static function parsePropertyFieldDocs()
    {
        if (isset(static::$propertyTypes[static::class]) && isset(static::$nostoreFields[static::class])) {
            return true;
        }

        static::$propertyTypes[static::class] = [];
        static::$nostoreFields[static::class] = [];

        foreach (static::getPropertyFields() as $key => $property) {
            $parameters = [];

            // @var => Datatype
            if (!preg_match('/\\s@var(\\s[\\w\\\\]+)?\\s/', $property->getDocComment(), $parameters)) {
                $type = '';
            } elseif (count($parameters) == 2) {
                $type = trim($parameters[1]);
            } else {
                $type = '';
            }
            static::$propertyTypes[static::class][$key] = $type;

            // @nostorage => Heeft geen veld in de database
            if (preg_match('/\\s@nostorage\\s/', $property->getDocComment(), $parameters)) {
                static::$nostoreFields[static::class][] = $key;
            }
        }

        return true;
    }

    /**
     * Gets a list of property types indexed by lower_cased key
     *
     * @return array
     */
    private static function getPropertyTypes()
    {
        static::parsePropertyFieldDocs();

        return static::$propertyTypes[static::class];
    }

    private static function getNoStorageFields()
    {
        static::parsePropertyFieldDocs();

        return static::$nostoreFields[static::class];
    }

    /**
     * Converts a property to an array value
     *
     * @param \ReflectionProperty $property Property
     * @param string $type Type of the property
     * @return mixed
     */
    private function toArrayValue(\ReflectionProperty $property, $type)
    {
        $value = $property->getValue($this);
        switch ($type) {
            case 'boolean':
            case 'bool':
                return $value ? 1 : 0;
            case 'integer':
            case 'int':
                return ($value === null) ? null : intval($value);
            case 'DateTime':
            case '\\DateTime':
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d H:i:s');
                } else {
                    return null;
                }
            default:
                return $value;
        }
    }

    /**
     * Converts to an associative array
     *
     * @return array                    Array of fields
     */
    public function toArray(): array
    {
        $array = [];
        $types = static::getPropertyTypes();
        foreach (static::getPropertyFields() as $key => $property) {
            if (!in_array($key, $this->getNoStorageFields())) {
                $array[$key] = $this->toArrayValue($property, $types[$key]);
            }
        }

        return $array;
    }

    /**
     * Converts an array value to a property
     *
     * @param \ReflectionProperty $property Property
     * @param string $type Type of the property
     * @param string|null $value
     */
    private function fromArrayValue(\ReflectionProperty $property, $type, $value)
    {
        if ($value !== null) {
            switch ($type) {
                case 'boolean':
                case 'bool':
                    $value = (boolean)$value;
                    break;
                case 'integer':
                case 'int':
                    $value = intval($value);
                    break;
                case 'DateTime':
                case '\\DateTime':
                    $value = new \DateTime($value);
                    break;
            }
        }

        $property->setValue($this, $value);
    }

    /**
     * Converts from an associative array
     *
     * @param array $array Array of fields
     */
    public function fromArray(array $array)
    {
        $types = static::getPropertyTypes();
        foreach (static::getPropertyFields() as $key => $property) {
            if (!in_array($key, $this->getNoStorageFields())) {
                $value = isset($array[$key]) ? $array[$key] : null;
                $this->fromArrayValue($property, $types[$key], $value);
            }
        }
    }
}
