<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;

/**
 * 新增用户表单模型
 */
class CreateUserForm extends Model
{
    public $username;
    public $nickname;
    public $class_id;
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户名已存在！'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['nickname', 'required'],
            ['nickname', 'string', 'max' => 128],

            ['class_id', 'required'],
            ['class_id', 'integer'],

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
     *
     * @return User|null the saved model or null if saving fails
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
