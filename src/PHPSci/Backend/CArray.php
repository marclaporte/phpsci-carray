<?php
namespace PHPSci\Backend;
use PHPSci\Backend\Exceptions\ParameterValueException;

/**
 * Class Abstract CArray
 *
 * @category PHPSci
 * @package  PHPSci\Backend
 * @author   Henrique Borba <henrique.borba.dev@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://phpsci.readthedocs.io
 */
abstract class CArray implements \ArrayAccess
{
    use CArrayPrinter;

    /**
     * Main connection with backend array of doubles.
     *
     * @var double * Backend C Array of Doubles
     */
    protected $c_array;


    /**
     * Get CArray property
     *
     * @return float
     */
    public function getCArray() {
        return $this->c_array;
    }

    /**
     * Generate C array of doubles
     * @param array $array
     * @return bool
     */
    public function generate_c_array(array $array) : bool {
        $this->c_array = new \CPHPSci($array);
        return true;
    }


    /**
     * Return current C array size in bytes, kilobytes, megabytes or gigabytes.
     *
     * @param string|null $mode
     * @return float
     * @throws ParameterValueException
     */
    public function sizeOf(string $mode = null) : float {
        if(!isset($mode)) {
            return $this->c_array->c_array_size;
        } else {
            switch($mode) {
                case 'kb':
                    return $this->c_array->c_array_size/1024;
                    break;
                case 'mb':
                    return $this->c_array->c_array_size/2048;
                    break;
                case 'gb':
                    return $this->c_array->c_array_size/3072;
                    break;
                default:
                    throw new ParameterValueException("Expected parameter mode to be one of (null, 'kb', 'mb', 'gb'), '$mode' given");
            }
        }
    }

    /**
     *  Transform CArray in PHP regular array
     */
    public function toArray() {
        return $this->c_array->php_array;
    }


    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetUnset($offset) {
        return true;
    }

    /**
     *
     *
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetGet($offset) {
        return $offset;
    }

    /**
     *
     */
    public function __toString()
    {
        switch($this->c_array->dim) {
            case 1:
                return $this->print1d();
                break;
            case 2:
                return $this->print2d();
                break;
            default:
                break;
        }
    }
}