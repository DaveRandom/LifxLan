<?php declare(strict_types=1);

namespace DaveRandom\LifxLan\Messages\Device\Responses;

use DaveRandom\LifxLan\Messages\Message;

final class StateGroup extends Message
{
    public const MESSAGE_TYPE_ID = 53;

    public function getTypeId(): int
    {
        return self::MESSAGE_TYPE_ID;
    }

    // todo
}
