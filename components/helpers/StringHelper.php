<?php


namespace app\components\helpers;


use yii\helpers\Json;

class StringHelper extends \yii\helpers\StringHelper
{

    public static function getLink2Passage($passage)
    {
        $p = explode(' ', $passage);

        if(count($p) > 2) {
            $last = array_pop($p);
            $p = [implode(' ', $p), $last];
        }

        $src = implode('.', $p);
        return "http://bible.ubf.org.ua/rst/?scr=" . urlencode($src);
    }

    public static function toHumanReadablePassage($json)
    {
        if(empty($json)) {
            return null;
        }

        $passage = Json::decode($json);

        $str = $passage[0];
        $str.= " ";
        if($passage[1]) $str.= $passage[1];
        if($passage[2]) $str.= ":" . $passage[2];

        if($passage[1] && $passage[3]) $str.= "-";
        if($passage[3] && $passage[1] != $passage[3]) $str.= $passage[3] . ($passage[4]?":":"");
        if($passage[4]) $str.= $passage[4];

        return $str;
    }

    public static function fromHumanReadablePassage($string)
    {
        if(empty($string)) {
            return null;
        }

        $j = ["", "", "", "", ""];

        $p = explode(' ', $string);
        // if there are more "spaces" then one: Chapter Name 2:3-4
        if(count($p) > 2) {
            $last = array_pop($p);
            $p = [implode(' ', $p), $last];
        }

        $j[0] = $p[0]; // Chapter Name

        $r = explode('-', $p[1]);
        $from = explode(':', $r[0]);
        $j[1] = $from[0] ?? '';
        $j[2] = $from[1] ?? '';
        $to = explode(':', $r[1]);
        $j[3] = $to[0] ?? ($from[0] ?? '');
        $j[4] = $to[1] ?? ($from[1] ?? '');

        return Json::encode($j);
    }

    public static function lang2locale($lang, $short = false)
    {
        $name = \Yii::$app->params['locales'][$lang] ?? $lang;

        return $short ? explode('-', $name)[0] : $name;
    }

}