<?php

namespace app\models;

use app\components\ActiveRecord;
use app\rbac\RbacEnum;
use yii\base\NotSupportedException;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException;
    }

    public static function findByGoogleId($id)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::find()
            ->where(['email' => $username])
            ->andWhere('deleted_at is null')
            ->one();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function resolveTotalCount($key)
    {
        switch ($key) {
            case 'default': return self::find()->andWhere('deleted_at is null')->count();
        }

        return -1;
    }

    public function beforeSave($insert)
    {
        $this->group = $this->group ?: RbacEnum::DEFAULT_GROUP;

        return parent::beforeSave($insert);
    }
}
