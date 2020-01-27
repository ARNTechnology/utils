<?php


namespace ARNTech\Utils\Helper;


use ARNTech\Utils\Exception\DateTimeException;
use \InvalidArgumentException;
use \DateTimeInterface;

class DateTimeHelper
{
    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Can not unserialize.");
    }

    const STRING_TO_TIME_PATTERN_TWO_DIGITS = '([\d]{2})[\/|\:|\-]?([\d]{2})';
    const STRING_TO_TIME_PATTERN_TWO_FIRST_DIGITS = '([\d]{2})[\/|\:|\-]?([\d]{4})';
    const STRING_TO_TIME_PATTERN_TWO_LAST_DIGITS = '([\d]{4})[\/|\:|\-]?([\d]{2})';

    /**
     * Tries to create a DateTime object from a string for the following formats: MMYY, MMYYYY, YYYY/MM and separator between the two
     * accepted separators are "/", ":", "-"
     * @param string|int $string
     * @throws InvalidArgumentException
     * @throws DateTimeException
     */
    public static function dateTimeFromYearMonthString($string)
    {
        if (is_int($string)) {
            $string = strval($string);
            $len = strlen($string);
            if ($len == 3) {
                $string = '0' . $string;
            } elseif ($len == 5) {
                $string = '0' . $string;
            } elseif ($len != 4) {
                throw new InvalidArgumentException("Invalid expiration provided.");
            }
        }
        if (!is_string($string)) {
            throw new InvalidArgumentException("Invalid expiration provided.");
        }
        try {
            list($sts, $matches) = self::matchString(
                $string,
                self::STRING_TO_TIME_PATTERN_TWO_DIGITS
            );
            if ($sts) {
                return self::buildDateFromYearMonth($matches[2], $matches[1]);
            }
            list($sts, $matches) = self::matchString(
                $string,
                self::STRING_TO_TIME_PATTERN_TWO_FIRST_DIGITS
            );
            if ($sts) {
                return self::buildDateFromYearMonth($matches[1], $matches[2]);
            }

            list($sts, $matches) = self::matchString(
                $string,
                self::STRING_TO_TIME_PATTERN_TWO_LAST_DIGITS
            );
            if ($sts) {
                return self::buildDateFromYearMonth($matches[2], $matches[1]);
            }
        } catch (\Exception $e) {
            throw new DateTimeException(sprintf("Could not create date. %s", $e->getMessage()));
        }
        throw new DateTimeException("The date is not matching any format.");
    }

    /**
     * @param string $year
     * @param int $month
     * @return \DateTime
     * @throws DateTimeException
     * @throws InvalidArgumentException
     */
    private static function buildDateFromYearMonth($year, $month)
    {
        if (is_string($year)) {
            $year = intval($year);
        }
        if (!is_int($year)) {
            throw new InvalidArgumentException("Invalid year provided.");
        }
        if ($year < 100) {
            $year += 2000;//for 2 digit years like '01, '20
        }
        if ($year < 1970 || $year > 2099) {
            throw new DateTimeException("At the moment only year in range 1970-2099 is supported");
        }
        if (is_string($month)) {
            $month = intval($month);
        }
        if (!is_int($month)) {
            throw new InvalidArgumentException("Invalid month provided.");
        }
        if ($month < 1 || $month > 12) {
            throw new DateTimeException("Month can't be outside range 1-12.");
        }

        return new \DateTime(sprintf("%s-%s", $year, $month));
    }

    /**
     * @param DateTimeInterface $time
     * @return DateTimeInterface
     */
    public static function moveDateToEndOfDay(DateTimeInterface $time)
    {
        if (version_compare(phpversion(), '7.1', '>=')) {
            $time->setTime(23, 59, 59, 999999);
        } else {
            $time->setTime(23, 59, 59);
        }
        return $time;
    }

    /**
     * @param DateTimeInterface $time
     * @return DateTimeInterface
     */
    public static function moveDateToEndOfMonth(DateTimeInterface $time)
    {
        return self::moveDateToEndOfDay(
            self::moveDateToLastDayOfMonth(
                $time
            )
        );
    }

    /**
     * @param DateTimeInterface $time
     * @return DateTimeInterface
     */
    public static function moveDateToLastDayOfMonth(DateTimeInterface $time)
    {
        $diffDays = $time->format('t') - $time->format('j');
        if ($diffDays > 0) {
            $time->modify(sprintf('+%s days', $diffDays));
        }
        return $time;
    }

    private static function matchString($string, $pattern)
    {
        $sts = preg_match(sprintf('/^%s$/', self::STRING_TO_TIME_PATTERN_TWO_DIGITS), $string, $matches);
        return [$sts, $matches];
    }
}