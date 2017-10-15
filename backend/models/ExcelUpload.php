<?php
namespace backend\models;

use common\models\Adminuser;
use common\models\Classes;
use common\models\Classroom;
use common\models\Course;
use common\models\CourseRelationship;
use common\models\User;
use PHPExcel_IOFactory;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Excel文件上传模型类
 *
 * @property UploadedFile $teacher
 * @property UploadedFile $classroom
 * @property UploadedFile $adminuser
 * @property UploadedFile $student
 * @property UploadedFile $course
 * @property UploadedFile $admin
 */
class ExcelUpload extends Model
{
    public $teacher;
    public $classroom;
    public $adminuser;
    public $student;
    public $course;
    public $admin;

    /**
     * 属性验证规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['teacher', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
            ['classroom', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
            ['adminuser', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
            ['student', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
            ['course', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
            ['admin', 'file', 'skipOnEmpty' => true, 'extensions' => ['xls','xlsx'], 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'teacher' => '教师名单',
            'classroom' => '教室列表',
            'adminuser' => '辅导员&班级',
            'student' => '学生名单',
            'course' => '课程列表',
            'admin' => '管理员名单',
        ];
    }

    /**
     * 导入教师用户数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     */
    public function importTeacherUsers($file)
    {
        // 数据列配置
        $username_col = 'A';    // 教师工号
        $nickname_col = 'B';    // 教师姓名

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        $user = new User();
        $data = [];
        $errorRows = [];
        $successNum = 0;
        // 生成值为 123456 对应的哈希密码
        $password_hash = Yii::$app->security->generatePasswordHash('123456');
        for ($row = 2; $row <= $highestRow; $row++) {
            // 数据赋值
            $user->username = $objWorksheet->getCell($username_col . $row)->getValue();
            $user->nickname = $objWorksheet->getCell($nickname_col . $row)->getValue();
            $user->class_id = User::TEACHER_CLASS;
            $user->password_hash = $password_hash;
            $user->auth_key = Yii::$app->security->generateRandomString();

            // 验证数据
            if ($user->validate()) {
                $data[] = $user->toArray();
                $successNum++;
            } else {
                // 验证不通过的行的数组集
                $errorRows[] = $row;
            }
        }

        // 批量写入数据库
        if (!empty($data)) {
            Yii::$app->db->createCommand()->batchInsert(User::tableName(), [
                'username',
                'nickname',
                'class_id',
                'password_hash',
                'auth_key',
            ], $data)->execute();
        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入教室数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     */
    public function importClassrooms($file)
    {
        // 数据列配置
        $number_col = 'A';  // 教室代码
        $name_col = 'B';    // 教室名称
        $type_col = 'C';    // 教室类型
        $amount_col = 'D';  // 容纳人数

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        $classroom = new Classroom();
        $data = [];
        $errorRows = [];
        $successNum = 0;
        for ($row = 2; $row <= $highestRow; $row++) {

            // 教室类型字符串标识 => 数字标识
            $typeStr = trim($objWorksheet->getCell($type_col . $row)->getValue());
            if ($typeStr == '计算机房' || $typeStr == '实训室') {
                $type = Classroom::TYPE_SPECIAL;
            } else {
                $type = Classroom::TYPE_ORDINARY;
            }

            // 数据赋值
            $classroom->number = $objWorksheet->getCell($number_col . $row)->getValue();
            $classroom->name = $objWorksheet->getCell($name_col . $row)->getValue();
            $classroom->type = $type;
            $classroom->amount = $objWorksheet->getCell($amount_col . $row)->getValue();

            // 验证数据
            if ($classroom->validate()) {
                $data[] = $classroom->toArray();
                $successNum++;
            } else {
                // 验证不通过的行的数组集
                $errorRows[] = $row;
            }
        }

        // 批量写入数据库
        if (!empty($data)) {
            Yii::$app->db->createCommand()->batchInsert(Classroom::tableName(), [
                'number',
                'name',
                'type',
                'amount',
            ], $data)->execute();
        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入辅导员与班级数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     * @throws \Exception
     */
    public function importAdminusers($file)
    {
        // 数据列配置
        $username_col = 'A';    // 辅导员工号
        $nickname_col = 'B';    // 辅导员姓名
        $class_name_col = 'C';  // 班级名称

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        // 遍历excel数据
        $errorRows = [];
        $successNum = 0;
        // 生成值为 123456 对应的哈希密码
        $password_hash = Yii::$app->security->generatePasswordHash('123456');
        for ($row = 2; $row <= $highestRow; $row++) {

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 辅导员数据赋值
                $adminuser = new Adminuser();
                $adminuser->username = $objWorksheet->getCell($username_col . $row)->getValue();
                $adminuser->nickname = $objWorksheet->getCell($nickname_col . $row)->getValue();
                $adminuser->role = Adminuser::COUNSELOR;
                $adminuser->password_hash = $password_hash;
                $adminuser->auth_key = Yii::$app->security->generateRandomString();

                // 新增辅导员成功
                if ($adminuser->save()) {
                    // 将班级单元格数据分组
                    $classes_group = explode('，', $objWorksheet->getCell($class_name_col . $row)->getValue());
                    foreach ($classes_group as $classes) {
                        // 继续拆分成单个班级
                        $classes = explode('、', $classes);
                        foreach ($classes as $k => $class) {
                            $class = trim($class);
                            // 新增一个班级
                            $model = new Classes();
                            $model->number = '1';
                            $model->adminuser_id = $adminuser->getAttribute('id');
                            if ($k === 0) {
                                $model->name = $class;
                            } else {
                                $model->name = strstr($classes[0],'-',true).'-'.$class;
                            }
                            if (!$model->save()) {
                                // 抛出异常实现回滚
                                throw new \Exception('保存班级数据失败~');
                            }
                        }
                    }
                } else {
                    // 抛出异常实现回滚
                    throw new \Exception('保存辅导员数据失败~');
                }
                // 提交事务
                $successNum++;
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollback();
                $errorRows[] = $row;
            }

        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入在校生数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     */
    public function importStudents($file)
    {
        // 数据列配置
        $username_col = 'A';    // 学生工号
        $nickname_col = 'B';    // 学生姓名
        $class_col = 'C';       // 所在班级

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        $user = new User();
        $data = [];
        $errorRows = [];
        $successNum = 0;
        // 生成值为 123456 对应的哈希密码
        $password_hash = Yii::$app->security->generatePasswordHash('123456');
        for ($row = 2; $row <= $highestRow; $row++) {

            // 查询对应班级记录
            $class = Classes::findOne([
                'name' => trim($objWorksheet->getCell($class_col . $row)->getValue())
            ]);

            if (!empty($class)) {
                // 数据赋值
                $user->username = $objWorksheet->getCell($username_col . $row)->getValue();
                $user->nickname = $objWorksheet->getCell($nickname_col . $row)->getValue();
                $user->class_id = $class->id;
                $user->password_hash = $password_hash;
                $user->auth_key = Yii::$app->security->generateRandomString();

                // 验证数据
                if ($user->validate()) {
                    $data[] = $user->toArray();
                    $successNum++;
                } else {
                    // 验证不通过的行的数组集
                    $errorRows[] = $row;
                }
            } else {
                // 验证不通过的行的数组集
                $errorRows[] = $row;
            }

        }

        // 批量写入数据库
        if (!empty($data)) {
            Yii::$app->db->createCommand()->batchInsert(User::tableName(), [
                'username',
                'nickname',
                'class_id',
                'password_hash',
                'auth_key',
            ], $data)->execute();
        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入课程数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     */
    public function importCourses($file)
    {
        // 数据列配置
        $course_number_col = 'A';   // 课程序号
        $course_name_col = 'B';     // 课程名称
        $class_name_col = 'C';      // 教学班级名称
        $username_col = 'D';       // 教师工号
        $course_col = 'E';         // 排课信息

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        $errorRows = [];
        $successNum = 0;
        for ($row = 2; $row <= $highestRow; $row++) {

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 取单元格数据
                $course_number_data = $objWorksheet->getCell($course_number_col . $row)->getValue();
                $course_name_data = $objWorksheet->getCell($course_name_col . $row)->getValue();
                $class_name_data = $objWorksheet->getCell($class_name_col . $row)->getValue();
                $username_data = $objWorksheet->getCell($username_col . $row)->getValue();
                $course_info_data = $objWorksheet->getCell($course_col . $row)->getValue();

                // 查询对应教室记录
                $user = User::findOne([
                    'username' => trim($username_data)
                ]);
                if (empty($user)) {
                    throw new \Exception('不存在此教师~');
                }

                // 处理排课信息单元格数据
                $course_infos = explode('<br>', $course_info_data);
                foreach ($course_infos as $course_info) {
                    // 格式化排课信息数据
                    $course_info = $this->formatData($course_info);
                    // 课程数据赋值
                    $course = new Course();
                    $course->number = $course_number_data;
                    $course->name = $course_name_data;
                    $course->user_id = $user->id;
                    $course->week = $course_info['week'];
                    $course->day = $course_info['day'];
                    $course->sec = $course_info['sec'];
                    $course->classroom_id = $course_info[4];
                    // 新增课程记录成功
                    if ($course->save()) {
                        // 如果班级列不为空
                        if (!empty(trim($class_name_data))) {
                            // 新增课程班级关联记录
                            foreach (explode(' ', trim($class_name_data)) as $value) {
                                // 查询对应班级记录
                                $class = Classes::findOne(['name' => $value]);
                                if (empty($class)) {
                                    throw new \Exception('不存在此班级~');
                                }
                                // 新增关联记录
                                $relation = new CourseRelationship();
                                $relation->class_id = $class->id;
                                $relation->course_id = $course->getAttribute('id');
                                if (!$relation->save()) {
                                    throw new \Exception('保存课程班级关联记录失败~');
                                }
                            }
                        }
                    } else {
                        throw new \Exception('保存课程记录失败~');
                    }

                }

                // 提交事务
                $successNum++;
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollback();
                $errorRows[] = $row;
            }
        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入管理员用户数据
     * @param string $file 导入文件路径
     * @return array 导入结果
     */
    public function importAdmin($file)
    {
        // 数据列配置
        $username_col = 'A';    // 管理员工号
        $nickname_col = 'B';    // 管理员姓名
        $role_col = 'C';        // 管理员职位

        // 导入Excel文件
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        // 获取第一张表对象
        $objWorksheet = $objPHPExcel->getSheet(0);
        // 获取最大行数
        $highestRow = $objWorksheet->getHighestRow();

        $adminuser = new Adminuser();
        $data = [];
        $errorRows = [];
        $successNum = 0;
        // 生成值为 123456 对应的哈希密码
        $password_hash = Yii::$app->security->generatePasswordHash('123456');
        for ($row = 2; $row <= $highestRow; $row++) {
            $role = [
                Adminuser::BOSS => '院长',
                Adminuser::DEAN => '教学副院长',
                Adminuser::OFFICE => '院办',
                Adminuser::DIRECTOR => '系主任',
                Adminuser::LABORATORY => '实验中心副主任',
            ];

            // 数据赋值
            $adminuser->username = $objWorksheet->getCell($username_col . $row)->getValue();
            $adminuser->nickname = $objWorksheet->getCell($nickname_col . $row)->getValue();
            $adminuser->role = array_search(trim($objWorksheet->getCell($role_col . $row)->getValue()), $role);
            $adminuser->password_hash = $password_hash;
            $adminuser->auth_key = Yii::$app->security->generateRandomString();

            // 验证数据
            if ($adminuser->validate()) {
                $data[] = $adminuser->toArray();
                $successNum++;
            } else {
                // 验证不通过的行的数组集
                $errorRows[] = $row;
            }
        }

        // 批量写入数据库
        if (!empty($data)) {
            Yii::$app->db->createCommand()->batchInsert(Adminuser::tableName(), [
                'username',
                'nickname',
                'role',
                'password_hash',
                'auth_key',
                'email',
            ], $data)->execute();
        }

        return $this->message($successNum, $errorRows);

    }

    /**
     * 导入结果提示消息
     * @param int $successRows 成功导入总条数
     * @param array $errorRows 错误行的数组集
     * @return array
     */
    protected function message($successRows = 0, $errorRows = []) {
        $errorMsg = empty($errorRows) ? '' : '第 ' . implode('，', $errorRows) . ' 行记录导入失败~请重试~';
        return [
            'success' => '成功导入 ' . $successRows . ' 条记录！',
            'error' => $errorMsg,
        ];
    }

    /**
     * 格式化排课信息数据
     * @param string $data
     * @return array
     */
    private function formatData($data) {
        $arr = explode(' ', $data);

        // 处理day
        $day = ['','星期一','星期二','星期三','星期四','星期五','星期六','星期天'];
        $arr['day'] = array_search($arr[1], $day,true);

        // 处理sec
        $sec = explode('-', $arr[2]);
        if (empty($sec[1])) {
            $arr['sec'] = (int)$sec[0];
        } else {
            for ($i=(int)$sec[0];$i<=$sec[1];$i++) {
                $arr['sec'][] = $i;
            }
        }

        // 处理week
        preg_match('/(.*)\[(\d+)-?(\d*)\]/', trim($arr[3]), $match);
        $arr['week'] = [];
        if (empty($match[3])) {
            $arr['week'] = (int)$match[2];
        } elseif (empty($match[1])) {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                $arr['week'][] = $j;
            }
        } elseif ($match[1] == '单') {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                if ($j%2==1) {
                    $arr['week'][] = $j;
                }
            }
        } elseif ($match[1] == '双') {
            for ($j=(int)$match[2];$j<=(int)$match[3];$j++) {
                if ($j%2==0) {
                    $arr['week'][] = $j;
                }
            }
        }

        return $arr;
    }

}