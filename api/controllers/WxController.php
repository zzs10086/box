<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 17:28
 */
namespace api\controllers;
use yii\web\Controller;
use Yii;
use common\models\ByUser as User;
use common\models\ByOrder as Order;
use common\models\ByMoney as Money;
use common\models\UserShare;


class WxController extends BaseController
{
    private $site;

    public function beforeAction($action) {

        $currentaction = $action->id;

        $novalidactions = ['notice'];

        if(in_array($currentaction,$novalidactions)) {

            $action->controller->enableCsrfValidation = false;
        }
        parent::beforeAction($action);

        return true;
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        //初始化appid key
        $this->site = Yii::$app->request->get('site', 1);
        Yii::$app->wepay->initParams($this->site);
    }

    public function actionUserinfo(){
        $params = Yii::$app->request->get();
        $openid = $params['openid'];
        $user = User::find()->where('openid=:openid',[':openid'=>$openid])->one();
        if($user){
            $user->wx_name = $params['wx_name'];
            $user->head_img = $params['head_img'];
            $user->is_auth_userinfo = 1;
            $user->save();
        }
    }





    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        $result = Yii::$app->wepay->getOpenid($code);
        $data = [];
        if($result && isset($result->openid)){
            $openid = $result->openid;
            $session_key = $result->session_key;
            $user = User::find()->where('openid=:openid',[':openid'=>$openid])->one();
            if(!$user){
                $user = new User();
                $user->openid = $openid;
                $user->site = $this->site;
                $user->save(false);
            }
            $data = $user->getAttributes();
            /*Yii::$app->cache->set('login_'.$openid,[
                'openid'=>$openid,
                'session_key'=>$session_key
            ],7000);*/
        }
        self::output($data);
    }


    public function actionPay(){
        $params = Yii::$app->request->get();
        $shop_id = $params['shop_id'];
        $openid = $params['openid'];
        //$total_fee = $params['total_fee'];
        $money = Money::findOne($shop_id);
        $user = User::find()->where('openid=:openid',[':openid'=>$openid])->one();
        if($money && $user /*&& ($total_fee*100) == ($money->money*100)*/){
            $order = new Order();
            $order->shop_id = $money->id;
            $order->status = 1;
            $order->time = time();
            $order->month = $money->month;
            $order->user_id = $user->id;
            $order->openid = $user->openid;
            $order->site = $user->site;
            $order->cash = $money->money;
            $orderSn = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);;
            $order->number = $orderSn;
            if($order->save()){
                $return=Yii::$app->wepay->pay($orderSn,$money->money,$openid);
                //$return=Yii::$app->wepay->pay($orderSn,0.1,$openid);
                
                self::output($return);
            }

        }else{
            self::output([]);
        }
    }

    public function actionNotice(){
        $postXml = $_REQUEST;
        if($postXml==null) {
            $postXml = file_get_contents("php://input");
            if ($postXml == null) {
                $postXml = $GLOBALS['HTTP_RAW_POST_DATA'];
            }
            if (empty($postXml)) {
                return false;
            }
        }
        $attr = Yii::$app->wepay->xmlToArray($postXml);
        
        $total_fee = $attr['total_fee'];
        $open_id = $attr['openid'];
        $out_trade_no = $attr['out_trade_no'];
        $time = $attr['time_end'];
        $transaction_id = $attr['transaction_id'];

        $tr = Yii::$app->db->beginTransaction();
        try {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $order = Order::findBySql("select * from {{%order}} where status = 1 and number=:number for update",[
                     ':number'=>$out_trade_no
                ])->one();
                if($order){
                    $user = User::findBySql("select * from {{%user}} where id = :id for update",[
                         ':id'=>$order->user_id
                    ])->one();
                    $order->status = 2;
                    $order->transaction_id = $transaction_id;
                    if($order->save()){
                        if($user->is_forever == 2){
                            $expire_time = $user->expire_time;
                            $month = $order->month;
                            if($month == 0){
                                $user->is_forever = 1;
                                $user->expire_time = '0000-00-00';
                            }else{
                                if($expire_time == '0000-00-00' || strtotime($expire_time) < time()){
                                    $time = date('Y-m-d');
                                }else{
                                    $time = $expire_time;
                                }
                                $interval = \DateInterval::createFromDateString($month.' month');
                                $datetime = new \DateTime($time);
                                $datetime->add($interval);
                                $user->expire_time = $datetime->format('Y-m-d');
                            }
                            $user->save();
                        }
                    }

                    //如果是分享后优惠支付shop_id =5，则重置share表里的status
                    if($order->shop_id == 5){
                        UserShare::updateAll(array('status'=>1),'openid=:openid',array(':openid'=>$open_id));
                    }

                }
                $tr->commit();
                $result = [
                    'return_code'=>'SUCCESS',
                    'return_msg'=>'OK',
                ];
                echo Yii::$app->wepay->arrayToXml($result);

            } catch (Exception $e) {
                //回滚
                $tr->rollBack();
            }
            $tr->commit();

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();

        }
        Yii::error($attr);
    }



}