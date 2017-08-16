<?php
namespace common\widgets;

use common\models\Application;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * 多按钮小部件
 * @property Application $application
 */
class ApplyDetailWidget extends Widget
{
    public $application;

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
        if ($this->application->type == Application::TYPE_ADJUST) {
            $table = <<<EOT
            <div class="table-responsive">
                <table class="table table-bordered">
                    
                    <thead>
                        <th colspan='3'>调课</th>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <th>课程</th>
                            <td colspan='2'></td>
                        </tr>                        
                        <tr>
                            <th>授课班级</th>
                            <td colspan='2'></td>
                        </tr>                        
                        <tr>
                            <th>#</th>
                            <th>调整前</th>
                            <th>调整前</th>
                        </tr>                        
                        <tr>
                            <th>教师</th>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>时间</th>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>地点</th>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>事由</th>
                            <td colspan='2'></td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td colspan='2'></td>
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
                        <th colspan='2'>停课</th>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <th>申请人</th>
                            <td></td>
                        </tr> 
                        <tr>
                            <th>课程</th>
                            <td></td>
                        </tr>                        
                        <tr>
                            <th>停课班级</th>
                            <td></td>
                        </tr>                                           
                        <tr>
                            <th>停课时间</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>停课地点</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>事由</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>申请时间</th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
EOT;
        } elseif ($this->application->type == Application::TYPE_SCHEDULE) {
            $table = null;
        } else {
            $table = null;
        }
        return $table;
    }

}
