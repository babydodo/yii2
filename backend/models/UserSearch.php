<?php

namespace backend\models;

use common\models\Adminuser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * User模型搜索过滤类
 *
 * @property string className
 */
class UserSearch extends User
{
    /**
     * 增加className属性
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['className']);
    }

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'class_id'], 'integer'],
            [['username', 'nickname', 'className', 'auth_key', 'password_hash', 'password_reset_token'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * 根据过滤条件查询数据
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->orderBy(['class_id' => SORT_ASC, 'username' => SORT_ASC]); //排序

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['defaultPageSize' => 10],  //分页
        ]);



        // 块赋值查询条件
        $this->load($params);

        // 验证不通过时返回的结果
        if (!$this->validate()) {
            return $dataProvider;
        }

        // 连接classes表
        $query->joinWith('class');

        // 如果角色是辅导员,则只能查询所带班级的学生
        if (Yii::$app->user->identity->role == Adminuser::COUNSELOR) {
            $query->andWhere(['adminuser_id' => Yii::$app->user->id]);
        }

        // 查询条件
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like','classes.name',$this->className]);

        // 增加className属性正倒排序
        $dataProvider->sort->attributes['className'] = [
            'asc'=>['classes.name'=>SORT_ASC],
            'desc'=>['classes.name'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
