<?php
/**
 * Created by PhpStorm.
 * User: FSZY-001
 * Date: 2018/1/16
 * Time: 9:37
 */

namespace app\admin\model;

use app\admin\model\base\BaseModel;
use think\Model;
use traits\model\SoftDelete;

class User extends BaseModel
{
    use SoftDelete;
    protected $deleteTime ='delete_time';
    protected $table ='user';
    protected $resultSetType='collection';
    protected $autoWriteTimestamp ='datetime';
    protected $updateTime ='update_time';
    protected $createTime = 'create_time';


    //获取器
    public function getGenderAttr($gender){
        $status=[0=>'女',1=>'男',2=>'保密'];
        return $status[$gender];
    }

    //用户列表
    public function userList($limit=['page'=>1,'limit'=>10],$condition=[],$regTime=[],$logTime=[]){
        if ($logTime==[]){
            $logTime=['1970-10-1 00:00:00',date('Y-m-d H:i:s')];
        };
        if ($regTime==[]){
            $regTime=['1970-10-1 00:00:00',date('Y-m-d H:i:s')];
        }


        $returnData['data']=$this->where($condition)
            ->whereTime('create_time','between',$regTime)
            //->whereTime('login_time','between',$logTime)
            ->page($limit['page'],$limit['limit'])
            ->order(['create_time'=>'desc','user_id'=>'desc'])
            ->select()
            ->toArray();

        $returnData['count']=$this->where($condition)
            ->whereTime('create_time','between',$regTime)
            //->whereTime('login_time','between',$logTime)
            ->count();


        return $returnData;
    }

    //单个用户详情
    public function findUser($condition){
        $result = $this->where('user_id','=',$condition['userId'])->find();
        if ($result!=null){
            $result=$result->toArray();
        }else{
            $result=[];
        }
        return $result;

    }

    //编辑
    public function editUser($data,$userId){
        return $this->allowField(true)->save($data,$userId);
    }

    //添加用户
    public function addUser($data){
        return $this->allowField(true)->isUpdate(false)->save($data);
    }

    //删除用户
    public function destroyUser($condition){
        $where= function ($query) use ($condition){
            $query->where($condition);
        };
        return $this->destroy($where);
    }

    //验证用户是否存在
    public function verify($condition){
        if ($this->where($condition)->find()){
            return true;
        }else{
            return false;
        }
    }



    public function userCount($condition){
        return $this->where($condition)->count();
    }

    public function likeUser($where = []){
        return $this->where($where)->select()->toArray();

    }

    public function addUserOther($data){
        return db('user_car')->insert($data);
    }

    public function editUserOther($data,$uc_id){
        return db('user_car')->where('uc_id',$uc_id)->update($data);
    }

    public function deleteUser($user_id){
        if($user_id){
            $user_car = db('user_car')->where('user_id',$user_id)->select()->toArray();
            if($user_car){
                foreach ($user_car as $key => $val) {
                    $return = db('user_car')->where('user_id',$val['user_id'])->delete();
                }
            }
            $return = $this->where('user_id',$user_id)->delete();
            return $return;
        }
    }


//    public function articleDb(){
//        return $this->hasMany('Article','user_id','id');
//    }
//
//    public function findArticle($value){
//        return $this->with('article_db')->where('id','=',$value)->find();
//
//    }

}