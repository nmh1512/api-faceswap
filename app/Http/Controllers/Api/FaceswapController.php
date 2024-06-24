<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\FaceswapService;
use App\Traits\ResponseApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceswapController extends Controller
{
    use ResponseApiTrait;

    protected $faceswapService;

    public function __construct(FaceswapService $faceswapService) 
    {   
        $this->faceswapService = $faceswapService;
    }
    public function faceswap(Request $request)
    {
        $request->validate([
            'fb_id' => 'required',
            'theme_id' => 'required',
            'image_id' => 'required',
        ]);
        $result = $this->faceswapService->faceswap($request->all());
        if (!$result) {
            return $this->failure();
        }
        return $this->success("Thành công", $result);
    }

    private function response($imagePath)
    {
        try {
            $resultPath = Storage::disk('public')->path($imagePath);
            $resultBase64 = base64_encode(file_get_contents($resultPath));
    
            return $this->success('Thành công', $resultBase64);
        } catch (Exception $e) {
            logger($e->getMessage());
            return $this->failure();
        }
    } 
}
