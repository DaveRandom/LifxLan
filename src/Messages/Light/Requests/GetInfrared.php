<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Messages\Light\Requests;

use DaveRandom\LibLifxLan\Messages\Message;

final class GetInfrared implements Message
{
    public const MESSAGE_TYPE_ID = 120;
    public const WIRE_SIZE = 0;

    public function getTypeId(): int
    {
        return self::MESSAGE_TYPE_ID;
    }

    public function getWireSize(): int
    {
        return self::WIRE_SIZE;
    }
}
