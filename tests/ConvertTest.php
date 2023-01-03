<?php namespace Gayou\EmojiConverter\Test;

use PHPUnit\Framework\TestCase;
use Gayou\EmojiConverter\EmojiConverter;

class ConvertTest extends TestCase
{
    private const UA_DOCOMO = "DoCoMo/2.0 P906i(c100;TB;W24H15)";
    private const UA_KDDI = "KDDI-HI21 UP.Browser/6.0.2.254 (GUI) MMP/1.1";
    private const UA_SOFTBANK = "SoftBank/1.0/910T/TJ001/SN123456789012345 Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1";
    private const UA_CHROME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';

    private static $converter;

    public static function setUpBeforeClass(): void
    {
        self::$converter = new EmojiConverter();
    }


    /**
     * ドコモの場合、i絵文字バイナリはそのまま
     * 
     */
    public function testDocomoBinaryForDocomo()
    {
        $from = $this->getTestString("testDocomoEmojiBinary.txt");
        $to = self::$converter->convert($from, EmojiConverter::MODE_BINARY, self::UA_DOCOMO);

        $this->assertSame($to, $from);
    }


    /**
     * i絵文字バイナリをEZ絵文字に変換
     * 
     */
    public function testDocomoBinaryToKddiBinary()
    {
        $from = $this->getTestString("testDocomoEmojiBinary.txt");
        $to = self::$converter->convert($from, EmojiConverter::MODE_BINARY, self::UA_KDDI);

        $this->assertSame($to, '&#xE5AC;');
    }


    /**
     * i絵文字バイナリをSoftbank絵文字に変換
     * 
     */
    public function testDocomoBinaryToSoftbankBinary()
    {
        $from = $this->getTestString("testDocomoEmojiBinary.txt");
        $to = self::$converter->convert($from, EmojiConverter::MODE_BINARY, self::UA_SOFTBANK);

        $this->assertSame($to, '&#xE225;');
    }


    /**
     * 携帯以外の場合、Unicode絵文字に変換
     * 
     */
    public function testDocomoBinaryForNone()
    {
        $from = $this->getTestString("testDocomoEmojiBinary.txt");
        $to = self::$converter->convert($from, EmojiConverter::MODE_BINARY, self::UA_CHROME);

        $this->assertSame($to, "0️⃣");
    }


    private function getTestString(string $fileName): string
    {
        $filePath = dirname(__FILE__)."/data/".$fileName;
        return file_get_contents($filePath);
    }
}