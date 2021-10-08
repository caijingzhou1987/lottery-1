<?php

namespace App\Admin\Controllers;

use App\Models\Prize;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PrizesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '奖品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Prize());

        $grid->id('ID');
        $grid->title('标题');
        $grid->url('图片');
        $grid->probability('概率');
        $grid->status('状态')->editable('select', [0 => '未启用', 1 => '已启用']);
        return $grid;
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Prize());
        
        $form->text('title', '标题')->rules('required');
        $form->image('url','图片')->rules('required|image');
        $form->text('probability', '概率')->rules('required');
        $form->radio('status', '状态')->options(['1' => '已启用', '0' => '未启用'])->default(1);
        return $form;
    }
}
