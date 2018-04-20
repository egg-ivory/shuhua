<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/2/2
 * Time: 10:25
 */

namespace app\admin\model;


use app\admin\model\base\BaseModel;
use ivory\Ivory;

class Admin extends BaseModel
{
    protected $table ='admin';
    protected $resultSetType='collection';
    protected $dateFormat='Y-m-d H:i:s';

    public function findAdmin($condition){
      return  $this->where('admin_name','=',$condition)->find();
    }

    public function findOne($adminName=''){
        return $this->where('admin_name','=',$adminName)->value('password');
    }

    public function editAdmin($data,$adminName){
            return $this->allowField(true)->isUpdate(true)->save($data,$adminName);
    }

    public function adminList($limit=['page'=>1,'limit'=>10],$condition=[],$field=['admin_id','admin_name'],$permission=['permission'=>['<>','1']]){

        $returnData['data']=$this->where($condition)
            ->where($permission)
            ->field($field)
            ->page($limit['page'],$limit['limit'])
            ->order(['admin_id'=>'desc'])
            ->select()
            ->toArray();

        $returnData['count']=$this->where($condition)
            ->where($permission)
            ->count();

        return $returnData;
    }

    public function addAdmin($data){

        return $this->allowField(true)->isUpdate(false)->save($data);

    }

    public function deleteAdmin($adminId){
        return $this->where('admin_id','=',$adminId)->delete();
    }

    public function resetAdmin($adminId){
        $data['password']=Ivory::myEncrypt(config('custom.resetPassword'));
        return $this->allowField(true)->Update($data,$adminId);
    }

}