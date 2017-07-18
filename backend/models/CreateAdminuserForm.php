<?php
namespace backend\models;

use common\models\Adminuser;
use yii\base\Model;


/**
 * 新增管理员表单模型
 */
class CreateAdminuserForm extends Model
{
    public $username;
    public $nickname;
    public $role;
    public $password;
    public $password_repeat;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\Adminuser', 'message' => '职工号已存在！'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['nickname', 'required'],
            ['nickname', 'string', 'max' => 128],

            ['role', 'required'],
            ['role', 'integer'],

            ['email', 'email'],

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
            'username' => '职工号',
            'nickname' => '姓名',
            'role' => '角色',
            'password' => '密码',
            'password_repeat' => '确认密码',
            'email' => '邮箱',
        ];
    }

    /**
     * 新增一个管理员
     *
     * @return Adminuser|null the saved model or null if saving fails
     */
    public function createAdminuser()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $Adminuser = new Adminuser();

        $Adminuser->username = $this->username;
        $Adminuser->nickname = $this->nickname;
        $Adminuser->role = $this->role;
        $Adminuser->email = $this->email;
        $Adminuser->setPassword($this->password);
        $Adminuser->generateAuthKey();
        
        return $Adminuser->save() ? $Adminuser : null;
    }
}
