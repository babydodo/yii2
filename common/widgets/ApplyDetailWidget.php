<?php
namespace common\widgets;

use common\models\Application;
use common\models\Audit;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * 多按钮小部件
 * @property Application $application
 */
class ApplyDetailWidget extends Widget
{
    public $application = null;

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
        if ($this->application == null) {
            return null;
        }

        /*
         * 申请表部分
         */
        $dayStr = ['', '一', '二', '三', '四', '五', '六', '天'];
        $classesStr = implode(', ', ArrayHelper::getColumn($this->application->course->classes, 'name'));
        $apply_at = \Yii::$app->formatter->asDatetime($this->application->apply_at);
        $before_time = '第'.$this->application->apply_week.'周 星期'.$dayStr[$this->application->course->day].' 第'.$this->application->apply_sec.'节';
        $this->application->adjust_day == null ? $this->application->adjust_day=0 : null;
        $after_time = '第' . $this->application->adjust_week . '周 星期' . $dayStr[$this->application->adjust_day] . ' 第' . $this->application->adjust_sec . '节';

        if ($this->application->type == Application::TYPE_ADJUST) {
            $table = <<<EOT
            <div class="table-responsive">
                <table class="table table-bordered">
                    
                    <thead>
                        <th class="text-center" colspan='3'>调课</th>
                    </thead>

                    <tbody>
                        <tr>
                            <th>课程</th>
                            <td colspan='2'>{$this->application->course->name}</td>
                        </tr>                        
                        <tr>
                            <th>授课班级</th>
                            <td colspan='2'>{$classesStr}</td>
                        </tr>                        
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">调整前</th>
                            <th class="text-center">调整后</th>
                        </tr>          
                        <tr>
                            <th>教师</th>
                            <td>{$this->application->user->nickname}</td>
                            <td>{$this->application->teacher->nickname}</td>
                        </tr>
                        <tr>
                            <th>时间</th>
                            <td>{$before_time}</td>
                            <td>{$after_time}</td>
                        </tr>
                        <tr>
                            <th>地点</th>
                            <td>{$this->application->course->classroom->name}</td>
                            <td>{$this->application->classroom->name}</td>
                        </tr>
                        <tr>
                            <th>事由</th>
                            <td colspan='2'>{$this->application->reason}</td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td colspan='2'>{$apply_at}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
EOT;
        } elseif($this->application->type == Application::TYPE_SUSPEND) {
            $table = <<<EOT
            <div class="table-responsive">
                <table class="table table-bordered">
                    
                    <thead>
                        <th class="text-center" colspan='2'>停课</th>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <th>申请人</th>
                            <td>{$this->application->user->nickname}</td>
                        </tr> 
                        <tr>
                            <th>课程</th>
                            <td>{$this->application->course->name}</td>
                        </tr>
                        <tr>
                            <th>停课班级</th>
                            <td>{$classesStr}</td>
                        </tr>
                        <tr>
                            <th>停课时间</th>
                            <td>{$before_time}</td>
                        </tr>
                        <tr>
                            <th>停课地点</th>
                            <td>{$this->application->course->classroom->name}</td>
                        </tr>
                        <tr>
                            <th>事由</th>
                            <td>{$this->application->reason}</td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td>{$apply_at}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
EOT;
        } elseif ($this->application->type == Application::TYPE_SCHEDULE) {
            $table = <<<EOT
            <div class="table-responsive">
                <table class="table table-bordered">
                    
                    <thead>
                        <th class="text-center" colspan='2'>排课</th>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <th>申请人</th>
                            <td>{$this->application->user->nickname}</td>
                        </tr>
                        <tr>
                            <th>课程</th>
                            <td>{$this->application->course->name}</td>
                        </tr>
                        <tr>
                            <th>授课班级</th>
                            <td>{$classesStr}</td>
                        </tr>
                        <tr>
                            <th>授课教师</th>
                            <td>{$this->application->teacher->nickname}</td>
                        </tr>
                        <tr>
                            <th>授课时间</th>
                            <td>{$after_time}</td>
                        </tr>
                        <tr>
                            <th>授课地点</th>
                            <td>{$this->application->classroom->name}</td>
                        </tr>
                        <tr>
                            <th>事由</th>
                            <td>{$this->application->reason}</td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td>{$apply_at}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
EOT;
        } else {
            $table = null;
        }

        /*
         * 进度框部分
         */
        $progress = '<div class="panel panel-default">';

        // 进度列表
        $progress .= '<div class="text-center panel-heading">审核进度</div>';
        $progress .= '<ul class="list-group">';
        foreach ($this->application->audits as $audit) {
            $content = '';
            $options = ['class'=>'list-group-item'];
            switch($this->application->status) {
                case Audit::STATUS_UNAUDITED:
                    $content = '等待 '.$audit->adminuser->nickname.' 审核';
                    break;
                case Audit::STATUS_FAILED:
                    $content = $audit->adminuser->nickname.' 不同意';
                    Html::addCssClass($options, 'list-group-item-danger');
                    $remark = $audit->remark;
                    break;
                case Audit::STATUS_PASS:
                    $content = $audit->adminuser->nickname.' 已同意';
                    Html::addCssClass($options, 'list-group-item-success');
                    break;
            }

            $progress .= Html::tag('li', $content, $options);
        }
        $progress .= '</ul>';

        // 审核不通过理由框
        if (isset($remark)) {
            $progress .= '<div class="panel-body"><p>备注: ';
            $progress .= $remark;
            $progress .= '</p></div>';
        }

        $progress.='</div>';

        // 撤销申请按钮
        $button = null;
        if ($this->application->status == Audit::STATUS_UNAUDITED) {
            $button = Html::a('撤销申请', ['delete', 'id' => $this->application->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '你确定要撤销该申请吗?',
                    'method' => 'post',
                ],
            ]);
            $button = '<p>'.$button.'</p>';
        }

        return $table.$progress.$button;
    }

}
