<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;

class DatesValidation extends Validation
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Checks if the passed date(time) is a past date
     *
     * @param  mixed $check The date to be checked. Can be either string, array
     *                      or a DateTimeInterface instance.
     * @return bool
     */
    public static function past($check)
    {
        if ($check instanceof \DateTimeInterface) {
            return $check < new \DateTime();
        }

        if (is_array($check)) {
            $check = static::_getDateString($check);
        }

        return strtotime($check) < time();
    }

    /**
     * Checks if the passed date(time) is a past date
     *
     * @param  mixed $check The date to be checked. Can be either string, array
     *                      or a DateTimeInterface instance.
     * @return bool
     */
    public static function future($check)
    {
        if ($check instanceof \DateTimeInterface) {
            return $check > new \DateTime();
        }

        if (is_array($check)) {
            $check = static::_getDateString($check);
        }

        return strtotime($check) > time();
    }

    /**
     * Checks if the passed date(time) is today.
     *
     * @param  mixed $check The date to be checked. Can be either string, array
     *                      or a DateTimeInterface instance.
     * @return bool
     */
    public static function today($check)
    {
        if ($check instanceof \DateTimeInterface) {
            return $check->format('Y-d-m') === date('Y-d-m');
        }

        if (is_array($check)) {
            $check = static::_getDateString($check);
        }

        return date('Y-m-d', strtotime($check)) === date('Y-m-d');
    }

    /**
     * Checks if the passed date(time) is not today.
     *
     * @param  mixed $check The date to be checked. Can be either string, array
     *                      or a DateTimeInterface instance.
     * @return bool
     */
    public static function notToday($check)
    {
        return !static::today($check);
    }
}