<?php


namespace app\models\edges;


interface EdgeInterface
{
    public static function create($id1, $id2, $params = []);
    public static function remove($id1, $id2, $params = []);
}