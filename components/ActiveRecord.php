<?php


namespace app\components;


use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ActiveRecord extends \yii\db\ActiveRecord
{
    private static $_totalCount = [];
    public static function totalCount($key = 'default')
    {
        $_key = get_called_class() . $key;

        if((self::$_totalCount[$_key] ?? null) === null) {
            self::$_totalCount[$_key] = static::resolveTotalCount($key);
        }
        return self::$_totalCount[$_key];
    }

    public static function resolveTotalCount($key)
    {
        return 0;
    }

    public function softDelete()
    {
        if (!$this->isTransactional(self::OP_DELETE)) {
            return $this->softDeleteInternal();
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->softDeleteInternal();
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    protected function softDeleteInternal()
    {
        if (!$this->beforeDelete()) {
            return false;
        }

        // we do not check the return value of deleteAll() because it's possible
        // the record is already deleted in the database and thus the method will return 0
        $condition = $this->getOldPrimaryKey(true);
        $lock = $this->optimisticLock();
        if ($lock !== null) {
            $condition[$lock] = $this->$lock;
        }

        $result = static::updateAll(['deleted_at' => time()], $condition);

        if ($lock !== null && !$result) {
            throw new StaleObjectException('The object being deleted is outdated.');
        }
        $this->setOldAttributes(null);
        $this->afterDelete();

        return $result;
    }

    /**
     * @param $condition
     * @return static|ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public static function findOneOrFail($condition)
    {
        if($model = static::findOne($condition)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Item not found.');
        }
    }
}