<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
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
 * @property Audit[] $audits
 */
class Adminuser extends ActiveRecord implements IdentityInterface
{
    const BOSS = 1;         // 院长
    const DEAN = 2;         // 教学副院长
    const OFFICE = 3;       // 院办
    const DIRECTOR = 4;     // 系主任
    const LABORATORY = 5;   // 实验中心副主任
    const COUNSELOR = 6;    // 辅导员

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adminuser';
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'nickname'], 'trim'],
            [['username', 'nickname', 'role'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            ['username', 'unique', 'message' => '{attribute}已存在！'],
            ['nickname', 'string', 'max' => 128],
            ['role', 'integer'],
            ['role', 'in', 'range' => [
                self::BOSS,
                self::DEAN,
                self::OFFICE,
                self::DIRECTOR,
                self::LABORATORY,
                self::COUNSELOR,
            ]],
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
            'username' => '工号',
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
    public function getAudits()
    {
        return $this->hasMany(Application::className(), ['id' => 'application_id'])
                    ->viaTable(Audit::className(), ['adminuser_id' => 'id']);
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
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * 根据password_reset_token找到对应记录
     * @param string $token 密码重置令牌
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
     * 验证密码是否正确
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * 设置密码
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 生成自动登录令牌
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
     * @return string role属性值对应的中文
     */
    public function getRoleStr()
    {
        $roleStr = [
            self::BOSS => '院长',
            self::DEAN => '教学副院长',
            self::OFFICE => '院办',
            self::DIRECTOR => '系主任',
            self::LABORATORY => '实验中心副主任',
            self::COUNSELOR => '辅导员',
        ];
        return $roleStr[$this->role];
    }

    /**
     * @return array 所有角色数组
     */
    public static function allRoles()
    {
        return [
            self::BOSS => '院长',
            self::DEAN => '教学副院长',
            self::OFFICE => '院办',
            self::DIRECTOR => '系主任',
            self::LABORATORY => '实验中心副主任',
            self::COUNSELOR => '辅导员',
        ];
    }

    /**
     * @return array 以id为索引的所有辅导员角色数组
     */
    public static function allCounselors()
    {
        return self::find()->select(['nickname','id'])->where(['role'=>self::COUNSELOR])->indexBy('id')->column();
    }

}
