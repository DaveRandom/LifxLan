<?php declare(strict_types=1);

namespace DaveRandom\LibLifxLan\Encoding;

use DaveRandom\LibLifxLan\DataTypes as DeviceDataTypes;
use DaveRandom\LibLifxLan\DataTypes\Light as LightDataTypes;
use DaveRandom\LibLifxLan\Encoding\Exceptions\InvalidMessageException;
use DaveRandom\LibLifxLan\Messages\Device\Commands as DeviceCommands;
use DaveRandom\LibLifxLan\Messages\Device\Requests as DeviceRequests;
use DaveRandom\LibLifxLan\Messages\Device\Responses as DeviceResponses;
use DaveRandom\LibLifxLan\Messages\Light\Commands as LightCommands;
use DaveRandom\LibLifxLan\Messages\Light\Responses as LightResponses;
use DaveRandom\LibLifxLan\Messages\Message;
use const DaveRandom\LibLifxLan\FLOAT32_CODE;
use DaveRandom\LibLifxLan\Messages\UnknownMessage;

final class MessageEncoder
{
    /**
     * @uses encodeUnknownMessage
     * @uses encodeEchoRequest
     * @uses encodeEchoResponse
     * @uses encodeSetGroup
     * @uses encodeStateGroup
     * @uses encodeStateHostFirmware
     * @uses encodeStateHostInfo
     * @uses encodeStateInfo
     * @uses encodeSetLabel
     * @uses encodeStateLabel
     * @uses encodeSetLocation
     * @uses encodeStateLocation
     * @uses encodeSetDevicePower
     * @uses encodeStateDevicePower
     * @uses encodeStateService
     * @uses encodeStateVersion
     * @uses encodeStateWifiFirmware
     * @uses encodeStateWifiInfo
     * @uses encodeSetColor
     * @uses encodeSetWaveform
     * @uses encodeSetWaveformOptional
     * @uses encodeState
     * @uses encodeSetInfrared
     * @uses encodeStateInfrared
     * @uses encodeSetLightPower
     * @uses encodeStateLightPower
     */
    private const ENCODING_ROUTINES = [
        UnknownMessage::class => 'UnknownMessage',

        // Device command messages
        DeviceCommands\SetGroup::class => 'SetGroup',
        DeviceCommands\SetLabel::class => 'SetLabel',
        DeviceCommands\SetLocation::class => 'SetLocation',
        DeviceCommands\SetPower::class => 'SetDevicePower',

        // Device request messages
        DeviceRequests\EchoRequest::class => 'EchoRequest',

        // Device response messages
        DeviceResponses\EchoResponse::class => 'EchoResponse',
        DeviceResponses\StateGroup::class => 'StateGroup',
        DeviceResponses\StateHostFirmware::class => 'StateHostFirmware',
        DeviceResponses\StateHostInfo::class => 'StateHostInfo',
        DeviceResponses\StateInfo::class => 'StateInfo',
        DeviceResponses\StateLabel::class => 'StateLabel',
        DeviceResponses\StateLocation::class => 'StateLocation',
        DeviceResponses\StatePower::class => 'StateDevicePower',
        DeviceResponses\StateService::class => 'StateService',
        DeviceResponses\StateVersion::class => 'StateVersion',
        DeviceResponses\StateWifiFirmware::class => 'StateWifiFirmware',
        DeviceResponses\StateWifiInfo::class => 'StateWifiInfo',

        // Light command messages
        LightCommands\SetColor::class => 'SetColor',
        LightCommands\SetInfrared::class => 'SetInfrared',
        LightCommands\SetPower::class => 'SetLightPower',
        LightCommands\SetWaveform::class => 'SetWaveform',
        LightCommands\SetWaveformOptional::class => 'SetWaveformOptional',

        // Light response messages
        LightResponses\State::class => 'State',
        LightResponses\StateInfrared::class => 'StateInfrared',
        LightResponses\StatePower::class => 'StateLightPower',
    ];

    private function signedShortToUnsignedShort(int $signed): int
    {
        if ($signed >= 0) {
            return $signed & 0x7fff;
        }

        return 0x8000 | (($signed & 0x7fff) + 1);
    }

    private function encodeHsbkColor(LightDataTypes\HsbkColor $color): string
    {
        return \pack('v4', $color->getHue(), $color->getSaturation(), $color->getBrightness(), $color->getTemperature());
    }

    /**
     * @param DeviceDataTypes\Location $location
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeLocation(DeviceDataTypes\Location $location): string
    {
        return \pack(
            'a16a32P',
            $location->getGuid()->getBytes(),
            $location->getLabel()->getValue(),
            $this->dateTimeToNanoseconds($location->getUpdatedAt())
        );
    }

    /**
     * @param DeviceDataTypes\Group $group
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeGroup(DeviceDataTypes\Group $group): string
    {
        return \pack(
            'a16a32P',
            $group->getGuid()->getBytes(),
            $group->getLabel()->getValue(),
            $this->dateTimeToNanoseconds($group->getUpdatedAt())
        );
    }

    /**
     * @param DeviceDataTypes\Firmware $firmware
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeFirmware(DeviceDataTypes\Firmware $firmware)
    {
        return \pack(
            'PPV',
            $this->dateTimeToNanoseconds($firmware->getBuild()),
            0, // reserved
            $firmware->getVersion()
        );
    }

    private function encodeNetworkInfo(DeviceDataTypes\NetworkInfo $info): string
    {
        return \pack(
            FLOAT32_CODE . 'VVv',
            $info->getSignal(),
            $info->getTx(),
            $info->getRx(),
            0 // reserved
        );
    }

    /**
     * @param \DateTimeInterface $dateTime
     * @return int
     * @throws InvalidMessageException
     */
    private function dateTimeToNanoseconds(\DateTimeInterface $dateTime): int
    {
        $result = ($dateTime->format('U') * 1000000000) + ($dateTime->format('u') * 1000);

        if ($result < 0) {
            throw new InvalidMessageException("Timestamp {$dateTime->format('Y-m-d H:i:s.u')} is negative");
        }

        return $result;
    }

    private function encodeUnknownMessage(UnknownMessage $message): string
    {
        return $message->getData();
    }

    private function encodeStateVersion(DeviceResponses\StateVersion $message): string
    {
        $version = $message->getVersion();

        return \pack('VVV', $version->getVendor(), $version->getProduct(), $version->getVersion());
    }

    private function encodeStateService(DeviceResponses\StateService $message): string
    {
        $service = $message->getService();

        return \pack('CV', $service->getTypeId(), $service->getPort());
    }

    /**
     * @param DeviceResponses\StateInfo $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeStateInfo(DeviceResponses\StateInfo $message): string
    {
        $info = $message->getInfo();

        return \pack('PPP', $this->dateTimeToNanoseconds($info->getTime()), $info->getUptime(), $info->getDowntime());
    }

    /**
     * @param DeviceResponses\StateHostFirmware $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeStateHostFirmware(DeviceResponses\StateHostFirmware $message): string
    {
        return $this->encodeFirmware($message->getHostFirmware());
    }

    private function encodeStateHostInfo(DeviceResponses\StateHostInfo $message): string
    {
        return $this->encodeNetworkInfo($message->getHostInfo());
    }

    /**
     * @param DeviceResponses\StateWifiFirmware $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeStateWifiFirmware(DeviceResponses\StateWifiFirmware $message): string
    {
        return $this->encodeFirmware($message->getWifiFirmware());
    }

    private function encodeStateWifiInfo(DeviceResponses\StateWifiInfo $message): string
    {
        return $this->encodeNetworkInfo($message->getWifiInfo());
    }

    /**
     * @param DeviceCommands\SetGroup $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeSetGroup(DeviceCommands\SetGroup $message): string
    {
        return $this->encodeGroup($message->getGroup());
    }

    /**
     * @param DeviceResponses\StateGroup $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeStateGroup(DeviceResponses\StateGroup $message): string
    {
        return $this->encodeGroup($message->getGroup());
    }

    /**
     * @param DeviceCommands\SetLabel $message
     * @return string
     */
    private function encodeSetLabel(DeviceCommands\SetLabel $message): string
    {
        return \pack('a32', $message->getLabel()->getValue());
    }

    /**
     * @param DeviceResponses\StateLabel $message
     * @return string
     */
    private function encodeStateLabel(DeviceResponses\StateLabel $message): string
    {
        return \pack('a32', $message->getLabel()->getValue());
    }

    /**
     * @param DeviceCommands\SetLocation $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeSetLocation(DeviceCommands\SetLocation $message): string
    {
        return $this->encodeLocation($message->getLocation());
    }

    /**
     * @param DeviceResponses\StateLocation $message
     * @return string
     * @throws InvalidMessageException
     */
    private function encodeStateLocation(DeviceResponses\StateLocation $message): string
    {
        return $this->encodeLocation($message->getLocation());
    }

    /**
     * @param DeviceCommands\SetPower $message
     * @return string
     */
    private function encodeSetDevicePower(DeviceCommands\SetPower $message): string
    {
        return \pack('v', $message->getLevel());
    }

    /**
     * @param DeviceResponses\StatePower $message
     * @return string
     */
    private function encodeStateDevicePower(DeviceResponses\StatePower $message): string
    {
        return \pack('v', $message->getLevel());
    }

    /**
     * @param DeviceRequests\EchoRequest $message
     * @return string
     */
    private function encodeEchoRequest(DeviceRequests\EchoRequest $message): string
    {
        return $message->getPayload();
    }

    /**
     * @param DeviceResponses\EchoResponse $message
     * @return string
     */
    private function encodeEchoResponse(DeviceResponses\EchoResponse $message): string
    {
        return $message->getPayload();
    }

    /**
     * @param LightCommands\SetColor $message
     * @return string
     */
    private function encodeSetColor(LightCommands\SetColor $message): string
    {
        $transition = $message->getColorTransition();

        return "\x00" . $this->encodeHsbkColor($transition->getColor()) . \pack('V', $transition->getDuration());
    }

    /**
     * @param LightCommands\SetInfrared $message
     * @return string
     */
    private function encodeSetInfrared(LightCommands\SetInfrared $message): string
    {
        return \pack('v', $message->getBrightness());
    }

    /**
     * @param LightResponses\StateInfrared $message
     * @return string
     */
    private function encodeStateInfrared(LightResponses\StateInfrared $message): string
    {
        return \pack('v', $message->getBrightness());
    }

    /**
     * @param LightCommands\SetPower $message
     * @return string
     */
    private function encodeSetLightPower(LightCommands\SetPower $message): string
    {
        $transition = $message->getPowerTransition();

        return \pack('vV', $transition->getLevel(), $transition->getDuration());
    }

    /**
     * @param LightResponses\StatePower $message
     * @return string
     */
    private function encodeStateLightPower(LightResponses\StatePower $message): string
    {
        return \pack('v', $message->getLevel());
    }

    /**
     * @param LightResponses\State $message
     * @return string
     */
    private function encodeState(LightResponses\State $message): string
    {
        $state = $message->getState();

        return $this->encodeHsbkColor($state->getColor()) . \pack(
            'v2a32P',
            0, // reserved
            $state->getPower(),
            $state->getLabel()->getValue(),
            0  // reserved
        );
    }

    /**
     * @param LightCommands\SetWaveform $message
     * @return string
     */
    private function encodeSetWaveform(LightCommands\SetWaveform $message): string
    {
        $effect = $message->getEffect();
        $skew = $this->signedShortToUnsignedShort($effect->getSkewRatio());

        return "\x00" . \chr((int)$effect->isTransient())
            . $this->encodeHsbkColor($effect->getColor())
            . \pack('V' . FLOAT32_CODE . 'vC', $effect->getPeriod(), $effect->getCycles(), $skew, $effect->getWaveform())
        ;
    }

    /**
     * @param LightCommands\SetWaveformOptional $message
     * @return string
     */
    private function encodeSetWaveformOptional(LightCommands\SetWaveformOptional $message): string
    {
        $effect = $message->getEffect();
        $skew = $this->signedShortToUnsignedShort($effect->getSkewRatio());

        $options = $effect->getOptions();
        $optionData = \pack(
            'C4',
            (int)(bool)($options & LightDataTypes\Effect::SET_HUE),
            (int)(bool)($options & LightDataTypes\Effect::SET_SATURATION),
            (int)(bool)($options & LightDataTypes\Effect::SET_BRIGHTNESS),
            (int)(bool)($options & LightDataTypes\Effect::SET_TEMPERATURE)
        );

        return "\x00" . \chr((int)$effect->isTransient())
            . $this->encodeHsbkColor($effect->getColor())
            . \pack('V' . FLOAT32_CODE . 'vC', $effect->getPeriod(), $effect->getCycles(), $skew, $effect->getWaveform())
            . $optionData
        ;
    }

    public function encodeMessage(Message $message): string
    {
        return \array_key_exists($class = \get_class($message), self::ENCODING_ROUTINES)
            ? $this->{'encode' . self::ENCODING_ROUTINES[$class]}($message)
            : '';
    }
}
