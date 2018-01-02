<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Header;

use DaveRandom\LibLifxLan\Exceptions\InvalidValueException;
use const DaveRandom\LibLifxLan\UINT32_MAX;
use const DaveRandom\LibLifxLan\UINT32_MIN;

final class Frame
{
    public const WIRE_SIZE = 8;

    private $size;
    private $origin;
    private $tagged;
    private $addressable;
    private $protocolNo;
    private $source;

    /**
     * @param int $size
     * @param int $origin
     * @param bool $tagged
     * @param bool $addressable
     * @param int $protocolNo
     * @param int $source
     * @throws InvalidValueException
     */
    public function __construct(int $size, int $origin, bool $tagged, bool $addressable, int $protocolNo, int $source)
    {
        if ($size < 0 || $size > 65535) {
            throw new InvalidValueException("Message size {$size} outside allowable range of 0 - 65535");
        }

        if ($origin < 0 || $origin > 3) {
            throw new InvalidValueException("Message origin value {$origin} outside allowable range 0 - 3");
        }

        if ($protocolNo < 0 || $protocolNo > 4095) {
            throw new InvalidValueException("Message protocol number {$protocolNo} outside allowable range 0 - 4095");
        }

        if ($source < UINT32_MIN || $source > UINT32_MAX) {
            throw new InvalidValueException("Message source value {$source} outside allowable range " . UINT32_MIN . " - " . UINT32_MAX);
        }

        $this->size = $size;
        $this->origin = $origin;
        $this->tagged = $tagged;
        $this->addressable = $addressable;
        $this->protocolNo = $protocolNo;
        $this->source = $source;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getOrigin(): int
    {
        return $this->origin;
    }

    public function isTagged(): bool
    {
        return $this->tagged;
    }

    public function isAddressable(): bool
    {
        return $this->addressable;
    }

    public function getProtocolNo(): int
    {
        return $this->protocolNo;
    }

    public function getSource(): int
    {
        return $this->source;
    }
}
