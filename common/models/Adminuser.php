<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adminuser".
 *
 * @property integer $id
 * @property string $username
 * @property string $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 *
 * @property Classes[] $classes
 * @property Push[] $pushes
 */
class Adminuser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adminuser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'nickname', 'auth_key', 'password_hash'], 'required'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['nickname'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasses()
    {
        return $this->hasMany(Classes::className(), ['adminuser_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPushes()
    {
        return $this->hasMany(Push::className(), ['adminuser_id' => 'id']);
    }
}
