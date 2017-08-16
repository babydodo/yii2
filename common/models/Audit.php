<?php

namespace common\models;

use Yii;

/**
 * audit表模型类
 *
 * @property integer $id
 * @property integer $application_id
 * @property integer $adminuser_id
 * @property integer $status
 * @property integer $audit_at
 * @property string $remark
 *
 * @property Application $application
 * @property Adminuser $adminuser
 */
class Audit extends \yii\db\ActiveRecord
{
    const STATUS_UNAUDITED = 1;
    const STATUS_FAILED = 0;
    const STATUS_PASS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'audit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'adminuser_id'], 'required'],
            [['application_id', 'adminuser_id', 'status', 'audit_at'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Application::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['adminuser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['adminuser_id' => 'id']],
            ['status', 'default', 'value' => self::STATUS_UNAUDITED],
            ['audit_at', 'default', 'value' => null],
            ['remark', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => '申请ID',
            'adminuser_id' => '审核人ID',
            'status' => '状态',
            'audit_at' => '审核时间',
            'remark' => '备注',
            'statusStr' => '状态',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if (!$insert) {
                $this->audit_at = time();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Application::className(), ['id' => 'application_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminuser()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'adminuser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusStr()
    {
        $statusStr = [
            Audit::STATUS_UNAUDITED=>'待审核',
            Audit::STATUS_FAILED=>'不同意',
            Audit::STATUS_PASS=>'同意',
        ];
        return $statusStr[$this->status];
    }
}
