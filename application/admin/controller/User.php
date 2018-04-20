<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/2/2
 * Time: 16:33
 */

namespace app\admin\controller;

use app\admin\controller\base\BaseController;
use app\admin\model\User as UserModel;
use ivory\Ivory;
use think\Validate;

use think\Chat;

class User extends BaseController
{
    //用户列表
    public function userList(){
        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'count'=>0,
            'data'=>[],
        ];
        if (isset($_REQUEST['actionType']) && $_REQUEST['actionType']=='list'){
            $limit['limit']=$this->request->param('limit/d');
            $limit['page']=$this->request->param('page/d');
            $condition=$this->request->only(['name','phone','gender','status']);
            $logTime=$this->request->param('log_time');
            $regTime=$this->request->param('reg_time');
            if ($regTime==''){
                $regTime=[];
            }else{
                $regTime=explode(' - ',$regTime);
            }
            if ($logTime==''){
                $logTime=[];
            }else{
                $logTime=explode(' - ',$logTime);
            }


            foreach ($condition as $key=>$value){
                if ($condition[$key]==''){
                    unset($condition[$key]);
                }else{
                    $condition[$key]=['like',"%".$value."%"];
                }
            }

            $user = new UserModel();
            $returnData1 = $user->userList($limit,$condition,$regTime);
            $returnData['data']=$returnData1['data'];
            $returnData['count']=$returnData1['count'];
            return json($returnData);
        }else{
            return $this->fetch('userList');
        }
    }

    //添加用户
    public function addUser(){
        if (! $this->request->isAjax()){
            return $this->fetch('addUser');
        }
         $returnData=[
             'resultCD'=>200,
             'errorMsg'=>'',
            'data'=>[],
        ];
        $data=$this->request->only(['name','password','account','gender','phone','address','id_card','status','description','permission']);

         //去掉'',null,true,
        $data=Ivory::emptyString($data);
        $rule=[
            'account'=>'require|verifyNotExist',
            'password'=>'require|length:6,12'

        ];
        $msg=[
            'actionType.eq'=>'必要参数错误',
        ];
        $validate = new Validate($rule,$msg);
        //增加验证车辆和用户纯在
        $validate->extend([
            'verifyNotExist'=>function($value) {
                return (new \app\admin\model\User())->verify(['account'=>$value]) ? '该账户已经存在':true;
            },

        ]);

        if(!$validate->check($data)){
            $returnData['errorMsg']=$validate->getError();
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }

        //完成验证

        //加密
        $data['password']=Ivory::myEncrypt($data['password']);
        $data['user_id']=Ivory::get_uuid();

        if ((new UserModel())->addUser($data)){
            return $returnData;
        }else{
            $returnData['errorMsg']='添加用户失败';
            $returnData['resultCD']=config('custom.cdError');
            return $returnData;
        }


    }

    // 修改用户
    public function editUser(){
        $user = new UserModel();
        $data = $this->request->post();
        if($this->request->isPost()){
            if(!empty($data['password'])){
                $data['password'] = Ivory::myEncrypt($data['password']);
            }else{
                unset($data['password']);
            }
            $result = $user->editUser($data,$data['user_id']);
            if(!$result){
                $returnData = ['code'=>-3,'msg'=>'修改失败'];
            }else{
                $returnData = ['code'=>200,'msg'=>''];
            }
            return json($returnData);
        }
    }

    //删除
    public function deleteUser(){
        if (! $this->request->isAjax()){
            $this->redirect('/admin/user/userList');
        }
        $returnData=[
            'code'=>200,
            'msg'=>'',
            'data'=>[]
        ];
        $data=$this->request->only(['user_id']);

        $rule=[
            'user_id'=>'require',
        ];
        $validate=new Validate($rule);
        if(!$validate->check($data)){
            $returnData['msg']=$validate->getError();
            $returnData['code']=config('custom.cdError');
            return $returnData;
        }

        //完成验证

        if (( new UserModel())->destroyUser($data)){
            return ['code'=>200,'msg'=>'删除成功'];
        }else{
            return ['code'=>-3,'msg'=>'删除失败'];
        }


//        $user = new UserModel();
//        $user_id=$this->request->param('user_id');
//        $username=$this->request->param('account');
//        if($this->request->isGet()){
//            /**
//             * 删除环信账号
//             */
//            $Chat = new Chat(config("RingLetter"));
//            $result = $Chat->deleteUser($username);
//            if(isset($result['error'])){
//                return ['code'=>-3,'msg'=>$result['error']];
//            }else{
//                $result = $user->deleteUser($user_id);
//                if(!$result){
//                    return ['code'=>-3,'msg'=>'删除失败'];
//                }else{
//                    return ['code'=>200,'msg'=>'删除成功'];
//                }
//
//            }
//        }
    }

    //用户信息详情页
    public function userInfo(){

        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'count'=>0,
            'data'=>[],
        ];
        $condition['userId']=$this->request->param('user_id');
        $userData=(new \app\admin\model\User())->findUser($condition);

        if ($userData==[]){
            $this->error('查询错误，返回重试');
        }
        $this->assign('userData',$userData);
        return $this->fetch('info');
    }

}















