<?php

namespace app\models;

use app\components\ActiveRecord;
use Yii;
use yii\log\Logger;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int|null $level
 * @property string|null $category
 * @property float|null $log_time
 * @property string|null $prefix
 * @property string|null $message
 */
class Log extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'level' => Yii::t('app', 'Level'),
            'category' => Yii::t('app', 'Category'),
            'log_time' => Yii::t('app', 'Log Time'),
            'prefix' => Yii::t('app', 'Prefix'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    public static function resolveTotalCount($key)
    {
        switch ($key) {
            case Logger::LEVEL_ERROR:
            case Logger::LEVEL_WARNING:
            case Logger::LEVEL_INFO:
            case Logger::LEVEL_TRACE: return self::find()->where(['level' => $key])->count();
            case 'other': return self::find()->where(['not in', 'level', [Logger::LEVEL_ERROR, Logger::LEVEL_WARNING, Logger::LEVEL_INFO, Logger::LEVEL_TRACE]])->count();
            case 'default': return self::find()->count();
        }

        return -1;
    }

    public static function levels()
    {
        return [
            Logger::LEVEL_ERROR => 'error',
            Logger::LEVEL_WARNING => 'warning',
            Logger::LEVEL_INFO => 'info',
            Logger::LEVEL_TRACE => 'trace',
            Logger::LEVEL_PROFILE_BEGIN => 'profile begin',
            Logger::LEVEL_PROFILE_END => 'profile end',
            Logger::LEVEL_PROFILE => 'profile',
        ];
    }

    public function getLevelName()
    {
        return self::levels()[$this->level] ?? 'unknown';
    }
}
