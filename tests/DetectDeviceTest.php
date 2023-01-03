<?php namespace Gayou\EmojiConverter\Test;

use PHPUnit\Framework\TestCase;
use Gayou\EmojiConverter\EmojiConverter;

class DetectDeviceTest extends TestCase
{
    private const UA_DOCOMO1 = "DoCoMo/2.0 P906i(c100;TB;W24H15)";
    private const UA_DOCOMO2 = "DoCoMo/2.0 F02A(c100;TB;W24H17)";
    private const UA_KDDI = "KDDI-HI21 UP.Browser/6.0.2.254 (GUI) MMP/1.1";
    private const UA_VODAFONE = "Vodafone/1.0/V904SH/SHJ001/SN123456789012345 Browser/VF-NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1";
    private const UA_SOFTBANK = "SoftBank/1.0/910T/TJ001/SN123456789012345 Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1";
    private const UA_CHROME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';

    private static $converter;

    public static function setUpBeforeClass(): void
    {
        self::$converter = new EmojiConverter();
    }


    /**
     * ドコモ端末を検知
     * 
     */
    public function testDetectDeviceForDocomo()
    {
        //ユーザーエージェントを渡す
        $deivce = self::$converter->detectDevice(self::UA_DOCOMO1);
        $this->assertSame($deivce, EmojiConverter::DOCOMO);

        $deivce = self::$converter->detectDevice(self::UA_DOCOMO2);
        $this->assertSame($deivce, EmojiConverter::DOCOMO);

        //ユーザエージェントを渡さない
        $_SERVER['HTTP_USER_AGENT'] = self::UA_DOCOMO1;
        $deivce = self::$converter->detectDevice();
        $this->assertSame($deivce, EmojiConverter::DOCOMO);
    }

    /**
     * au端末を検知
     * 
     */
    public function testDetectDeviceForKddi()
    {
        //ユーザーエージェントを渡す
        $deivce = self::$converter->detectDevice(self::UA_KDDI);
        $this->assertSame($deivce, EmojiConverter::KDDI);

        //ユーザエージェントを渡さない
        $_SERVER['HTTP_USER_AGENT'] = self::UA_KDDI;
        $deivce = self::$converter->detectDevice();
        $this->assertSame($deivce, EmojiConverter::KDDI);
    }

    /**
     * Softbank端末を検知
     * 
     */
    public function testDetectDeviceForSoftbank()
    {
        //ユーザーエージェントを渡す
        $deivce = self::$converter->detectDevice(self::UA_VODAFONE);
        $this->assertSame($deivce, EmojiConverter::SOFTBANK);

        $deivce = self::$converter->detectDevice(self::UA_SOFTBANK);
        $this->assertSame($deivce, EmojiConverter::SOFTBANK);

        //ユーザエージェントを渡さない
        $_SERVER['HTTP_USER_AGENT'] = self::UA_SOFTBANK;
        $deivce = self::$converter->detectDevice();
        $this->assertSame($deivce, EmojiConverter::SOFTBANK);
    }


    /**
     * 携帯端末以外
     * 
     */
    public function testDetectDeviceForPC()
    {
        //ユーザーエージェントを渡す
        $deivce = self::$converter->detectDevice(self::UA_CHROME);
        $this->assertSame($deivce, EmojiConverter::NONE);

        //ユーザエージェントを渡さない
        $_SERVER['HTTP_USER_AGENT'] = self::UA_CHROME;
        $deivce = self::$converter->detectDevice();
        $this->assertSame($deivce, EmojiConverter::NONE);
    }

}