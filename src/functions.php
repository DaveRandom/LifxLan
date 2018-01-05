<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan;

const UINT32_MIN = 0;
const UINT32_MAX = 0xffffffff;

// @codeCoverageIgnoreStart
foreach (['g', 'f', 'e'] as $format) {
    if (\pack($format, -0.0) === "\x00\x00\x00\x80") {
        \define(__NAMESPACE__ . '\\FLOAT32_CODE', $format);
        break;
    }
}

if (!\defined(__NAMESPACE__ . '\\FLOAT32_CODE')) {
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new \Error('Cannot pack()/unpack() floating point numbers to a 32-bit little-endian representation');
}
// @codeCoverageIgnoreEnd

/**
 * @param \DateTimeInterface $dateTime
 * @return \DateTimeImmutable
 */
function datetimeinterface_to_datetimeimmutable(\DateTimeInterface $dateTime): \DateTimeImmutable
{
    if ($dateTime instanceof \DateTimeImmutable) {
        return $dateTime;
    }

    \assert($dateTime instanceof \DateTime, new \Error('DateTimeInterface is not DateTimeImmutable or DateTime???'));

    return \DateTimeImmutable::createFromMutable($dateTime);
}

/**
 * @param int $timestamp
 * @return \DateTimeImmutable
 */
function nanotime_to_datetimeimmutable(int $timestamp): \DateTimeImmutable
{
    static $utcTimeZone;

    $usecs = \abs((int)(($timestamp % 1000000000) / 1000));
    $secs = (int)($timestamp / 1000000000);

    $result = \DateTimeImmutable::createFromFormat(
        'u U',
        \sprintf("%06d %d", $usecs, $secs),
        $utcTimeZone ?? ($utcTimeZone = new \DateTimeZone('UTC'))
    );

    \assert($result !== false, new \Error("Could not convert nanotime to DateTimeImmutable instance"));

    return $result;
}

function datetimeinterface_to_nanotime(\DateTimeInterface $dateTime): int
{
    return (int)$dateTime->format('Uu000');
}

function int16_to_uint16(int $signed): int
{
    return $signed < 0
        ? ($signed & 0x7fff) + ($signed & 0x8000)
        : $signed & 0x7fff;
}

function uint16_to_int16(int $unsigned): int
{
    return $unsigned >= 0x8000
        ? ($unsigned & 0x7fff) - ($unsigned & 0x8000)
        : $unsigned & 0x7fff;
}
