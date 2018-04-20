<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/3/6
 * Time: 15:46
 */

namespace app\admin\controller;


use app\admin\controller\base\BaseController;
use ivory\Ivory;
use think\Validate;


class Admin extends BaseController
{

    //管理员列表
    public function adminList(){
//        $this->superVerify();
        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
            'count'=>0,
            'data'=>[],
        ];
        if (isset($_REQUEST['actionType']) && $_REQUEST['actionType']=='list'){
            $limit['limit']=$this->request->param('limit/d');
            $limit['page']=$this->request->param('page/d');
            $condition=$this->request->only(['admin_name']);
            if (isset($condition['admin_name']) && !$condition['admin_name']){
                $condition=[];
            }
            foreach ($condition as $key=>$value){
                if ($condition[$key]==''){
                    unset($condition[$key]);
                }else{
                    $condition[$key]=['like',"%".$value."%"];
                }
            };
            $admin = new \app\admin\model\Admin();
            $returnData1 = $admin->adminList($limit,$condition);
            $returnData['data']=$returnData1['data'];
            $returnData['count']=$returnData1['count'];
            return json($returnData);


        }else{
            return $this->fetch('adminList');
        }

    }

    //修改密码入口
    public function editAdmin(){


        $actionType=$this->request->param('actionType');
        if ($actionType == 'verify'){
            return $this->verify();
        }else if ($actionType == 'edit'){
            return $this->edit();
        }else{
            $returnData = [
                'resultCD' => 200,
                'errorMsg' => '',

            ];
            $this->assign('returnData',$returnData);
            return $this->fetch('edit');
        }

    }



    //增加管理员
    public function addAdmin(){
//        $this->superVerify();
        $returnData=[
            'resultCD'=>200,
            'errorMsg'=>'',
        ];
        //判断是增加car
        if(!(isset($_POST['actionType']) && $_POST['actionType']=='add')){
            $this->assign('returnData',$returnData);
            return $this->fetch('addAdmin');
        }
        $data = $this->request->only(['admin_name','password','rePassword']);

        $rule = [
            'admin_name'=>'require|unique:Admin',
            "password" => "require|confirm:rePassword|length:6,12|alphaDash",
            'rePassword'=>'require|confirm:password'
        ];
        $msg = [
            'admin_name.require' => '用户名必须',
            'admin_name.unique' => '用户名不能重复',
            'password.require' => '密码必须',
            'password.confirm' => '两次密码必须相同',
            'password.length' => '密码长度为6到12位',
            'password.alphaDash' => '密码只能包括数字字母下划线和破折号',
            'rePassword.require' => '密码必须',
        ];
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $returnData['errorMsg'] = $validate->getError();
            $this->assign('returnData',$returnData);
            return $this->fetch('addAdmin');
        }

        $data['password']=Ivory::myEncrypt($data['password']);
        if((new \app\admin\model\Admin())->addAdmin($data)){
            $this->redirect('admin/admin/adminList');
        };

        $returnData['errorMsg']='添加失败';
        $this->assign('returnData',$returnData);
        return $this->fetch('addAdmin');


    }

    //删除管理员
    public function deleteAdmin(){
        //$this->superVerify();
        if($this->request->param('actionType')=='delete'){

            $returnData=[
                'resultCD'=>200,
                'errorMsg'=>'',
                'data'=>[],
            ];
            $carId=$this->request->param('admin_id/d');
            $result = (new \app\admin\model\Admin())->deleteAdmin($carId);

            if (!$result){
                $returnData['resultCD']=404;
                $returnData['errorMsg']='删除数据失败';
                return json($returnData);

            }else{
                return json($returnData);
            }
        } else{
            $this->redirect('admin/admin/adminList');
        }
    }

    //重置管理员
    public function resetAdmin(){
       // $this->superVerify();
        if($this->request->param('actionType')=='reset'){

            $returnData=[
                'resultCD'=>200,
                'errorMsg'=>'',
                'data'=>[],
            ];
            $adminId=$this->request->param('admin_id/d');
            $adminId=['admin_id'=>$adminId];

            $result = (new \app\admin\model\Admin())->resetAdmin($adminId);

            if (!$result){
                $returnData['resultCD']=404;
                $returnData['errorMsg']='重置数据失败';
                return json($returnData);

            }else{
                return json($returnData);
            }
        } else{
            $this->redirect('admin/admin/adminList');
        }
    }


    //修改密码时验证
    protected function verify()
    {
        $returnData = [
            'resultCD' => 200,
            'errorMsg' => '',
        ];

        $data = $this->request->only(['loginPwd']);

        $rule = [
            "loginPwd" => "require",
        ];
        $msg = [
            'loginPwd.require' => '密码必须',
        ];
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $returnData['errorMsg'] = $validate->getError();

            $this->assign('returnData',$returnData);
            return $this->fetch('edit');
        }

        $loginId=session('myName');
        $password=(new \app\admin\model\Admin())->findOne($loginId);
        if ($password !==null && $password == Ivory::myEncrypt($data['loginPwd'])){
            $returnData['resultCD'] = 400;
            $this->assign('returnData',$returnData);
            return $this->fetch('edit');
        }

        $returnData['errorMsg']='用户名或密码错误';
        $this->assign('returnData',$returnData);
        return $this->fetch('edit');

    }

    //修改密码
    protected function edit()
    {
        $returnData = [
            'resultCD' => 400,
            'errorMsg' => '',
        ];

        $data = $this->request->only(['password','rePassword']);

        $rule = [
            "password" => "require|confirm:rePassword|length:6,12|alphaDash",
            'rePassword'=>'require|confirm:password'
        ];
        $msg = [
            'password.require' => '密码必须',
            'password.confirm' => '两次密码必须相同',
            'password.length' => '密码长度为6到12位',
            'password.alphaDash' => '密码只能包括数字字母下划线和破折号',
            'rePassword.require' => '密码必须',
        ];
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $returnData['errorMsg'] = $validate->getError();
            $this->assign('returnData',$returnData);
            return $this->fetch('edit');
        }
        $data=['password'=>Ivory::myEncrypt($data['password'])];
        $loginName=['admin_name'=>session('myName')];

        if((new \app\admin\model\Admin())->editAdmin($data,$loginName)){
             $this->redirect('admin/login/logout');
        };

        $returnData['errorMsg']='修改失败';
        $this->assign('returnData',$returnData);
        return $this->fetch('edit');

    }

    //需要超级管理员时验证是否为超级管理员
    protected function superVerify(){
        $supperName=session('myName');
        if ($supperName != config('custom.superAdmin')){
            if ($this->request->isAjax()){
                header('Content-type: application/json');
                echo json_encode([
                    'resultCD'=>404,
                    'errorMsg'=>'没有该权限',
                    'data'=>[],
                ]);
                exit();
            }else{
                $this->redirect('admin/car/carList');
            }

        }
    }


}