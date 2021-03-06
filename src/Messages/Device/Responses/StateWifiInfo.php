<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Messages\Device\Responses;

use DaveRandom\LibLifxLan\DataTypes\WifiInfo;
use DaveRandom\LibLifxLan\Messages\Message;

final class StateWifiInfo implements Message
{
    public const MESSAGE_TYPE_ID = 17;
    public const WIRE_SIZE = 14;

    private $wifiInfo;

    public function __construct(WifiInfo $wifiInfo)
    {
        $this->wifiInfo = $wifiInfo;
    }

    public function getWifiInfo(): WifiInfo
    {
        return $this->wifiInfo;
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
