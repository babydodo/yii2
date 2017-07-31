<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "audit".
 *
 * @property integer $id
 * @property integer $application_id
 * @property integer $adminuser_id
 * @property integer $status
 * @property string $remark
 *
 * @property Application $application
 * @property Adminuser $adminuser
 */
class Audit extends \yii\db\ActiveRecord
{
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
            [['application_id', 'adminuser_id', 'status'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Application::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['adminuser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['adminuser_id' => 'id']],
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
            'remark' => '备注',
        ];
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
}
