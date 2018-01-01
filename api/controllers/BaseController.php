<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 17:29
 */
namespace api\controllers;
use Yii;
use yii\web\Controller;

class BaseController extends Controller{

    const WECHATKEY = 'zzs594ba@*2f5fH9zzs';  //小程序 KEY

    const LOGS_PATH = '../runtime/apiLog';

    const DS = DIRECTORY_SEPARATOR;


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        //对小程序进行简单的加密,正式环境需要token对比
        if(self::isWX()){
            //if (YII_ENV == 'dev') {
                if(self::debugWechatEncode(self::WECHATKEY)==false){
                    echo "token校验错误";
                    Yii::$app->end();
                }
            //}
        }

    }

    /**
     * 判断是否是微信小程序
     * @return bool
     */
    public static function isWX()
    {
        return isset($_GET['type']) && $_GET['type'] == 'json';
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && (isset($_GET['type']) && $_GET['type'] == 'json');
    }


    /**
     * 微信小程序加密验证
     * @param $salt
     * @return bool
     */
    protected static function debugWechatEncode($salt){
        $token = Yii::$app->request->get('token_wechat');
        $time = Yii::$app->request->get('time_wechat');
        $rand = Yii::$app->request->get('rand_wechat');
        if(!$token || !$time || !$rand){
            return false;
        }
        $str = md5($time.$rand.$salt);
        return $token == $str;
    }

    //错误代码对应的信息
    private static $code = array
    (
        '10000' => '头部信息不全',
        '10001' => '请填写完整信息',
        '10002' => '您已收藏',
        '10003' => '收藏成功',
        '10004' => '参数不全',
        '10005' => '请登陆',
        '10006' => '您已点赞',
        '10007' => '点赞成功',
        '10008' => '暂无支付价格',
        '10009' => '取消收藏失败',
        '10010' => '您已分享成功',
    );

    /**
     * 获取代码对应的错误信息
     * @param $key
     * @return bool|mixed
     */
    protected static function getCodeMsg($key)
    {

        if (!array_key_exists($key, self::$code)) {
            return 'ok';
        } else {
            return self::$code[$key];
        }
    }

    //打印日志
    public static function debug_log($msg,$level='')
    {

        //$logdir= self::LOGS_PATH?self::LOGS_PATH:'/runtime/tmp';

        $logPath = dirname(dirname(__FILE__)) . '/runtime/apiLog';
        $logdir= $logPath?$logPath:'/runtime/tmp';

        $level=strtolower($level);
        if($level=='' || $level=='debug')
        {
            $f='debug';
        }
        else
        {
            $f='debug_'.$level;
        }
        $flag=file_put_contents(rtrim($logdir,self::DS).self::DS.$f.'.'.date('Ymd'),(is_array($msg)?print_r($msg,true):$msg)."\t".date('Y-m-d H:i:s')."\n",FILE_APPEND);
        if($flag===false)
        {
            if(!file_exists($logdir) || !is_dir($logdir))
            {
                mkdir(LOGS_PATH,0777,true);
            }
            file_put_contents(rtrim($logdir,self::DS).self::DS.$f.'.'.date('Ymd'),(is_array($msg)?print_r($msg,true):$msg)."\t".date('Y-m-d H:i:s')."\n",FILE_APPEND);
        }
    }
    /**
     *
     * 返回数据
     * @param null $data
     * @param int $code
     * @param string $msg
     */
    protected static function output($data = null, $code = 0, $msg = '')
    {

        $msg = ($msg ? $msg : ($code ? self::getCodeMsg($code) : 'ok'));
        if (!is_null($data)) {
            $cnt = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data));
        } else {
            $cnt = json_encode(array('code' => $code, 'msg' => $msg, 'data' => []));
        }

        header("Content-type: application/json");
        echo $cnt;

        Yii::$app->end();
    }
}