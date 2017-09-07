<?php
namespace common\widgets;

use common\models\Activity;
use common\models\Application;
use common\models\Course;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * 课程表格小部件
 *
 * @property Application[] $applications
 * @property Activity[] $activities
 * @property Course[] $courses
 * @property boolean $single 是否合并单元格, 默认不合并
 * @property array $tbody
 */
class CoursesWidget extends Widget
{
    public $applications = [];
    public $activities = [];
    public $courses = [];
    public $single = false;

    protected $tbody;

    /**
     * 数据初始化
     */
    public function init()
    {
        parent::init();
        // 构建12*7空表格
        for ($i=1;$i<=12;$i++) {
            for ($j=0;$j<=7;$j++) {
                if ($j==0) {
                    $this->tbody[$i][$j] = Html::tag('th', $i, [
                        'style'=>[
                            'vertical-align'=>'middle',
                            'text-align'=>'center',
                        ]
                    ]);
                } else {
                    $this->tbody[$i][$j] = Html::tag('td', '', [
                        'data-day'=>$j,
                        'data-sec'=>$i,
                    ]);
                }
            }
        }
    }

    /**
     * 逻辑处理
     * @return string 视图代码
     */
    public function run()
    {
        $thead = <<<EOT
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <colgroup span="1"><col/></colgroup>
                <colgroup span="7">
                    <col width="14%" />
                    <col width="14%" />
                    <col width="14%" />
                    <col width="14%" />
                    <col width="14%" />
                    <col width="14%" />
                    <col width="14%" />
                </colgroup>
                
                <tr class="text-center">
                    <th class="text-center">#</th>
                    <th class="text-center">周一</th>
                    <th class="text-center">周二</th>
                    <th class="text-center">周三</th>
                    <th class="text-center">周四</th>
                    <th class="text-center">周五</th>
                    <th class="text-center">周六</th>
                    <th class="text-center">周日</th>
                </tr>
            </thead>
EOT;

        // 渲染Application表数据
        if ($this->single) {
            foreach ($this->applications as $application) {
                // 格式化sec属性, 分组
                if (!empty($application->adjust_sec)) {
                    $application->adjust_sec = $this->filterSec($application->adjust_sec);
                    // 遍历
                    foreach ($application->adjust_sec as $sec) {
                        // $sec 节的具体数组单元
                        foreach ($sec as $key => $value) {
                            // 填充课程对应单元格的内容(不合并)
                            $this->tbody[$value][$application->adjust_day] = Html::tag('td', '#', [
                                'style' => [
                                    'vertical-align' => 'middle',
                                    'text-align'=>'center',
                                ],
                                'class' => 'danger',
//                                'data-day' => $application->adjust_day,
//                                'data-sec' => $value,
                            ]);
                        }
                    }
                }
            }
        }

        // 渲染Activity表数据
        foreach ($this->activities as $activity) {
            // 格式化sec属性, 分组
            if (!empty($activity->sec)) {
                $activity->sec = $this->filterSec($activity->sec);
                // 遍历
                foreach ($activity->sec as $sec) {
                    // $sec 节的具体数组单元
                    foreach ($sec as $key => $value) {
                        if ($this->single) {
                            // 填充课程对应单元格的内容(不合并)
                            $this->tbody[$value][$activity->day] = Html::tag('td', '#', [
                                'style' => [
                                    'vertical-align' => 'middle',
                                    'text-align'=>'center',
                                ],
                                'class' => 'danger',
//                                'data-day' => $activity->day,
//                                'data-sec' => $value,
                            ]);
                        } else {
                            // 填充课程对应单元格的内容(合并)
                            if ($key == 0) {
                                $counselor = $activity->adminuser->getAttribute('nickname');
                                $classroom = $activity->classroom->getAttribute('name');
                                $content = $activity->name . '<br />@' . $classroom . ' @' . $counselor;
                                $this->tbody[$value][$activity->day] = Html::tag('td', $content, [
                                    'style' => ['vertical-align' => 'middle'],
                                    'class' => 'warning',
//                                    'data-id' => $activity->id,
//                                    'data-day' => $activity->day,
//                                    'data-sec' => $value,
                                    'rowspan' => count($sec),
                                ]);
                            } else {
                                // 删除rowspan后多出tr标签
                                $this->tbody[$value][$activity->day] = null;
                            }
                        }
                    }
                }
            }
        }

        // 渲染Course表数据
        foreach ($this->courses as $course) {
            // 格式化sec属性, 分组
            if (!empty($course->sec)) {
                $course->sec = $this->filterSec($course->sec);
                // 遍历
                foreach ($course->sec as $sec) {
                    // $sec 节的具体数组单元
                    foreach ($sec as $key => $value) {
                        if ($this->single) {
                            // 填充课程对应单元格的内容(不合并)
                            $this->tbody[$value][$course->day] = Html::tag('td', '#', [
                                'style' => [
                                    'vertical-align' => 'middle',
                                    'text-align'=>'center',
                                ],
                                'class' => 'danger',
                                'data-day' => $course->day,
                                'data-sec' => $value,
                            ]);
                        } else {
                            // 填充课程对应单元格的内容(合并)
                            if ($key == 0) {
                                $teacher = $course->user->getAttribute('nickname');
                                $classroom = $course->classroom->getAttribute('name');
                                $content = $course->name . '<br />@' . $classroom . ' @' . $teacher;
                                $this->tbody[$value][$course->day] = Html::tag('td', $content, [
                                    'style' => ['vertical-align' => 'middle'],
                                    'class' => 'info',
                                    'data-id' => $course->id,
                                    'data-day' => $course->day,
                                    'data-sec' => $value,
                                    'rowspan' => count($sec),
                                ]);
                            } else {
                                // 删除rowspan后多出tr标签
                                $this->tbody[$value][$course->day] = null;
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->tbody as $k => $td) {
            $this->tbody[$k] = implode('', $td);
        }

        $tbody = '<tbody><tr>'.implode('</tr><tr>', $this->tbody).'</tr></tbody>';
        $table = $thead.$tbody.'</table></div>';

        return $table;
    }

    /**
     * 处理sec属性, 连续的节数为一个数组元素且对应的键为起始节
     * @param $sec
     * @return array
     */
    private function filterSec($sec)
    {
        $sections = explode(',', $sec);
        $result = array();
        $i = $j = 0;
        foreach ($sections as $section) {
            if ($section == ++$j) {
                $result[$i][] = $section;
            } else {
                $result[$section][] = $section;
                $i = $j = $section;
            }
        }
        return $result;
    }
}
