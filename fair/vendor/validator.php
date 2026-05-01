<?php
namespace validator;

/**
 * 验证器类
 * 用于验证 POST、GET、REQUEST 等数据
 *
 * 使用示例:
 * $input = ['username' => 'test', 'email' => 'test@test.com'];
 * $rules = ['username' => 'required|min:3', 'email' => 'required|email'];
 * $labels = ['username' => '用户名', 'email' => '邮箱'];
 * validator::make($input, $rules, $labels, []);
 * if (validator::fails()) {
 *     $errors = validator::errors();
 * }
 */
class validator
{
    /**
     * 错误信息数组
     * @var array
     */
    public static $errors = array();

    /**
     * 执行验证
     * @param array $input 要验证的数据
     * @param array $rules 验证规则
     * @param array $labels 字段标签
     * @param array $messages 自定义错误消息
     */
    public static function make($input, $rules, $labels, $messages)
    {
        $newRules = self::resolveRules($rules);

        foreach ($newRules as $key => $value) {
            if (isset($labels[$value['name']])) {
                $newRules[$key]['attribute'] = $labels[$value['name']];
            }
        }

        foreach ($newRules as $item) {
            foreach ($item['rule'] as $rule => $row) {
                $classValidator = 'validator'.ucfirst($rule);

                $validatorPath = dirname(__FILE__).'/template/'.$classValidator.'.php';
                if (!file_exists($validatorPath)) {
                    die("不存在的验证文件");
                }
                $namespace = 'validator\template\\'.$classValidator;
                if (class_exists($namespace)) {
                    $name = $item['name'];
                    $attribute = $item['attribute'];
                    $param = $row;
                    $msgName = $name.'.'.$rule;
                    $msg = isset($messages[$msgName]) ? $messages[$msgName] : '';

                    $validator = new $namespace($name, $input[$name], $attribute, $param, $msg);
                    $result = $validator->run();
                    if ($result) {
                        self::$errors[$name][] = $result;
                    }
                } else {
                    die("不存在的验证类");
                }
            }
        }
    }

    /**
     * 获取所有错误消息
     * @return array|bool 错误数组或 false
     */
    public static function errors()
    {
        if (self::$errors) {
            return self::$errors;
        }
        return false;
    }

    /**
     * 获取单个字段的错误消息
     * @param string $name 字段名
     * @return array|bool 错误数组或 false
     */
    public static function get($name)
    {
        if (isset(self::$errors[$name])) {
            return self::$errors[$name];
        }
        return false;
    }

    /**
     * 获取第一个错误消息
     * @return string|bool 错误消息或 false
     */
    public static function first()
    {
        if (self::$errors) {
            $firstError = reset(self::$errors);
            return $firstError[0];
        }
        return false;
    }

    /**
     * 判断某个字段是否有错误
     * @param string $name 字段名
     * @return bool 是否有错误
     */
    public static function has($name)
    {
        if (isset(self::$errors[$name])) {
            return true;
        }
        return false;
    }

    /**
     * 判断验证是否失败
     * @return bool 是否失败
     */
    public static function fails()
    {
        if (self::$errors) {
            return true;
        }
        return false;
    }

    /**
     * 解析验证规则
     * @param array $rules 规则数组
     * @return array 解析后的规则
     */
    private static function resolveRules($rules)
    {
        $newRules = array();
        foreach ($rules as $name => $item) {
            $param = array();
            $ruleArry = explode('|', $item);

            foreach ($ruleArry as $row) {
                $newRow = explode(';', $row);
                if (count($newRow) > 1) {
                    $top = array_shift($newRow);
                    $param[$top] = array();
                    foreach ($newRow as $value) {
                        $keyValue = explode('=', $value);
                        if (empty($keyValue[0]) || empty($keyValue[1])) {
                            die('参数错误');
                        }
                        $param[$top][$keyValue[0]] = $keyValue[1];
                    }
                } else {
                    $param[$row] = array();
                }
            }

            $newRules[] = array(
                'name' => $name,
                'rule' => $param
            );
        }

        return $newRules;
    }
}
