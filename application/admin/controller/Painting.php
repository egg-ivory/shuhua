<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/4/20
 * Time: 14:43
 */

namespace app\admin\controller;


use app\admin\controller\base\BaseController;
use app\admin\model\PaintingUserView;

class Painting extends BaseController
{
    //画作列表
    public function index(){

        if (! $this->request->isAjax()){
            return $this->fetch('index');
        }
        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'data'=>[],
            'count'=>0
        ];
        //分页
        $limit=$this->request->only(['limit','page']);
        if (!isset($limit['limit']) || !isset($limit['page']) || $limit['limit'] =='' || $limit['page']==''){
            $limit=[
                'page'=>1,
                'limit'=>10,
            ];
        }

        //筛选
        $condition=$this->request->only(['painting_name','name']);

        foreach ($condition as $key=>$value){
            if ($value == ''){
                unset($condition[$key]);
            }else{
                $condition[$key]=['like',"%".$value."%"];
            }

        }
        $expert= new PaintingUserView();
        $result=$expert->paintingList($condition,$limit);
        $returnData['data']=$result['Data'];
        $returnData['count']=$result['Count'];
        return $returnData;
    }

}