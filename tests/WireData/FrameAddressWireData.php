<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Tests\WireData;

final class FrameAddressWireData
{
    public const DEFAULT_MAC_ADDRESS_OCTETS = [0, 0, 0, 0, 0, 0];

    public const DEFAULT_ACK_FLAG = false;
    public const DEFAULT_RES_FLAG = false;

    public const DEFAULT_SEQUENCE_NUMBER = 0;

    public const VALID_MAC_ADDRESS_DATA = [
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => [0, 0, 0, 0, 0, 0],
        "\x01\x02\x03\x04\x05\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => [1, 2, 3, 4, 5, 6],
        "\x06\x05\x04\x03\x02\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => [6, 5, 4, 3, 2, 1],
        "\xff\xff\xff\xff\xff\xff\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => [255, 255, 255, 255, 255, 255],
    ];

    public const VALID_FLAGS_DATA = [
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => ['ack' => false, 'res' => false],
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x01\x00" => ['ack' => false, 'res' => true],
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x02\x00" => ['ack' => true, 'res' => false],
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x03\x00" => ['ack' => true, 'res' => true],
    ];

    public const VALID_SEQUENCE_NUMBER_DATA = [
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00" => 0,
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x2a" => 42,
        "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff" => 255,
    ];

    public const INVALID_SHORT_DATA = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
}
