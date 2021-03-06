<?php

namespace Jncinet\LaravelEnvEditor;

use Qihucms\Support\Str;

class EnvEditor
{
    protected $env;

    public function __construct($path)
    {
        $path = $path ?: base_path('.env');

        $this->env = file_get_contents($path);
    }

    /**
     * Get .env variable.
     *
     * @param null $key
     * @return array|mixed
     */
    public function getEnv($key = null)
    {
        $string = preg_split('/\n+/', $this->env);
        $string = array_filter($string);
        $array = [];
        foreach ($string as $k => $one) {
            if (preg_match('/^(#\s)/', $one) === 1 || preg_match('/^([\\n\\r]+)/', $one)) {
                continue;
            }
            $entry = explode("=", $one, 2);
            if (!empty($entry[0])) {
                $array[$entry[0]] = isset($entry[1]) ? $entry[1] : null;
                // 不转引号内的中文字符、key='APP_KEY'
                if (
                    $entry[0] != 'APP_KEY'
                    && !empty($array[$entry[0]])
                    && strpos($array[$entry[0]], '"') != 0
                    && strpos($array[$entry[0]], '"', -0) != 0
                    && Str::isEncoding($array[$entry[0]])
                ) {
                    $array[$entry[0]] = Str::decode($array[$entry[0]]);
                }
            }
        }
        if (empty($key)) {
            return $array;
        }
        return $array[$key];
    }

    /**
     * 合并数组更新
     *
     * @param array $data ['key' => 'value']
     * @return bool
     */
    public function setEnv(array $data)
    {
        // 读取当前数据
        $array = $this->getEnv();
        // 键名转为大写
        $data = array_change_key_case($data, CASE_UPPER);
        // 合并新老数据
        $array = array_merge($array, $data);
        // 保存
        return $this->saveEnv($array);
    }

    /**
     * Save .env variable.
     *
     * @param $array
     * @return bool
     */
    public function saveEnv($array)
    {
        if (is_array($array)) {
            $newArray = [];
            $i = 0;
            foreach ($array as $key => $env) {
                if (preg_match('/\s/', $env) > 0 && (strpos($env, '"') > 0 && strpos($env, '"', -0) > 0)) {
                    $env = '"' . $env . '"';
                } elseif (Str::includingChinese($env)) {
                    // 对中文进行base64编码
                    $env = Str::encode($env);
                }
                $newArray[$i] = $key . '=' . $env;
                $i++;
            }
            $newArray = implode("\n", $newArray);
            file_put_contents(base_path('.env'), $newArray);
            return true;
        }
        return false;
    }
}