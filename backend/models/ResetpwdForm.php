<?php
namespace backend\models;

use common\models\Adminuser;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * 重置密码表单
 */
class ResetpwdForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 5],
        		
        	['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致！'],
        ];
    }

    public function attributeLabels()
    {
    	return [
    	    'password' => '密码',
            'password_repeat'=>'确认密码',
    	];
    }
    
    
    /**
     * 更新密码
     * @return bool
     */
    public function resetPassword($id)
    {
        if (!$this->validate()) {
            return null;
        }
        if (Yii::$app->controller->id=='user') {
            $user = User::findOne($id);
        } else {
            $user = Adminuser::findOne($id);
        }
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        
        return $user->save() ? true : false;
    }
}
