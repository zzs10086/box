<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 17:28
 */
namespace http\controllers;
use yii\web\Controller;
use common\models\ByMoney;
use common\models\ByCategory;
use common\models\ByArticle;
use common\models\ByArticlePic;
use common\models\ByUserCollet;
use common\models\ByUser;
use common\models\UserPraise;
use common\models\UserShare;
use yii\data\Pagination;
use Yii;


class IndexController extends BaseController
{

     CONST EXPIRE_TIME = 3600;

     public function actionIndex()
     {

          //轮播
          $foucs =$this->getFoucs(6);
          //今日更新
          $new = $this->getArticleNew(10);
          //性感美女
          $sex = $this->getArticleByCategory(10);
          //丰乳大胸
          $chest = $this->getArticleByCategory(2);
          //美臀
          $meitun = $this->getArticleByCategory(8);
          //美腿
          $siwai = $this->getArticleByCategory(9);
          //制服
          $zhifu = $this->getArticleByCategory(7);
          //气质女神
          $nvshen = $this->getArticleByCategory(5);
          //模特
          $xiezhen = $this->getArticleByCategory(12);
          //模特
          $mote = $this->getArticleByCategory(1);

          return $this->render('index', [
               'sex'=>$sex,
               'chest'=>$chest,
               'meitun'=>$meitun,
               'siwai'=>$siwai,
               'new'=>$new,
               'foucs'=>$foucs,
               'zhifu'=>$zhifu,
               'nvshen'=>$nvshen,
               'xiezhen'=>$xiezhen,
               'mote'=>$mote,
               'current'=>'index'
          ]);
     }

     private function getFoucs($limit){

          $key = "index:foucs";

          $data = Yii::$app->redis->get($key);
          if(!$data){
               $data = ByArticle::find()->select(['id','title','pic_url'])->where(['status' => 1,'is_foucs'=>1])->limit($limit)->asArray()->orderBy('id desc')->all();
               Yii::$app->redis->set($key,$data,self::EXPIRE_TIME);
          }

          return $data;
     }
     /**
      * 性感
      * @return string
      */
     public function actionXinggan()
     {

          $cid = 10;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
               'list'=>$res,
               'title'=>'性感图片_性感美女_性感美女套图 - 喜欢宝贝',
               'keywords'=>'性感美女,性感图片,美女套图',
               'description'=>'性感美女频道提供各类顶级美女机构性感美女图片，如推女郎、秀人网、ROSI等众多性感美女写真及大胆日本美女艺术套图。',
               'category_name'=>'性感',
               'category_url'=>'xinggan',
               'current'=>'xinggan'
          ];

          return $this->render('list', $data);
     }
     /**
      * 丰乳
      * @return string
      */
     public function actionFengru()
     {
          $cid = 2;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'丰乳_大胸_大奶套图 - 喜欢宝贝',
              'keywords'=>'丰乳,大胸,大奶,美女',
              'description'=>'丰乳频道提供各类丰乳,大胸，大奶美女图片。',
              'category_name'=>'丰乳',
              'category_url'=>'fengru',
               'current'=>'fengru'
          ];
          return $this->render('list', $data);
     }
     /**
      * 翘臀
      * @return string
      */
     public function actionQiaotun()
     {
          $cid = 8;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'翘臀_美臀_肥臀套图 - 喜欢宝贝',
              'keywords'=>'翘臀,美臀,肥臀,美女',
              'description'=>'翘臀频道提供各类翘臀,美臀,肥臀美女图片。',
              'category_name'=>'翘臀',
              'category_url'=>'qiaotun',
              'current'=>'qiaotun'
          ];
          return $this->render('list', $data);
     }
     /**
      * 美腿
      * @return string
      */
     public function actionMeitui()
     {
          $cid = 9;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'美腿_丝袜_黑丝_长腿美女套图 - 喜欢宝贝',
              'keywords'=>'美腿,丝袜,黑丝袜,长腿,美女',
              'description'=>'美腿频道提供各类美腿,丝袜,黑丝,长腿美女图片。',
              'category_name'=>'美腿',
              'category_url'=>'meitui',
               'current'=>'meitui'
          ];
          return $this->render('list', $data);
     }

     /**
      * 女神
      * @return string
      */
     public function actionNvshen()
     {
          $cid = 5;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'女神_清纯_漂亮_气质美女套图 - 喜欢宝贝',
              'keywords'=>'女神,清纯,漂亮,气质,美女',
              'description'=>'女神频道提供各类女神,清纯,漂亮,气质美女图片。',
              'category_name'=>'女神',
              'category_url'=>'nvshen',
               'current'=>'nvshen'
          ];
          return $this->render('list', $data);
     }
     /**
      * 制服
      * @return string
      */
     public function actionZhifu()
     {
          $cid = 7;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'制服_女仆_校服_情趣内衣套图 - 喜欢宝贝',
              'keywords'=>'制服,女仆,校服,情趣内衣',
              'description'=>'制服频道提供各类制服,女仆,校服,情趣内衣图片。',
              'category_name'=>'制服',
              'category_url'=>'zhifu',
               'current'=>'zhifu'
          ];

          return $this->render('list', $data);
     }

     /**
      * 写真
      * @return string
      */
     public function actionXiezhen()
     {
          $cid = 12;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
              'list'=>$res,
              'title'=>'写真_旗袍_国模_性感写真套图 - 喜欢宝贝',
              'keywords'=>'写真,旗袍,国模,性感写真',
              'description'=>'写真频道提供各类写真,旗袍,国模,性感写真套图图片。',
              'category_name'=>'写真',
              'category_url'=>'xiezhen',
               'current'=>'xiezhen'
          ];
          return $this->render('list', $data);
     }

     /**
      * mote
      * @return string
      */
     public function actionMote()
     {
          $cid = 1;
          $page = Yii::$app->request->get('page', 1);
          $res = $this->getList($cid,$page);

          $data = [
               'list'=>$res,
               'title'=>'模特_车模_人体模特_漂亮模特 - 喜欢宝贝',
               'keywords'=>'模特,车模,人体模特,漂亮模特',
               'description'=>'写真频道提供各类模特,车模,人体模特,漂亮模特套图图片。',
               'category_name'=>'模特',
               'category_url'=>'mote',
               'current'=>'mote'
          ];
          return $this->render('list', $data);
     }

     public function actionShow()
     {
          $cate = array(
               10=> array('name'=>'性感美女','url'=>'xinggan'),
               2=> array('name'=>'丰乳美胸','url'=>'fengru'),
               8=> array('name'=>'翘臀','url'=>'qiaotun'),
               9=> array('name'=>'美腿','url'=>'meitui'),
               5=> array('name'=>'女神','url'=>'nvshen'),
               7=> array('name'=>'制服','url'=>'zhifu'),
               12=> array('name'=>'写真','url'=>'xiezhen'),
               1=> array('name'=>'模特','url'=>'mote'),
          );
          $cid = 10;
          $id = Yii::$app->request->get('id', 1);
          $page = Yii::$app->request->get('page', 1);
          //文章的信息
          $data = ByArticle::findOne($id)->toArray();

          $category_id = isset($data['category_id']) ? $data['category_id'] : 10;
          //文章总共多少图片
          $list =ByArticlePic::find()->where(['aid'=>$id])->asArray()->orderBy('sort_rank desc')->all();

          $imgurl = isset($list[$page-1]['pic_url']) ? $list[$page-1]['pic_url'] : '';

          //可能喜欢
          $guess = ByArticle::find()->where(['<','id',$id])->limit(12)->asArray()->orderBy('id desc')->all();
          //前一篇
          $preArticle = $this->preArticle($id);
          $nextArticle = $this->nextArticle($id);

          return $this->render('show', [
               'data'=>$data,
               'counts'=>count($list),
               'id'=>$id,
               'next'=>$page+1,
               'pre'=>$page-1,
               'list'=>$list,
               'page'=>$page,
               'imgurl'=>$imgurl,
               'guess'=>$guess,
               'preArticle'=>$preArticle,
               'nextArticle'=>$nextArticle,
               'cate'=>$cate[$category_id],
               'current'=>$cate[$category_id]['url']
          ]);
     }
     //前一篇
     private function preArticle($id){
          return  ByArticle::find()->where(['<','id',$id])->limit(1)->asArray()->orderBy('id desc')->one();
     }
     //后一篇
     private function nextArticle($id){
          return  ByArticle::find()->where(['>','id',$id])->limit(1)->asArray()->orderBy('id asc')->one();
     }

     /**
      *
      * 分类下的列表
      */
     public function getList($cid, $page=1, $limit=24){

          $offset = ($page-1)*$limit;

          if(!$cid){
              return false;
          }

          //$data = ByArticle::find()->where(['status' => 1, 'category_id'=>$cid])->offset($offset)->limit($limit)->asArray()->orderBy('id desc')->all();

          $query = ByArticle::find()->where(['status' => 1, 'category_id'=>$cid]);
          $count = $query->count();
          $query->orderBy([
               'id' => SORT_DESC
          ]);
          //$pagination = new Pagination(['totalCount' => $count,'pageSizeParam' => false,'validatePage' => false,'pageParam' => 'p']);
          $pagination = new Pagination(['totalCount' => $count,'pageSizeParam' => false,'validatePage' => false]);
          $pagination->setPageSize($limit);
          $list = $query->offset($pagination->offset)
               ->limit($pagination->limit)
               ->all();

          $data =  [
               'pagination' => $pagination,
               'list' => $list,
               'total' => $count,
          ];
          return $data;
     }

     //今日更新
     private function getArticleNew($limit =10){

          $key = "index:getArticleNew_".$limit;

          $data = Yii::$app->redis->get($key);
          if(!$data){
               $data = ByArticle::find()->select(['id','title','pic_url'])->where(['status' => 1])->limit($limit)->asArray()->orderBy('id desc')->all();
               Yii::$app->redis->set($key,$data,self::EXPIRE_TIME);
          }
          return $data;
     }

     private function getArticleByCategory($cid, $limit = 4){

          $key = "index:getArticleByCategory_".$cid.'_'.$limit;

          $data = Yii::$app->redis->get($key);
          if(!$data){
               $data = ByArticle::find()->select(['id','title','pic_url'])->where(['status' => 1, 'category_id'=>$cid])->limit($limit)->asArray()->orderBy('id desc')->all();
               Yii::$app->redis->set($key,$data,self::EXPIRE_TIME);
          }

         return $data;
     }



}