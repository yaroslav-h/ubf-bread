<?php
/**
 * Yii2 Shortcuts
 * @author Eugene Terentev <eugene@terentev.net>
 * -----
 * This file is just an example and a place where you can add your own shortcuts,
 * it doesn't pretend to be a full list of available possibilities
 * -----
 */

/**
 * @return int|string
 */
function getMyId()
{
    if(Yii::$app instanceof \yii\web\Application) {
        return Yii::$app->user->getId();
    }

    return null;
}

/**
 * @return \yii\web\IdentityInterface|app\models\User|null
 */
function getMyIdentity()
{
    if(Yii::$app instanceof \yii\web\Application) {
        return Yii::$app->user->getIdentity();
    }

    return null;
}

/**
 * @return bool|null
 */
function isGuest() {
    if(Yii::$app instanceof \yii\web\Application) {
        return Yii::$app->user->getIsGuest();
    }

    return null;
}

/**
 * @return bool|null
 */
function isLoggedIn() {
    if(Yii::$app instanceof \yii\web\Application) {
        return !Yii::$app->user->getIsGuest();
    }

    return null;
}

function can($permissionName, $params = [], $allowCaching = true) {
   return Yii::$app->user->can($permissionName, $params, $allowCaching);
}

/**
 * @param string $view
 * @param array $params
 * @return string
 */
function render($view, $params = [])
{
    return Yii::$app->controller->render($view, $params);
}

/**
 * @param $url
 * @param int $statusCode
 * @return \yii\web\Response
 */
function redirect($url, $statusCode = 302)
{
    return Yii::$app->controller->redirect($url, $statusCode);
}

function request() {
    return Yii::$app->request;
}
function response() {
    return Yii::$app->response;
}
function lang() {
    return LOCALE_UK_UA;
}
function post($name = null, $defaultValue = null) {
    return Yii::$app->request->post($name, $defaultValue);
}

function get($name = null, $defaultValue = null) {
    return Yii::$app->request->get($name, $defaultValue);
}

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{

    $value = getenv($key) ?? $_ENV[$key] ?? $_SERVER[$key];

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

function dd($expression = 'dd') {
    var_dump($expression);
    die;
}