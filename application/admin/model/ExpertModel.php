<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/4/18
 * Time: 18:01
 */

namespace app\admin\model;


use app\admin\model\base\BaseModel;
use ivory\Ivory;

class ExpertModel extends BaseModel
{
    protected $table ='expert';
    protected $resultSetType='collection';
    protected $autoWriteTimestamp ='datetime';
    protected $updateTime ='update_time';
    protected $createTime = 'create_time';
    protected $dateFormat='Y-m-d H:i:s';

    //专家列表
    public function expertList($condition=[],$limit=['page'=>1,'limit'=>10]){
        $returnData['Data'] =$this->where($condition)
            ->page($limit['page'],$limit['limit'])
            ->select()
            ->toArray();

        $returnData['Count']=$this->where($condition)
            ->count();
        return $returnData;
    }

    //验证名家是否存在
    public function verify($condition){

        if ($this->where($condition)->find()){
            return true;
        }else{
            return false;
        }
    }

    //增加名家
    public function addExpert($data){
        return $this->allowField(true)->isUpdate(false)->save($data);
    }

    //删除名家
    public function deleteExpert($condition){
        return $this->where($condition)->delete();
    }

    //修改名家
    public function editExpert($data){
        return $this->allowField(true)->isUpdate(true)->save($data);
    }

    //查找
    public function findExpert($condition){
        return $this->where($condition)->select()->toArray();
    }

    //重置密码
    public function resetExpert($expertId){
        $data['password']=Ivory::myEncrypt(config('custom.resetMjPassword'));
        return $this->allowField(true)->Update($data,$expertId);
    }


}