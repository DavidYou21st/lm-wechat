<?php
/**
 * Author david you
 * Date 2024/4/2
 * Time 20:11
 */


namespace App\Common\Helper;

use Closure;
use Exception;

class Util
{
    /**
     * 闭包函数序列化器
     *
     * @var Serializer
     */
    protected static $closureSerializer;

    /**
     * 根据模版和数据合成字符串
     *
     * @param string $template
     * @param array $data
     * @param Closure|null $onError
     *
     * @return string
     */
    public static function vsprintf(string $template, array $data, Closure $onError = null): string
    {
        if ($template && !(empty($data))) {
            try {
                $template = vsprintf($template, $data);
            } catch (\Exception $e) {
                if ($onError) {
                    call_user_func($onError, $e);
                }
            }
        }

        return $template;
    }

    /**
     * 获取路径中的path部分
     *
     * @param string $url
     * @param Closure|null $onError
     *
     * @return string
     */
    public static function pathOfUrl(string $url, Closure $onError = null): string
    {
        $path = '';
        try {
            $parsed = parse_url($url);
            $path = $parsed['path'];
        } catch (\Exception $e) {
            if ($onError) {
                call_user_func($onError, $e);
            }
        }

        return $path;
    }

    /**
     * 获取闭包函数序列化器
     *
     * @return Serializer
     */
    public static function getClosureSerializer(): Serializer
    {
        if (!self::$closureSerializer) {
            self::$closureSerializer = new Serializer();
        }

        return self::$closureSerializer;
    }

    /**
     * 序列化闭包函数
     *
     * @param Closure $closure
     *
     * @return string
     */
    public static function serializeClosure(Closure $closure): string
    {
        return self::getClosureSerializer()->serialize($closure);
    }

    /**
     * 反序列化闭包函数
     *
     * @param $serializedClosure
     *
     * @return Closure
     */
    public static function unserializeClosure($serializedClosure): Closure
    {
        return self::getClosureSerializer()->unserialize($serializedClosure);
    }

    /**
     * 解决中文encode的乱码
     * @param $data
     * @return false|string
     */
    public static function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 封装是否decode为数组还是对象
     * @param $data
     * @param $is_array
     * @return false|mixed|null
     */
    public static function jsonDecode($data, $is_array = true)
    {
        $val = null;
        if (is_string($data)) {
            $val = json_decode($data, $is_array);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $val;
            } else {
                return false;
            }
        }
        return $val;
    }


    /**
     * 获得当前使用内存
     * @return string
     */
    public static function memoryUsage()
    {
        $memory = memory_get_usage();
        return self::convert($memory);
    }

    /**
     * 获得最高使用内存
     * @return string
     */
    public static function memoryPeakUsage()
    {
        $memory = memory_get_peak_usage();
        return self::convert($memory);
    }

    /**
     * 转换大小单位
     * @param $size
     * @return string
     */
    public static function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * N位随机字符串
     * @param $num
     * @return string
     */
    public static function randStr($num = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = "";
        for ($i = 0; $i < $num; $i++) {
            $string .= substr($chars, rand(0, strlen($chars)), 1);
        }
        return $string;
    }

    /**
     * N位随机数字
     * @param $num
     * @return string
     */
    public static function randNum($num = 7)
    {
        $rand = "";
        for ($i = 0; $i < $num; $i++) {
            $rand .= mt_rand(0, 9);
        }
        return $rand;
    }

    /**
     * @param $str
     * @return string
     */
    public static function escape($str)
    {
        preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r);
        $ar = $r[0];
        foreach ($ar as $k => $v) {
            if (ord($v[0]) < 128) {
                $ar[$k] = rawurlencode($v);
            } else {
                $ar[$k] = "%u" . bin2hex(iconv(NUll, "UCS-2", $v));
            }
        }
        return join("", $ar);
    }

    /**
     * @param $str
     * @return string
     */
    public static function unescape($str)
    {
        $str = rawurldecode($str);
        preg_match_all("/(?:%u.{4})|.+/", $str, $r);
        $ar = $r[0];
        foreach ($ar as $k => $v) {
            if (substr($v, 0, 2) == "%u" && strlen($v) == 6) {
                $ar[$k] = iconv("UCS-2", NULL, pack("H4", substr($v, -4)));
            }
        }
        return join("", $ar);
    }

    // 获得某天前的时间戳
    public static function getxTime($day)
    {
        $day = intval($day);
        return mktime(23, 59, 59, date("m"), date("d") - $day, date("y"));
    }

    // 是否是utf8
    public static function isUtf8($str)
    {
        if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * utf8编码模式的中文截取2，单字节截取模式
     * 这里不使用mbstring扩展
     * @return string
     */
    public static function utf8Substr($str, $slen, $startdd = 0)
    {
        return mb_substr($str, $startdd, $slen, 'UTF-8');
    }

    /**
     * 数字金额转换为中文
     * @param string|integer|float $num 目标数字
     * @param boolean $sim 使用小写（默认）
     * @return string
     */
    public static function number2chinese($num, $sim = false)
    {
        if (!is_numeric($num)) return '含有非数字非小数点字符！';
        $char = $sim ? array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九') : array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
        $unit = $sim ? array('', '十', '百', '千', '', '万', '亿', '兆') : array('', '拾', '佰', '仟', '', '萬', '億', '兆');
        $retval = '';
        $num = sprintf("%01.2f", $num);
        list ($num, $dec) = explode('.', $num);

        // 小数部分
        if ($dec['0'] > 0) {
            $retval .= "{$char[$dec['0']]}角";
        }
        if ($dec['1'] > 0) {
            $retval .= "{$char[$dec['1']]}分";
        }

        // 整数部分
        if ($num > 0) {
            $retval = "元" . $retval;
            $f = 1;
            $str = strrev(intval($num));
            for ($i = 0, $c = strlen($str); $i < $c; $i++) {
                if ($str[$i] > 0) {
                    $f = 0;
                }

                if ($f == 1 && $str[$i] == 0) {
                    $out[$i] = "";
                } else {
                    $out[$i] = $char[$str[$i]];
                }

                $out[$i] .= $str[$i] != '0' ? $unit[$i % 4] : '';
                if ($i > 1 and $str[$i] + $str[$i - 1] == 0) {
                    $out[$i] = '';
                }

                if ($i % 4 == 0) {
                    $out[$i] .= $unit[4 + floor($i / 4)];
                }
            }
            $retval = join('', array_reverse($out)) . $retval;
        }
        return $retval;
    }

    /**
     * 获取当前毫秒时间
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 获取当前毫秒时间
     * @return float
     */
    public static function miSec()
    {
        return self::getMillisecond();
    }

    /**
     * 获取2个时间之间的间隔天数
     * @param $start_day
     * @param $end_day
     * @return float|int
     */
    public static function intervalday($start_day, $end_day)
    {
        $start_second = strtotime($start_day);
        $end_second = strtotime($end_day);

        if ($start_second < $end_second) {
            $tmp = $end_second;
            $end_second = $start_second;
            $start_second = $tmp;
        }

        return ($start_second - $end_second) / 86400;
    }

    /**
     * randTime
     *
     * @param mixed $s 开始时间
     * @param mixed $e 结束时间
     * @param mixed $format 返回时间格式化 "Y-m-d H:i:s"
     * @static
     * @return int|string
     */
    public static function randTime($s, $e, $format = false)
    {
        $s = strtotime($s);
        $e = strtotime($e);
        $rand_time = rand($s, $e);
        if ($format !== false) {
            return date($format, $rand_time);
        }
        return $rand_time;
    }

    /**
     * 行读取文件到数组，并返回
     * @param $file
     * @return array
     * @throws Exception
     */
    public static function lineRead($file)
    {
        $data = array();
        $fp = @fopen($file, "r");

        if ($fp) {
            while (!feof($fp)) {
                $line = trim(fgets($fp));
                if ($line) {
                    $data[] = $line;
                }
            }
        } else {
            throw new \Exception("Open {$file} error. ");
        }

        @fclose($fp);
        return $data;
    }

    /**
     * 将字符替换成另外一个字符
     * @param $str
     * @return string
     */
    public static function makeSemiangle($str)
    {
        $arr = [
            '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '〔' => '[',
            '〕' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']',
            '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%',
            '＋' => '+', '—' => '-', '－' => '-', '～' => '-', '：' => ':',
            '。' => '.', '、' => ',', '，' => '.', '、' => '.', '；' => ';',
            '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '”' => '"',
            '“' => '"', "'" => '`', '‘' => '`', '｜' => '|', '〃' => '"', '　' => ' ', '．' => '.'
        ];
        return strtr($str, $arr);
    }

    /**
     * ftok 重写ftok函数，防止跨语言时ftok的实现不一样，导致找不到同一个key
     * IPC通讯（消息队列、信号量和共享内存）时必须指定一个ID值。通常情况下，该id值通过ftok函数得到
     * 在一般的UNIX实现中，是将文件的索引节点号取出，前面加上子序号得到key_t的返回值。
     * 如指定文件的索引节点号为65538，换算成16进制为0x010002，而你指定的ID值为38，
     * 换算成16进制为0x26，则最后的key返回值为0x26010002
     * @param mixed $pathname 就是你指定的文件名（已经存在的文件名），一般使用当前目录
     * @param mixed $proj_id 是子序号。虽然是C中int类型，但是只使用8bits(1-255）, 在PHP中用字符来代替了0-255
     * @static
     * @return int|string
     */
    public static function ftok($pathname, $proj_id)
    {
        $st = @stat($pathname);
        if (!$st) {
            return -1;
        }
        $key = sprintf("%u", (($st['ino'] & 0xffff) | (($st['dev'] & 0xff) << 16) | (($proj_id & 0xff) << 24)));
        return $key;
    }


    /**
     * print_r
     * 格式化数组
     *
     * @return void
     * @author zero <512888425@qq.com>
     * @created time :2017-09-05 15:28
     */
    public static function print_r($arr)
    {
        if (!empty($arr)) {
            echo "<pre>";
            print_r($arr);
            die;
        }
    }
}
