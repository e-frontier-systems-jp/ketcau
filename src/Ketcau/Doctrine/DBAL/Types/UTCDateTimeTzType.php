<?php

namespace Ketcau\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeTzType;

class UTCDateTimeTzType extends DateTimeTzType
{
    /**
     * UTCのタイムゾーン
     *
     * @var ?\DateTimeZone
     */
    protected static ?\DateTimeZone $utc = null;

    /**
     * アプリケーションのタイムゾーン
     *
     * @var ?\DateTimeZone
     */
    protected static ?\DateTimeZone $timezone = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtcTimezone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }


    public function convertToPHPValue($value, AbstractPlatform $platform): \DateTime|\DateTimeInterface|null
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeTzFormatString(),
            $value,
            self::getUtcTimezone()
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeTzFormatString());
        }

        $converted->setTimezone(self::getTimezone());

        return $converted;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }


    protected static function getUtcTimezone(): \DateTimeZone
    {
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    /**
     * @return \DateTimeZone
     */
    public static function getTimezone(): \DateTimeZone
    {
        if (is_null(self::$timezone)) {
            throw new \LogicException(sprintf('%s::$timezone is undefined.', self::class));
        }

        return self::$timezone;
    }

    /**
     * @param string $timezone
     */
    public static function setTimezone(string $timezone = 'Asia/Tokyo'): void
    {
        self::$timezone = new \DateTimeZone($timezone);
    }
}