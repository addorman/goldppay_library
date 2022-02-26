<?php

namespace Common\Service\Traits;

use Carbon\CarbonInterface;
use InvalidArgumentException;
use Moogula\Support\Carbon;

trait TransformHelper
{
    /**
     * Decode the given float.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function fromFloat($value)
    {
        switch ((string) $value) {
            case 'Infinity':
                return INF;
            case '-Infinity':
                return -INF;
            case 'NaN':
                return NAN;
            default:
                return (float) $value;
        }
    }

    /**
     * Return a decimal as string.
     *
     * @param  float  $value
     * @param  int  $decimals
     * @return string
     */
    protected function asDecimal($value, $decimals)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, !$asObject);
    }

    /**
     * Return a timestamp as DateTime object with time set to 00:00:00.
     *
     * @param  mixed  $value
     * @return Carbon
     */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return Carbon
     */
    protected function asDateTime($value, $format = 'Y-m-d H:i:s')
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value);
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if ($this->isStandardDateFormat($value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        try {
            $date = Carbon::createFromFormat($format, $value);
        } catch (InvalidArgumentException $e) {
            $date = false;
        }

        return $date ?: Carbon::parse($value);
    }


    /**
     * Returns the alphabetic characters of the parameter value.
     *
     * @return string
     */
    public function asAlpha($value)
    {
        return preg_replace('/[^[:alpha:]]/', '', $value);
    }

    /**
     * Returns the alphabetic characters and digits of the parameter value.
     *
     * @return string
     */
    public function asAlnum($value)
    {
        return preg_replace('/[^[:alnum:]]/', '', $value);
    }

    /**
     * Returns the print characters and digits of the parameter value.
     *
     * @return string
     */
    public function asPrint($value)
    {
        return preg_replace('/[^[:print:]]/', '', $value);
    }

    /**
     * Returns the digits of the parameter value.
     *
     * @return string
     */
    public function asDigits($value)
    {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(['-', '+'], '', filter_var($value, \FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Returns the input characters and digits of the parameter value.
     *
     * @return string
     */
    public function asInput($value)
    {
        return \trim(\strip_tags($value));
    }

    /**
     * Return a timestamp as unix timestamp.
     *
     * @param  mixed  $value
     * @return int
     */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

    /**
     * Determine if the given value is a standard date format.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }
}
