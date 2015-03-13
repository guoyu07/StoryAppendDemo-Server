<?php
/**
 * @project hitour.server
 * @file CodeUtility.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-7-30 下午5:42
 **/

class CodeUtility {

    public static function utf8_substr($string, $offset, $length = null) {
        // generates E_NOTICE
        // for PHP4 objects, but not PHP5 objects
        $string = (string)$string;
        $offset = (int)$offset;

        if (!is_null($length)) {
            $length = (int)$length;
        }

        // handle trivial cases
        if ($length === 0) {
            return '';
        }

        if ($offset < 0 && $length < 0 && $length < $offset) {
            return '';
        }

        // normalise negative offsets (we could use a tail
        // anchored pattern, but they are horribly slow!)
        if ($offset < 0) {
            $strlen = strlen(utf8_decode($string));
            $offset = $strlen + $offset;

            if ($offset < 0) {
                $offset = 0;
            }
        }

        $Op = '';
        $Lp = '';

        // establish a pattern for offset, a
        // non-captured group equal in length to offset
        if ($offset > 0) {
            $Ox = (int)($offset / 65535);
            $Oy = $offset%65535;

            if ($Ox) {
                $Op = '(?:.{65535}){' . $Ox . '}';
            }

            $Op = '^(?:' . $Op . '.{' . $Oy . '})';
        } else {
            $Op = '^';
        }

        // establish a pattern for length
        if (is_null($length)) {
            $Lp = '(.*)$';
        } else {
            if (!isset($strlen)) {
                $strlen = strlen(utf8_decode($string));
            }

            // another trivial case
            if ($offset > $strlen) {
                return '';
            }

            if ($length > 0) {
                $length = min($strlen - $offset, $length);

                $Lx = (int)($length / 65535);
                $Ly = $length % 65535;

                // negative length requires a captured group
                // of length characters
                if ($Lx) {
                    $Lp = '(?:.{65535}){' . $Lx . '}';
                }

                $Lp = '(' . $Lp . '.{' . $Ly . '})';
            } elseif ($length < 0) {
                if ($length < ($offset - $strlen)) {
                    return '';
                }

                $Lx = (int)((-$length) / 65535);
                $Ly = (-$length)%65535;

                // negative length requires ... capture everything
                // except a group of  -length characters
                // anchored at the tail-end of the string
                if ($Lx) {
                    $Lp = '(?:.{65535}){' . $Lx . '}';
                }

                $Lp = '(.*)(?:' . $Lp . '.{' . $Ly . '})$';
            }
        }

        if (!preg_match( '#' . $Op . $Lp . '#us', $string, $match)) {
            return '';
        }

        return $match[1];
    }

    public static function xmlValidCheck($xml)
    {
        $xml_parser = xml_parser_create();
        if(!xml_parse($xml_parser, $xml, true)){
            xml_parser_free($xml_parser);
            return false;
        }else {
            return true;
        }
    }

    public static function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
        $return = '';
        if (function_exists('mb_get_info')) {
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
                $str = mb_substr ( $string, $x, 1, $in_encoding );
                if (strlen ( $str ) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
                } else {
                    //$return .= '%' . strtoupper ( bin2hex ( $str ) );
                    $return .= $str;
                }
            }
        }
        return $return;
    }

    public static function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++)
        {
            if ($str[$i] == '%' && $str[$i + 1] == 'u')
            {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else
                    if ($val < 0x800)
                        $ret .= chr(0xc0 | ($val >> 6)) .
                            chr(0x80 | ($val & 0x3f));
                    else
                        $ret .= chr(0xe0 | ($val >> 12)) .
                            chr(0x80 | (($val >> 6) & 0x3f)) .
                            chr(0x80 | ($val & 0x3f));
                $i += 5;
            } else
                if ($str[$i] == '%')
                {
                    $ret .= urldecode(substr($str, $i, 3));
                    $i += 2;
                } else
                    $ret .= $str[$i];
        }
        return $ret;
    }

    public static function utf8SubString($str, $start, $length) {
        $i = 0;
        //完整排除之前的UTF8字符
        while($i < $start) {
            $ord = ord($str{$i});
            if($ord < 192) {
                $i++;
            } elseif($ord <224) {
                $i += 2;
            } else {
                $i += 3;
            }
        }
        //开始截取
        $result = '';
        while($i < $start + $length && $i < strlen($str)) {
            $ord = ord($str{$i});
            if($ord < 192) {
                $result .= $str{$i};
                $i++;
            } elseif($ord <224) {
                $result .= $str{$i}.$str{$i+1};
                $i += 2;
            } else {
                $result .= $str{$i}.$str{$i+1}.$str{$i+2};
                $i += 3;
            }
        }
        if($i < strlen($str)) {
            $result .= '...';
        }
        return $result;
    }

} 