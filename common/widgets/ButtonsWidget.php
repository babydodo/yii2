<?php
namespace common\widgets;

use common\models\Classroom;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * 多按钮小部件
 * @property Classroom[] $classrooms
 */
class ButtonsWidget extends Widget
{
    public $classrooms;

    /**
     * 数据初始化
     */
    public function init()
    {
        parent::init();
    }

    /**
     * 逻辑处理
     * @return string 视图代码
     */
    public function run()
    {
        $buttons = array();
        foreach ($this->classrooms as $classroom) {
            if ($classroom->type == Classroom::TYPE_ORDINARY) {
                $buttons[] = Html::button($classroom->name, ['style'=>"margin-bottom: 4px;", 'class' => 'btn btn-default']);
            } elseif ($classroom->type == Classroom::TYPE_SPECIAL) {
                $buttons[] = Html::button($classroom->name, ['style'=>"margin-bottom: 4px;", 'class' => 'btn btn-warning']);
            }
        }

        return '<div>'.implode("\n", $buttons).'</div>';
    }

}
