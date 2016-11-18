<?php
namespace Puja\Entity;
class Entity
{
    const DATATYPE_BOOLEAN = 'boolean';
    const DATATYPE_DOUBLE = 'double';
    const DATATYPE_INT = 'int';
    const DATATYPE_FLOAT = 'float';
    const DATATYPE_STRING = 'string';
    const DATATYPE_ARRAY = 'array';
    const DATATYPE_OBJECT = 'object';
    const DATATYPE_RESOURCE = 'resource';
    const DATATYPE_NULL = 'NULL';
    const DATATYPE_MIXED = 'mixed';

    protected $attributes = array();

    private $data = array();
    private $checkDataType;
    private $allowActions = array('get', 'set', 'unset', 'has');

    public function __construct(array $data, $checkDataType = true)
    {
        $this->data = array_fill_keys(array_keys($this->attributes), null);
        $this->checkDataType = $checkDataType;
        if (empty($data)) {
            return;
        }

        foreach ($this->attributes as $attr => $dataType) {
            $value = null;
            if (array_key_exists($attr, $data)) {
                $value = $data[$attr];
            }
            $this->setAttr($attr, $value);
        }
    }

    public function hasAttr($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function setAttr($key, $value)
    {
        if ($this->hasAttr($key)) {
            $this->data[$key] = $this->convertDataType($key, $value);
        }
    }

    public function __call($name, $arguments)
    {
        $underScoreName = $this->convertCamelCaseToUnderScore($name);
        $explode = explode('_', $underScoreName);

        $action = $explode[0];
        if (!in_array($action, $this->allowActions)) {
            throw new Exception('Invalid method action [' . $action . ']. Only get, set, unset and has are allowed for class ' . get_class($this) . '!');
        }

        unset($explode[0]);
        $attr = strtolower(implode('_', $explode));
        switch ($action) {
            case 'get':
                return $this->getAttr($attr);
            case 'set':
                if (empty($arguments)) {
                    throw new Exception('Missing argument 1 for ' . get_class($this) . '->' .$name . '()');
                }
                $this->setAttr($attr, $arguments[0]);
                break;
            case 'unset':
                unset($this->data[$attr]);
                break;
            case 'has':
                return $this->hasAttr($attr);
            default:
                throw new Exception('Invalid method action [' . $action . ']. Only get, set, unset and has are allowed for class ' . get_class($this) . '!');

        }
    }

    public function getAttr($key, $defaultValue = null)
    {
        if ($this->hasAttr($key)) {
            return $this->data[$key];
        }

        return $defaultValue;
    }

    public function __toArray()
    {
        return $this->data;
    }

    protected function convertDataType($key, $value)
    {
        if (empty($this->checkDataType)) {
            return $value;
        }

        $dataType = $this->attributes[$key];
        switch ($dataType) {
            case self::DATATYPE_STRING:
                $value = (string) $value;
                break;
            case self::DATATYPE_ARRAY:
                $value = (array) $value;
                break;
            case self::DATATYPE_BOOLEAN:
                $value = (bool) $value;
                break;
            case self::DATATYPE_DOUBLE:
                $value = (double) $value;
                break;
            case self::DATATYPE_FLOAT:
                $value = (float) $value;
                break;
            case self::DATATYPE_INT:
                $value = (int) $value;
                break;
            case self::DATATYPE_NULL:
                $value = NULL;
                break;
            case self::DATATYPE_OBJECT:
                $value = (object) $value;
                break;
            case self::DATATYPE_RESOURCE:
                if (!is_resource($value)) {
                    throw new Exception($key . ' is not a resource');
                }
                break;
            default:
                // if empty, dont do anything
                if ($dataType) { // mean set $dataType is a class name
                    if (!class_exists($dataType)) {
                        throw new Exception('Datatype: ' . $dataType . ' is not existed.');
                    }

                    if (!($value instanceof $dataType)) {
                        throw new Exception(get_class($this) . '->attributes['. $key . '] must be a instance of ' . $dataType . ', given ' . gettype($value));
                    }
                }
        }
        return $value;
    }

    protected function convertCamelCaseToUnderScore($string)
    {
        $pattern = array(
            '#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#',
            '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'
        );
        $replacement = array(
            '_' . '\1',
            '_' . '\1'
        );

        return preg_replace($pattern, $replacement, $string);
    }

    public function getDocblock()
    {
        $code = '/**' . PHP_EOL;
        $code .= '* This comment (docblock) is copied from ' . get_class($this) . '->getDocblock(); you should do it each time you change the ' . get_class($this) . '->attributes' . PHP_EOL;
        foreach ($this->attributes as $attr => $dataType)
        {
            $attr = str_replace('_', ' ', $attr);
            $attr = ucwords($attr);
            $attr = str_replace(' ', '', $attr);
            $code .= '* @method ' . $dataType . ' get' . $attr . '()' . PHP_EOL;
            $code .= '* @method set' . $attr . '(' . $dataType . ' $attr)' . PHP_EOL;
            $code .= '* @method has' . $attr . '()' . PHP_EOL;
            $code .= '* @method unset' . $attr . '()' . PHP_EOL;
        }
        $code .= '*/';
        return $code;
    }
}