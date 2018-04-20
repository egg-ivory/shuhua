<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/4/20
 * Time: 14:27
 */

namespace app\admin\model;


use app\admin\model\base\BaseModel;
use traits\model\SoftDelete;

class PaintingUserView extends BaseModel
{
    use SoftDelete;
    protected $deleteTime ='delete_time';

    protected $table ='painting_user_view';
    protected $resultSetType='collection';
    protected $autoWriteTimestamp ='datetime';
    protected $updateTime ='update_time';
    protected $createTime = 'create_time';
    protected $dateFormat='Y-m-d H:i:s';

    //画作列表
    public function paintingList($condition=[],$limit=['page'=>1,'limit'=>10]){
        $returnData['Data'] =$this->where($condition)
            ->page($limit['page'],$limit['limit'])
            ->select()
            ->toArray();

        $returnData['Count']=$this->where($condition)
            ->count();
        return $returnData;
    }

    //验证画作是否存在
    public function verify($condition){

        if ($this->where($condition)->find()){
            return true;
        }else{
            return false;
        }
    }

    //增加画作
    public function addPainting($data){
        return $this->allowField(true)->isUpdate(false)->save($data);
    }

    //删除画作
    public function deletePainting($condition){
        return $this->where($condition)->delete();
    }

    //修改画作
    public function editPainting($data){
        return $this->allowField(true)->isUpdate(true)->save($data);
    }

    //查找
    public function findPainting($condition){
        return $this->where($condition)->select()->toArray();
    }
}