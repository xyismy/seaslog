<?php

namespace src;

use Seaslog;

class SeasLogger
{
    protected $appName;
    protected $logPath;
    protected $logDebugPath;

    //配置检查
    public function __invoke()
    {
        if( empty(getenv('APP_NAME')) ){
            throw new \SeasLogException('APP_NAME config error');
        }

        if( empty(getenv('LOG_ADDRESS')) ){
            throw new \SeasLogException('LOG_ADDRESS config error');
        }

        if( empty(getenv('LOG_DEBUG_ADDRESS')) ){
            throw new \SeasLogException('LOG_DEBUG_ADDRESS config error');
        }

        $this->configHandle();
    }

    private function configHandle()
    {
        $this->appName = !empty(getenv('APP_NAME')) ? getenv('APP_NAME') : 'default';
        $this->logPath = !empty(getenv('LOG_ADDRESS')) ? getenv('LOG_ADDRESS') : dirname(__DIR__).'/logs/info/';
        $this->logDebugPath = !empty(getenv('LOG_DEBUG_ADDRESS')) ? getenv('LOG_DEBUG_ADDRESS') : dirname(__DIR__).'/logs/debug/';
    }

    /**
     * 获取日志前缀
     * @return string
     */
    public function head()
    {
        return '<166>' . date('Y-m-d H:i:s', time()) . ' ' . gethostname() . ' '.$this->appName . '[' . getmypid() . ']' . ': topic=track.';
    }

    /**
     * 获取日志文件名
     * @param $fileName
     * @param $module
     * @return string
     */
    public function fileName($fileName, $module)
    {
        $fileName = $fileName ? $this->appName . '.' . $module . '.' . $fileName : $filename = $this->appName . '.' . $module;
        return $fileName;
    }

    /**
     * 设置日志路径
     * @param $logPath
     * @param $module
     */
    public function setPath($logPath,$module)
    {
        //设置路径
        Seaslog::setBasePath($logPath);
        //设置模块
        Seaslog::setLogger($module);
    }

    /**
     * 获取当前请求生命周期id，fpm模式使用，cli模式（或常驻内存模式，待测试）
     * @return mixed
     */
    public function getGuid()
    {
        return SeasLog::getRequestID();
    }

    /**
     * 清除资源句柄，释放文件
     */
    public function clearStream()
    {
        SeasLog::closeLoggerStream();
    }

    /**
     * 转换日志格式
     * @param $key
     * @param $level
     * @param $module
     * @param $content
     * @return string
     */
    public function logInfo($key,$level,$module,$content)
    {
        $head = $this->head() . $key . ' ';

        $result = [
            "log_id" => $this->getGuid(),
            "type" => $level,
            "tag" => $this->appName.' '.$key,
            "language" => "php",
            "app" => $this->appName,
            "module" => $module,
            "detail" => ['content' => $content],
        ];

        return ($head . json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * 业务日志接口
     * @param mixed $content 要写的内容string|array
     * @param string $key 关键字，一般是__FUNCTION__
     * @param string $module 模块，一般是Controller
     * @param string $fileName 要定义的文件名，默认日期
     * @param string $level 日志等级，默认info
     * @return boolean
     */
    public function log($content, $key = '', $module = '', $fileName = '', $level = 'info')
    {
        return $this->logPush($content,$this->logPath,$level,$key,$module,$fileName);
    }

    /**
     * debug日志接口
     * @param mixed $content 要写的内容string|array
     * @param string $key 关键字，一般是__FUNCTION__
     * @param string $module 模块，一般是Controller
     * @param string $fileName 要定义的文件名，默认日期
     * @param string $level 日志等级，默认debug
     * @return boolean
     */
    public function debugLog($content, $key = '', $module = '', $fileName = '', $level = 'debug')
    {
        return $this->logPush($content,$this->logDebugPath,$level,$key,$module,$fileName);
    }

    /**
     * 日志落地
     * @param $content
     * @param $logPath
     * @param $level
     * @param string $key
     * @param string $module
     * @param string $fileName
     * @return mixed
     */
    public function logPush($content, $logPath, $level, $key, $module, $fileName)
    {
        $this->setPath($logPath,$module);
        $fileName = $this->fileName($fileName, $module);
        $logContent = $this->logInfo($key,$level,$module,$content);

        return SeasLog::log($fileName, $logContent);
    }
}