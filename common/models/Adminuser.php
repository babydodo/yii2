<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

/**
 * adminuser表模型类
 *
 * @property integer $id
 * @property string $username
 * @property string $nickname
 * @property integer $role
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 *
 * @property Classes[] $classes
 * @property Push[] $pushes
 */
class Adminuser extends \yii\db\ActiveRecord implements IdentityInterface
{
    const DIRECTOR = 1;
    const DEAN = 2;
    const LABORATORY = 3;
    const COUNSELOR = 4;

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
            [['username', 'nickname', 'role'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            ['username', 'unique'],
            ['nickname', 'string', 'max' => 128],
            [['role'], 'integer'],
            ['email', 'email'],
            ['email', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '教职工号',
            'nickname' => '姓名',
            'role' => '角色',
            'email' => '邮箱',
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

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * 根据username找到对应记录
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * 根据password_reset_token找到对应记录
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * 检验password_reset_token是否有效
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 验证密码
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * 设置密码
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 生成密码重置令牌
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * 移除密码重置令牌
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return null|string
     */
    public function getRoleStr()
    {
        switch ($this->role) {
            case self::DIRECTOR : $role = '系主任'; break;
            case self::DEAN : $role = '副院长'; break;
            case self::LABORATORY : $role = '实验中心'; break;
            case self::COUNSELOR : $role = '辅导员'; break;
            default : $role = NULL;
        }
        return $role;
    }

    /**
     * 以id为索引的所有管理员数组
     * @return array
     */
    public static function allAdminusers()
    {
        return self::find()->select(['nickname','id'])->indexBy('id')->column();
    }

    /**
     * 所有角色数组
     * @return array
     */
    public static function allRoles()
    {
        return [
            self::DIRECTOR => '系主任',
            self::DEAN => '副院长',
            self::LABORATORY => '实验中心',
            self::COUNSELOR => '辅导员',
        ];
    }


}
