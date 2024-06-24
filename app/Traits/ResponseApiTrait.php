<?php

namespace App\Traits;

trait ResponseApiTrait
{
    protected function success($message = 'Thành công', $data = [], $status = 200)
    {
        return response([
            'type'      => 'success',
            'success'   => true,
            'data'      => $data,
            'message'   => $message,
        ], $status);
    }

    protected function failure($message = 'Có lỗi xảy ra', $type = 'error', $status = 500)
    {
        return response([
            'type'      => $type,
            'success'   => false,
            'message'   => $message,
        ], $status);
    }
}
