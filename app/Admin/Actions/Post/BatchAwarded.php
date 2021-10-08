<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchAwarded extends BatchAction
{
    public $name = '批量已发奖';

    protected $status;

    public function __construct($status = 0)
    {
        $this->status = $status;
    }

    public function handle(Collection $collection)
    {

        foreach ($collection as $model) {
            $model->update(['award_status' => $this->status]);
        }

        return $this->response()->success('Success message...')->refresh();
    }

}