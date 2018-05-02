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
    private static $propertyFields;

    /**
     * Property types
     *
     * @var string[]
     */
    private static $propertyTypes;

    /**
     * No_Storage fields
     *
     * @var string[]
     */
    private static $nostoreFields;

    /**
     * Gets a list of properties indexed lower_cased key
     *
     * @return \ReflectionProperty[]
     */
    private static function getPropertyFields()
    {
        if (!isset(self::$propertyFields)) {
            self::$propertyFields = [];
            $class = new \ReflectionClass(get_called_class());
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
                self::$propertyFields[$key] = $property;
            }
        }

        return self::$propertyFields;
    }

    private static function parsePropertyFieldDocs()
    {
        self::$propertyTypes = [];
        self::$nostoreFields = [];

        foreach (self::getPropertyFields() as $key => $property) {
            $parameters = [];

            // @var => Datatype
            if (!preg_match('/\\s@var(\\s[\\w\\\\]+)?\\s/', $property->getDocComment(), $parameters)) {
                $type = '';
            } elseif (count($parameters) == 2) {
                $type = trim($parameters[1]);
            } else {
                $type = '';
            }
            self::$propertyTypes[$key] = $type;

            // @nostorage => Heeft geen veld in de database
            if (preg_match('/\\s@nostorage\\s/', $property->getDocComment(), $parameters)) {
                self::$nostoreFields[] = $key;
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
        if (!isset(self::$propertyTypes)) {
            self::parsePropertyFieldDocs();
        }

        return self::$propertyTypes;
    }

    private static function getNoStorageFields()
    {
        if (!isset(self::$nostoreFields)) {
            self::parsePropertyFieldDocs();
        }

        return self::$nostoreFields;
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
        $types = self::getPropertyTypes();
        foreach (self::getPropertyFields() as $key => $property) {
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
        $types = self::getPropertyTypes();
        foreach (self::getPropertyFields() as $key => $property) {
            if (!in_array($key, $this->getNoStorageFields())) {
                $value = isset($array[$key]) ? $array[$key] : null;
                $this->fromArrayValue($property, $types[$key], $value);
            }
        }
    }
}
