<?php
namespace backend\models;

use common\models\Classes;
use yii\base\Model;
use common\models\User;

/**
 * 新增User模型表单
 */
class CreateUserForm extends Model
{
    public $username;
    public $nickname;
    public $class_id;
    public $password;
    public $password_repeat;

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::className(), 'message' => '{attribute}已存在！'],
            ['username', 'string', 'min' => 5, 'max' => 255],

            ['nickname', 'trim'],
            ['nickname', 'required'],
            ['nickname', 'string', 'max' => 128],

            ['class_id', 'required'],
            ['class_id', 'integer'],
            ['class_id', 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']],

            ['password', 'required'],
            ['password', 'string', 'min' => 5],

            ['password_repeat', 'required'],
            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次密码不一致！'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '学号/职工号',
            'nickname' => '姓名',
            'password' => '密码',
            'password_repeat' => '确认密码',
            'class_id' => '教师/班级',
        ];
    }

    /**
     * 新增一个用户
     * @return User|null
     */
    public function createUser()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->nickname = $this->nickname;
        $user->class_id = $this->class_id;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }
}
