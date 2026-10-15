<?php

namespace App\Helpers;

class ArabicReshaper
{
    private static $map = [
        // Char => [Isolated, Initial, Medial, Final, ConnectsNext]
        'ء' => ["\u{FE80}", "\u{FE80}", "\u{FE80}", "\u{FE80}", false],
        'آ' => ["\u{FE81}", "\u{FE81}", "\u{FE82}", "\u{FE82}", false],
        'أ' => ["\u{FE83}", "\u{FE83}", "\u{FE84}", "\u{FE84}", false],
        'ؤ' => ["\u{FE85}", "\u{FE85}", "\u{FE86}", "\u{FE86}", false],
        'إ' => ["\u{FE87}", "\u{FE87}", "\u{FE88}", "\u{FE88}", false],
        'ئ' => ["\u{FE89}", "\u{FE8B}", "\u{FE8C}", "\u{FE8A}", true],
        'ا' => ["\u{FE8D}", "\u{FE8D}", "\u{FE8E}", "\u{FE8E}", false],
        'ب' => ["\u{FE8F}", "\u{FE91}", "\u{FE92}", "\u{FE90}", true],
        'ة' => ["\u{FE93}", "\u{FE93}", "\u{FE94}", "\u{FE94}", false],
        'ت' => ["\u{FE95}", "\u{FE97}", "\u{FE98}", "\u{FE96}", true],
        'ث' => ["\u{FE99}", "\u{FE9B}", "\u{FE9C}", "\u{FE9A}", true],
        'ج' => ["\u{FE9D}", "\u{FE9F}", "\u{FEA0}", "\u{FE9E}", true],
        'ح' => ["\u{FEA1}", "\u{FEA3}", "\u{FEA4}", "\u{FEA2}", true],
        'خ' => ["\u{FEA5}", "\u{FEA7}", "\u{FEA8}", "\u{FEA6}", true],
        'د' => ["\u{FEA9}", "\u{FEA9}", "\u{FEAA}", "\u{FEAA}", false],
        'ذ' => ["\u{FEAB}", "\u{FEAB}", "\u{FEAC}", "\u{FEAC}", false],
        'ر' => ["\u{FEAD}", "\u{FEAD}", "\u{FEAE}", "\u{FEAE}", false],
        'ز' => ["\u{FEAF}", "\u{FEAF}", "\u{FEB0}", "\u{FEB0}", false],
        'س' => ["\u{FEB1}", "\u{FEB3}", "\u{FEB4}", "\u{FEB2}", true],
        'ش' => ["\u{FEB5}", "\u{FEB7}", "\u{FEB8}", "\u{FEB6}", true],
        'ص' => ["\u{FEB9}", "\u{FEBB}", "\u{FEBC}", "\u{FEBA}", true],
        'ض' => ["\u{FEBD}", "\u{FEBF}", "\u{FEC0}", "\u{FEBE}", true],
        'ط' => ["\u{FEC1}", "\u{FEC3}", "\u{FEC4}", "\u{FEC2}", true],
        'ظ' => ["\u{FEC5}", "\u{FEC7}", "\u{FEC8}", "\u{FEC6}", true],
        'ع' => ["\u{FEC9}", "\u{FECB}", "\u{FECC}", "\u{FECA}", true],
        'غ' => ["\u{FECD}", "\u{FECF}", "\u{FED0}", "\u{FECE}", true],
        'ف' => ["\u{FED1}", "\u{FED3}", "\u{FED4}", "\u{FED2}", true],
        'ق' => ["\u{FED5}", "\u{FED7}", "\u{FED8}", "\u{FED6}", true],
        'ك' => ["\u{FED9}", "\u{FEDB}", "\u{FEDC}", "\u{FEDA}", true],
        'ل' => ["\u{FEDD}", "\u{FEDF}", "\u{FEE0}", "\u{FEDE}", true],
        'م' => ["\u{FEE1}", "\u{FEE3}", "\u{FEE4}", "\u{FEE2}", true],
        'ن' => ["\u{FEE5}", "\u{FEE7}", "\u{FEE8}", "\u{FEE6}", true],
        'ه' => ["\u{FEE9}", "\u{FEEB}", "\u{FEEC}", "\u{FEEA}", true],
        'و' => ["\u{FEED}", "\u{FEED}", "\u{FEEE}", "\u{FEEE}", false],
        'ى' => ["\u{FEEF}", "\u{FEEF}", "\u{FEF0}", "\u{FEF0}", false],
        'ي' => ["\u{FEF1}", "\u{FEF3}", "\u{FEF4}", "\u{FEF2}", true],
        'لا' => ["\u{FEFB}", "\u{FEFB}", "\u{FEFC}", "\u{FEFC}", false],
        'لأ' => ["\u{FEF7}", "\u{FEF7}", "\u{FEF8}", "\u{FEF8}", false],
        'لإ' => ["\u{FEF9}", "\u{FEF9}", "\u{FEFA}", "\u{FEFA}", false],
        'لآ' => ["\u{FEF5}", "\u{FEF5}", "\u{FEF6}", "\u{FEF6}", false],
    ];

    public static function fix($text)
    {
        if (empty($text) || !is_string($text)) {
            return $text;
        }

        // If ArPHP package is loaded, prefer it
        if (class_exists('ArPHP\I18N\Arabic')) {
            try {
                $arabic = new \ArPHP\I18N\Arabic('Glyphs');
                return $arabic->utf8Glyphs($text);
            } catch (\Exception $e) {
                // fallback to native reshaper
            }
        }

        return self::reshape($text);
    }

    public static function reshape($text)
    {
        // Replace Lam-Alef ligatures
        $text = str_replace(['لأ', 'لإ', 'لآ', 'لا'], ["\u{FEF7}", "\u{FEF9}", "\u{FEF5}", "\u{FEFB}"], $text);

        $chars = mb_str_split($text);
        $count = count($chars);
        $output = [];

        for ($i = 0; $i < $count; $i++) {
            $c = $chars[$i];

            if (!isset(self::$map[$c])) {
                $output[] = $c;
                continue;
            }

            $prev = ($i > 0 && isset(self::$map[$chars[$i - 1]])) ? $chars[$i - 1] : null;
            $next = ($i < $count - 1 && isset(self::$map[$chars[$i + 1]])) ? $chars[$i + 1] : null;

            $prevConnects = $prev && (self::$map[$prev][4] ?? false);
            $nextConnects = $next !== null;

            if ($prevConnects && $nextConnects) {
                // Medial form
                $output[] = self::$map[$c][2];
            } elseif ($prevConnects) {
                // Final form
                $output[] = self::$map[$c][3];
            } elseif ($nextConnects) {
                // Initial form
                $output[] = self::$map[$c][1];
            } else {
                // Isolated form
                $output[] = self::$map[$c][0];
            }
        }

        // Reverse reshaped Arabic tokens for DomPDF visual right-to-left alignment
        return self::reverseRtlTokens(implode('', $output));
    }

    private static function reverseRtlTokens($str)
    {
        $words = preg_split('/(\s+)/u', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = [];

        foreach ($words as $word) {
            // Check if contains Arabic glyph range (U+FE70 to U+FEFF or U+0600 to U+06FF)
            if (preg_match('/[\x{FE70}-\x{FEFF}\x{0600}-\x{06FF}]/u', $word)) {
                $letters = mb_str_split($word);
                $result[] = implode('', array_reverse($letters));
            } else {
                $result[] = $word;
            }
        }

        return implode('', array_reverse($result));
    }
}
