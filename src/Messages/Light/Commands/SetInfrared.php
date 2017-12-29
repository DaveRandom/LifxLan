<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Messages\Light\Commands;

use DaveRandom\LibLifxLan\Messages\Message;

final class SetInfrared implements Message
{
    public const MESSAGE_TYPE_ID = 121;
    public const WIRE_SIZE = 2;

    private $brightness;

    public function __construct(int $brightness)
    {
        $this->brightness = $brightness;
    }

    public function getBrightness(): int
    {
        return $this->brightness;
    }

    public function getTypeId(): int
    {
        return self::MESSAGE_TYPE_ID;
    }

    public function getWireSize(): int
    {
        return self::WIRE_SIZE;
    }
}
