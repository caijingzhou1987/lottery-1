<?php

namespace App\Admin\Controllers;

use App\Models\LotteryCode;
use Encore\Admin\Controllers\AdminController;
use App\Admin\Actions\Post\BatchAwarded;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Str;
use DB;

class LotteryCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '抽奖码';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LotteryCode());
        $grid->model()->orderBy('prizes_time', 'desc');
        $grid->id('Id');
        $grid->code('抽奖码');
        $grid->batch_num('批次号');
        $grid->prizes_name('奖品');
        $grid->valid_period('有效期');
        $grid->prizes_time('抽奖时间');
        $grid->award_status('发奖状态')->editable('select', [0 => '未发放', 1 => '已发放']);
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('code', '抽奖码');
            $filter->like('batch_num', '批次号');
            $filter->between('prizes_time', '抽奖时间')->datetime();
        });
        return $grid;
    }

    public function create(Content $content)
    {
        $batch_nums = LotteryCode::query()
            ->select('batch_num')
            ->groupBy('batch_num')
            ->get();
        return $content
            ->header('生成抽奖码')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.lottery.create',['batch_nums'=>$batch_nums]));
    }

    public function generateCode(Request $request){
        $nums = $request->nums;
        $date = date('Y-m-d',strtotime($request->date));
        $batch_num = date('Ymd');
        $insert_codes = [];
        $codes = self::findAvailableNo($nums);
        foreach ($codes as $code) {
            $insert_codes[] = ['code'=>$code,'batch_num'=>$batch_num,'valid_period'=>$date];
        }
        $insert_nums = DB::table('lottery_codes')->insert($insert_codes);
        if($insert_nums){
            $msg = ['code'=>200,'message'=>'抽奖码生成成功'];
        }else{
            $msg = ['code'=>0,'message'=>'抽奖码生成失败'];
        }
        return $msg;
    }

    public static function findAvailableNo($nums=1000)
    {
        set_time_limit(1500);
        $codes = [];
        for ($i = 0; $i < 10000; $i++) {
            // 随机生成8位的数字加字母
            $no = Str::upper(Str::random(2)).str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!in_array($no,$codes)) {
                array_push($codes,$no);
            }else{
               $no = Str::upper(Str::random(2)).str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
               if (!in_array($no,$codes)) {
                   array_push($codes,$no);
               }else{
                    $no = Str::upper(Str::random(2)).str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    if (!in_array($no,$codes)) {
                        array_push($codes,$no);
                    }    
               } 
            }
        }
        return $codes;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new LotteryCode());

        $form->text('code', __('Code'));
        $form->number('batch_num', __('Batch num'));
        $form->text('prizes_name', __('Prizes name'));
        $form->datetime('valid_period', __('Valid period'))->default(date('Y-m-d H:i:s'));
        $form->datetime('prizes_time', __('Prizes time'))->default(date('Y-m-d H:i:s'));
        $form->text('operator', __('Operator'));
        $form->switch('award_status', __('Award status'));

        return $form;
    }
}
