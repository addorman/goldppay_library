<?php

namespace Common\Service;

use Common\Service\Traits\TransformHelper;
use Moogula\Collection\Collection as BaseCollection;
use Moogula\Contracts\Collection\Collection as CollectionContract;

class Transformer
{
    use TransformHelper;

    protected $casts = [];

    /**
     * @param array|string $casts
     */
    public function __construct($casts)
    {
        $this->casts = is_string($casts) ? ['*' => $casts] : (array) $casts;
    }

    public function handle($items, $key = '*')
    {
        if ($items instanceof CollectionContract) {
            $items = $items->map(function ($value, $key) {
                return $this->castValue($key, $value);
            });
            return $items;
        }
        if (is_array($items)) {
            foreach ($items as $key => $value) {
                $items[$key] = $this->castValue($key, $value);
            }

            return $items;
        }

        return $this->castValue($key, $items);
    }

    /**
     * Cast an value to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castValue($key, $value)
    {
        $castType = $this->getCastType($key);

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $this->getCasts()[$key], 2)[1]);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value, explode(':', $this->getCasts()[$key], 2)[1]);
            case 'timestamp':
                return $this->asTimestamp($value);
            case 'alpha': // a-z A-Z
                return $this->asAlpha($value);
            case 'alnum': // 0-9 a-z A-Z
                return $this->asAlnum($value);
            case 'print': // all
                return $this->asPrint($value);
            case 'digits': // 0-9
                return $this->asDigits($value);
            case 'input': // strip_tags trim
                return $this->asInput($value);
        }

        return $value;
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param  string  $key
     * @return string
     */
    protected function getCastType($key)
    {
        if (isset($this->getCasts()[$key])) {
            return $this->getCasts()[$key];
        }

        if (isset($this->getCasts()['*'])) {
            return $this->getCasts()['*'];
        }

        if ($this->isCustomDateTimeCast($this->getCasts()[$key])) {
            return 'custom_datetime';
        }

        if ($this->isDecimalCast($this->getCasts()[$key])) {
            return 'decimal';
        }
    }

    /**
     * Determine if the cast type is a custom date time cast.
     *
     * @param  string  $cast
     * @return bool
     */
    protected function isCustomDateTimeCast($cast)
    {
        return strncmp($cast, 'date:', 5) === 0 ||
            strncmp($cast, 'datetime:', 9) === 0;
    }

    /**
     * Determine if the cast type is a decimal cast.
     *
     * @param  string  $cast
     * @return bool
     */
    protected function isDecimalCast($cast)
    {
        return strncmp($cast, 'decimal:', 8) === 0;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }
}
