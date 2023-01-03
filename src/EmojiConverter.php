<?php namespace Gayou\EmojiConverter;

use SimpleXMLElement;

/**
 * 絵文字変換クラス
 *
 * 携帯絵文字を各キャリア向けの絵文字に変換する
 *
 * @author gayou <admin@gayou.info>
 */
class EmojiConverter
{
    public const DOCOMO = 'docomo';
    public const KDDI = 'kddi';
    public const SOFTBANK = 'softbank';
    public const NONE = 'none';

    public const MODE_BINARY = 1;
    public const MODE_HTML = 2;
    
    private $mapping;
    
    function __construct()
    {
        //変換表ロード
        $filename = dirname(__FILE__)."/data/emoji4unicode4docomo.xml";
        $this->mapping = simplexml_load_file($filename);
    }
    
    
    /**
     * 携帯絵文字をデバイスに最適な文字に変換
     * 
     * @param string $str 変換対象の文字列
     * @param string $chartset 文字コード
     * @
     */
    public function convert($str, $mode = self::MODE_BINARY, $userAgent = null): string
    {
        //デバイス検出
        $device = $this->detectDevice($userAgent);
        
        //ドコモ、携帯以外の場合は変換しない
        if ($device === self::DOCOMO) {
            return $str;
        }

        //i絵文字抽出
        mb_substitute_character("long");
        $str = mb_convert_encoding($str, "SJIS", 'UTF-8');
        $pattern = $mode === self::MODE_BINARY ? "/U\+([A-F0-9]{4})/": "/&#x([A-F0-9]{4});/";
        preg_match_all($pattern, $str, $matches);
                
        //キャリア絵文字変換
        $mapping = $this->mapping; 
        foreach ($matches[1] as $emoji) {
            $xpath_query = '//e[@docomo="'.$emoji.'"][position()=1]';
            $result = $mapping->xpath($xpath_query);
            if (count($result) > 0) {
                foreach ($result as $e) {
                    $targetStr = $mode === self::MODE_BINARY ? 'U+'.$emoji: '&#x'.$emoji;
                    $str = str_replace($targetStr, $this->getEmoji($e, $device), $str);
                }
            }
        }

        return $str;
    }
    
    
    /**
     * 携帯キャリアを検出する
     * 
     * @param string $userAgent ユーザエージェント
     * @return string 携帯キャリア
     */
    public function detectDevice($userAgent = null): string
    {
        $userAgent = $userAgent ?? $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/^DoCoMo/', $userAgent)) {
            return self::DOCOMO;
        }
        else if (preg_match('/^KDDI-/', $userAgent)) {
            return self::KDDI;
        }
        else if (preg_match('/^(?:SoftBank|Vodafone|MOT-)/', $userAgent)) {
            return self::SOFTBANK;
        }
        else if (preg_match('/^J-PHONE/', $userAgent)) {
            return self::SOFTBANK;
        }
        return self::NONE;
    }


    /**
     * 絵文字コードを返却
     * 
     * @param SimpleXMLElement $e
     * @param  string $device
     * @return string
     */
    private function getEmoji(SimpleXMLElement $e, string $device): string
    {
        if ($device !== self::NONE) {
            return '&#x'.(string)$e->attributes()->$device.';';
        }

        //PCデバイスのときはUnicode絵文字バイナリを返却
        $bin = '';
        $codePoints = explode("+", (string)$e->attributes()->unicode);
        foreach ($codePoints as $code) {
            if (strlen($bin) > 0) {
                $bin .= hex2bin(str_repeat('0', 8 - strlen("FE0F"))."FE0F");
            }
            $bin .= hex2bin(str_repeat('0', 8 - strlen($code)).$code);
        }

        return mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
    }
    
}
