<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/4/20
 * Time: 14:43
 */

namespace app\admin\controller;


use app\admin\controller\base\BaseController;
use app\admin\model\ExpertModel;
use app\admin\model\PaintingUserView;
use ivory\Ivory;
use think\Validate;

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

    //增加名家
    public function addExpert(){
        if (! $this->request->isAjax()){
            return $this->fetch('addExpert');
        }

        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'data'=>[],
        ];
        $data=$this->request->only(['permission','expert_name','account','password','rePassword','description']);

        //去掉'',null,true,
        $data=Ivory::emptyString($data);

        $rule=[
//            'permission'=>'require',
            'expert_name'=>'require',
            'account'=>'require|verifyExpertNotExist',
            "password" => "require|confirm:rePassword|length:6,12|alphaDash",
            'rePassword'=>'require|confirm:password'
        ];
        $msg=[
            'password.require' => '密码必须',
            'password.confirm' => '两次密码必须相同',
            'password.length' => '密码长度为6到12位',
            'password.alphaDash' => '密码只能包括数字字母下划线和破折号',
            'rePassword.require' => '密码必须',
            'account.verifyExist'=>'用户名不能重复',
        ];
        $validate= new Validate($rule,$msg);

        //增加验证用户存在
        $validate->extend([
            'verifyExpertNotExist'=>function($value) {
                return (new \app\admin\model\User())->verify(['account'=>$value]) ? '该账号已经存在':true;
            },

        ]);
        if(!$validate->check($data)){
            $returnData['errorMsg']=$validate->getError();
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }
        //完成验证

        //获得uuid
        $data['expert_id']=Ivory::get_uuid();


        //密码加密
        $data['password']=Ivory::myEncrypt($data['password']);

        //开始增加
        $expert=new PaintingUserView();
        if ($expert->addPainting($data)){
            return $returnData;
        }else{
            $returnData['errorMsg']='增加名家失败';
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }

    }

    //删除名家，
    public function deleteExpert(){
        if (! $this->request->isAjax()){
            return $this->fetch('index');
        }

        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'data'=>[],
        ];
        $data=$this->request->only(['expert_id',]);
        $rule=[
            'expert_id'=>'require'
        ];
        $validate=new Validate($rule);
        if(!$validate->check($data)){
            $returnData['errorMsg']=$validate->getError();
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }
        //完成验证

        //开始删除
        $expert=new  PaintingUserView();
        if ($expert->deletePainting($data)){
            return $returnData;
        }else{
            $returnData['errorMsg']='删除名家失败';
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }

    }

    //编辑
    public function editExpert(){
        if (! $this->request->isAjax()){
            $data=$this->request->only(['expert_id']);

            if (! isset($data['expert_id'] ) || $data['expert_id'] ==''){

            }

            //查找
            $expertData=((new ExpertModel())->findExpert($data));
            if (empty($expertData)){
                $this->redirect('/admin/expert/index');
            }
            $expertData=$expertData[0];
            $this->assign('expertData',$expertData);
            return $this->fetch('editExpert');
        }

        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'data'=>[],
        ];


        $data=$this->request->only(['expert_id','expert_name','description']);

        //去掉'',null,true,
        $data=Ivory::emptyString($data);
        $rule=[
            'expert_id'=>'require|verifyExpertExist',
            'expert_name'=>'require',
        ];
        $msg=[
            'password.length'=>'密码长度在6到16位之间',
            'account.verifyExist'=>'账户已经存在，重新输入',
        ];
        $validate= new Validate($rule,$msg);

        //增加验证名家存在
        $validate->extend([
            'verifyExpertExist'=>function($value) {
                return (new \app\admin\model\ExpertModel())->verify(['expert_id'=>$value]) ? true:'没有该名家账户';
            },

        ]);
        if(!$validate->check($data)){
            $returnData['errorMsg']=$validate->getError();
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }
        //完成验证

        //开始修改
        $expert=new PaintingUserView();
        if ($expert->editPainting($data)){
            return $returnData;
        }else{
            $returnData['errorMsg']='修改名家失败';
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }

    }


}