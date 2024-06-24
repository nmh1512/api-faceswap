<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\ModelImageService;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelImageController extends Controller
{
    use ResponseApiTrait;

    protected $modelImageService;
    public function __construct(ModelImageService $modelImageService)
    {
        $this->modelImageService = $modelImageService;
    }
    public function storeThemes(Request $request)
    {
        $result = $this->modelImageService->storeThemes($request->all());
        if (!$result['success']) {
            return $this->failure($result['msg'], 'error', $result['code']);
        }
        return $this->success($result['msg']);
    }

    public function getThemes(Request $request)
    {
        $themes = $this->modelImageService->getThemes($request);
        
        if (!$themes) {
            return $this->failure();
        }
        return $this->success('Thành công', $themes);
    }


    public function storeImages(Request $request)
    {
        $result = $this->modelImageService->storeImages($request->all());
        if (!$result['success']) {
            return $this->failure($result['msg'], 'error', $result['code']);
        }
        return $this->success($result['msg']);
    }
}
