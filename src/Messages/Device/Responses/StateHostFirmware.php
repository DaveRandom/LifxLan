<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Messages\Device\Responses;

use DaveRandom\LibLifxLan\DataTypes\HostFirmware;
use DaveRandom\LibLifxLan\Messages\ResponseMessage;

final class StateHostFirmware extends ResponseMessage
{
    public const MESSAGE_TYPE_ID = 15;
    public const WIRE_SIZE = 20;

    private $hostFirmware;

    public function __construct(HostFirmware $hostFirmware)
    {
        parent::__construct();

        $this->hostFirmware = $hostFirmware;
    }

    public function getHostFirmware(): HostFirmware
    {
        return $this->hostFirmware;
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
