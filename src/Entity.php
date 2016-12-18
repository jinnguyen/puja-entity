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
    protected $defaults = array();

    private $data = array();
    private $checkDataType;
    private $attributeActionMapping = array();

    public function __construct(array $data, $checkDataType = true)
    {
        $this->checkDataType = $checkDataType;
        if (empty($data)) {
            return;
        }

        foreach ($this->attributes as $attr => $dataType) {
            $this->validateAttributeKeyName($attr);
            $value = null;
            if (array_key_exists($attr, $data)) {
                $value = $data[$attr];
            } elseif (array_key_exists($attr, $this->defaults)) {
                $value = $this->defaults[$attr];
            }

            $this->addAttributeActionMapping($attr);

            $this->data[$attr] = $this->convertDataType($attr, $value);
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

    public function unsetAttr($key)
    {
        $this->data[$key] = empty($this->defaults[$key]) ? null : $this->defaults[$key];
    }

    public function __call($name, $arguments)
    {
        list ($action, $attr) = $this->attributeActionMapping[$name];

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
                $this->unsetAttr($attr);
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

    public function getDocblock()
    {
        $code = '/**' . PHP_EOL;
        $code .= '* This comment (docblock) is copied from ' . get_class($this) . '->getDocblock(); you should do it each time you change the ' . get_class($this) . '->attributes' . PHP_EOL;
        foreach ($this->attributes as $attr => $dataType)
        {
            $func = $this->getFuncName($attr);
            $code .= '* @method ' . $dataType . ' get' . $func . '()' . PHP_EOL;
            $code .= '* @method set' . $func . '(' . $dataType . ' $' . lcfirst($func) . ')' . PHP_EOL;
            $code .= '* @method has' . $func . '()' . PHP_EOL;
            $code .= '* @method unset' . $func . '() // set value to ' . get_class($this) .'::defaults[' . $attr . '] or null' . PHP_EOL;
        }
        $code .= '*/';
        return $code;
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
            case self::DATATYPE_MIXED:
                break;
            default:
                if (empty($dataType)) {
                    break;
                }

                if (!class_exists($dataType)) {
                    throw new Exception('Datatype: ' . $dataType . ' is not existed.');
                }

                if (!($value instanceof $dataType)) {
                    throw new Exception(get_class($this) . '->attributes['. $key . '] must be a instance of ' . $dataType . ', given ' . gettype($value));
                }
                break;
        }
        return $value;
    }

    protected function addAttributeActionMapping($attr)
    {
        $func = $this->getFuncName($attr);
        $this->attributeActionMapping['get' . $func] = array('get', $attr);
        $this->attributeActionMapping['set' . $func] = array('set', $attr);
        $this->attributeActionMapping['unset' . $func] = array('unset', $attr);
        $this->attributeActionMapping['has' . $func] = array('has', $attr);
    }

    protected function getFuncName($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        return str_replace(' ', '', $str);
    }

    protected function validateAttributeKeyName($key)
    {
        if (substr($key, 0, 1) == '_') {
            throw new Exception('Puja\\Entity::attributes key cannot start with "_" (' . $key . ')');
        }

        if (substr($key, -1) == '_') {
            throw new Exception('Puja\\Entity::attributes key cannot end with "_" (' . $key . ')');
        }

        if (strpos($key, '__')) {
            throw new Exception('Puja\\Entity::attributes key cannot include "__" (' . $key . ')');
        }
    }


}