<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FaceswapService
{
    public function faceswap($request) 
    {
        $fbId = $request['fb_id'];
        $themeId = $request['theme_id'];
        $imageId = $request['image_id'];
        $imagePath = "results/{$fbId}_{$themeId}_{$imageId}.jpg";

        try {
            $exist = Storage::disk('public')->exists($imagePath);
            if ($exist) {
                return $this->response($imagePath);
            }
            // $mplconfigdir = config('custom.MPLCONFIGDIR');
    
            // if ($mplconfigdir) {
            //     putenv("MPLCONFIGDIR=$mplconfigdir");
            // }
            
            $pythonScript = base_path('faceswap\faceswap.py');
            $pythonPath = 'D:\Programs\laragon\bin\python\python-3.10\python.exe';
            
            $command = [
                $pythonPath,
                $pythonScript,
                $fbId,
                $themeId,
                $imageId
            ];
            $process = new Process($command);
            $process->run();
    
    
            if (!$process->isSuccessful() && $process->getExitCode() !== 1) {
                throw new ProcessFailedException($process);
            }
           
            $output  = trim($process->getOutput());
            
            if (strpos($output, "Swapface successfully") === false) {
                return false;
            }
    
            return $this->response($imagePath);
        } catch (Exception $e) {
            logger($e->getMessage());
            return false;
        }
        // $pattern = '/result_[0-9]+_[a-zA-Z0-9]+\.jpg/';
        // preg_match($pattern, $output, $matches);
        
        // if (!$matches) {
        //     return response()->json(['message' => 'Not found'], 404);
        // }
        // $parsedUrl = parse_url($resultPath);

        // $resultPath = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

        // return response()->json(['message' => 'Swapface successfully', 'base64' => $resultPath], 200);

    }
    
    private function response($imagePath)
    {
        try {
            $resultPath = Storage::disk('public')->path($imagePath);
            $resultBase64 = base64_encode(file_get_contents($resultPath));
    
            return $resultBase64;
        } catch (Exception $e) {
            logger($e->getMessage());
            return '';
        }
    } 
}