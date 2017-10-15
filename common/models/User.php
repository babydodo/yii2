<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * user表模型类
 *
 * @property integer $id
 * @property string $username
 * @property string $nickname
 * @property integer $class_id
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $password write-only password
 *
 * @property Application[] $applications
 * @property Application[] $applications0
 * @property Course[] $courses
 * @property Elective[] $electives
 */
class User extends ActiveRecord implements IdentityInterface
{
    const TEACHER_CLASS = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'nickname'], 'trim'],
            [['username', 'nickname', 'class_id'], 'required'],
            ['class_id', 'integer'],
            ['class_id', 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']],
            ['username', 'string', 'max' => 255],
            ['nickname', 'string', 'max' => 128],
            ['username', 'unique', 'message' => '{attribute}已存在！'],
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
            'class_id' => '教师/班级',
            'className' => '教师 / 班级',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Application::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications0()
    {
        return $this->hasMany(Application::className(), ['teacher_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElectives()
    {
        return $this->hasMany(Course::className(), ['id' => 'course_id'])
                    ->viaTable('elective', ['user_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'class_id']);
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
     * @param string $token
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
     * @return array 所有的教师
     */
    public static function allTeachers()
    {
        return static::find()
                ->select(['nickname','id'])
                ->where(['class_id' => self::TEACHER_CLASS])
                ->indexBy('id')
                ->column();
    }

//    /**
//     * @return array 获取辅导员所带全部学生
//     */
//    public static function getAllStudents()
//    {
//        $classes_ids = Classes::find()->where(['adminuser_id' => Yii::$app->user->id])->column();
//        return static::find()->where(['class_id' => $classes_ids])->all();
//    }
}
