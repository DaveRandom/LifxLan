<?php declare(strict_types=1);

namespace DaveRandom\LifxLan\Messages\Device\Responses;

use DaveRandom\LifxLan\Messages\Message;

final class Acknowledgement extends Message
{
    public const MESSAGE_TYPE_ID = 45;

    public function getTypeId(): int
    {
        return self::MESSAGE_TYPE_ID;
    }
}
