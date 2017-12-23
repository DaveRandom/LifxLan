<?php declare(strict_types=1);

namespace DaveRandom\LifxLan\Decoding;

use DaveRandom\LifxLan\Decoding\Exceptions\InsufficientDataException;
use DaveRandom\LifxLan\Header\Header;
use DaveRandom\LifxLan\Packet;

final class PacketDecoder
{
    private const HEADER_OFFSET = 0;
    private const MESSAGE_OFFSET = Header::WIRE_SIZE;

    private $headerDecoder;
    private $messageDecoder;

    public function __construct(HeaderDecoder $headerDecoder = null, MessageDecoder $messageDecoder = null)
    {
        $this->headerDecoder = $headerDecoder ?? new HeaderDecoder;
        $this->messageDecoder = $messageDecoder ?? new MessageDecoder;
    }

    /**
     * @param DataBuffer $data
     * @return Packet
     * @throws Exceptions\InsufficientDataException
     * @throws Exceptions\InvalidReadException
     */
    public function decode(DataBuffer $data): Packet
    {
        $length = \unpack('v', $data->read(2, true))[1];

        if ($data->getLength() < $length) {
            throw new InsufficientDataException(
                "Packet length is stated to be {$length} bytes, only {$data->getLength()} bytes available in buffer"
            );
        }

        $packetBytes = $data->read($length);

        $header = $this->headerDecoder->decodeHeader(\substr($packetBytes, self::HEADER_OFFSET, Header::WIRE_SIZE));
        $payload = $this->messageDecoder->decodeMessage($header->getProtocolHeader()->getType(), \substr($packetBytes, self::MESSAGE_OFFSET));

        return new Packet($header, $payload);
    }
}
