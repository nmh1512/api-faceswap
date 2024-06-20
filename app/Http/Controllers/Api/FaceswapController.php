<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\FaceswapService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FaceswapController extends Controller
{

    protected $faceswapService;
    public function __construct(FaceswapService $faceswapService) 
    {
        $this->faceswapService = $faceswapService;
    }
    public function faceswap(Request $request)
    {
        $request->validate([
            'fb_id' => 'required',
            'model_id' => 'required|integer',
        ]);
        
        $result = $this->faceswapService->faceswap($request);
        
        return response()->json(
            ['message' => $result['message'], 'path' => $result['path'] ?? ''], $result['code']
        );
    }
}
