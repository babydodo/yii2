<?php
namespace backend\models;

use common\models\Adminuser;
use Yii;
use yii\base\Model;

/**
 * 后台登陆表单模型
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            // 根据validatePassword()方法验证password
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '职工号',
            'password' => '密码',
            'rememberMe' => '记住我',
        ];
    }

    /**
     * 验证密码
     * @param string $attribute
     * @param array $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }

    /**
     * 登陆
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * 根据username找到Adminuser表对应记录
     * @return Adminuser|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Adminuser::findByUsername($this->username);
        }

        return $this->_user;
    }
}
