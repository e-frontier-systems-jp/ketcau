<?php

namespace Ketcau\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    /**
     * UTCのタイムゾーン
     * @var \DateTimeZone
     */
    protected static $utc;


    /**
     * アプリケーションのタイムゾーン
     * @var \DateTimeZone
     */
    protected static $timezone;


    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtcTimezone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUtcTimezone()
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        $converted->setTimezone(self::getTimezone());

        return $converted;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }


    public static function getUtcTimezone()
    {
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    public static function getTimezone()
    {
        if (is_null(self::$timezone)) {
            throw new \LogicException(sprintf('%s::$timezone is undefined.', self::class));
        }

        return self::$timezone;
    }

    public static function setTimezone($timezone = 'Asia/Tokyo')
    {
        self::$timezone = new \DateTimeZone($timezone);
    }
}