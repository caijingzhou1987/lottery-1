<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LotteryCode;
use App\Models\Prize;
use Carbon\Carbon;

class LotteryController extends Controller
{
    //抽奖首页
    public function index(Request $request){
        $prizes = Prize::query()
            ->where('status', 1)
            ->get();
        return view('lottery.index',['prizes' => $prizes]);
    }

    public function code(Request $request)
    {
        $code = strtoupper($request->input('code'));
        if(empty($code)){
            return ['code'=>0,'message'=>'抽奖码不能为空'];   
        }
        $codes = LotteryCode::where('code',$code)->first();
        if(empty($codes)){
            return ['code'=>0,'message'=>'抽奖码不存在,请核对'];
        }
        if($codes->prizes_time){
            return ['code'=>0,'message'=>'该抽奖码已使用'];
        }
        if(Carbon::now()->gte($codes->valid_period)){
            return ['code'=>0,'message'=>'该抽奖码已过期'];
        }
        $prizes = Prize::query()
            ->where('status', 1)
            ->get();
        foreach ($prizes as $key => $val) {
            $arr[$val['id']] = $val['probability'];
        }
        $rid = $this->get_rand($arr); //根据概率获取奖项id
        $prizes_name = $prizes[$rid-1]['title'];
        $res['yes'] = $prizes[$rid-1]['id']; //中奖项
        $codes->update([
            'prizes_time'=>time(),
            'prizes_name'=>$prizes_name
        ]);
        return ['code'=>200,'index'=>$res['yes'],'number'=>$codes->code];
    }

    protected function get_rand($proArr) {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
     
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
     
        return $result;
    }
}
