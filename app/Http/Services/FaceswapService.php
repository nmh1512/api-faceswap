<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FaceswapService
{
    public function faceswap(Request $request) 
    {
        $imageName = 'fb_image_' . $request->fb_id . '.png';
        
        $pythonScript = base_path('faceswap\faceswap.py');
        $pythonPath = 'python3';
        $imagePath = public_path('storage/uploads/') . $imageName;
        $modelPath = public_path('storage/models/') . "{$request->model_id}.png";
        
        $command = [
            $pythonPath,
            $pythonScript,
            $imagePath,
            $modelPath
        ];
        try {
            $process = new Process($command);
            $process->run();
        } catch (ProcessFailedException $e) {
            logger($e->getMessage());
            return [
                'status'    => 'error',
                'message'   => 'Something went wrong',
                'code'      => 500
            ];
        }

        $output  = trim($process->getOutput());
        preg_match('/result_[a-zA-Z0-9\-]+\.jpg/', $output, $matches);

        if (!$matches) {
            return [
                'status'    => 'error',
                'message'   => 'Not found',
                'code'      => 404
            ];
        }
        $resultFilename = $matches[0];
        return [
            'status'    => 'success',
            'message'   => 'Swapface successfully',
            'path'      => public_path('storage/results/') . $resultFilename,
            'code'      => 200
        ];
    }
}
