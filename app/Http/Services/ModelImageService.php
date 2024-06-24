<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ModelImageService
{
    public function crawlThemes($request)
    {
        $request['lang'] = $request['lang'] ?? 'vi';
        try {
            $response = Http::get('https://api.thebetter.ai/api/v1/fb/themes/v2', $request);
            $isOK = $response['msg'] == 'OK';
            return $isOK ? ($response->json()['data']['list'] ?? []) : '';
        } catch (\Exception $e) {
            logger($e->getMessage());
            return '';
        }
    }
    public function storeThemes($request)
    {
        $lang = $request['lang'] ?? 'vi';
        $themes = $this->crawlThemes($request);
        if (!$themes) {
            return [
                'success' => false,
                'code' => 404,
                'msg' => 'Không có dữ liệu'
            ];
        }
        try {
            foreach ($themes as $theme) {
                $themeId = $theme['theme_id'];
                $themeImage = file_get_contents($theme['cover']);
                $path = "models/themes/{$themeId}.jpg";
                if (!Storage::disk('public')->exists($path))
                    Storage::disk('public')->put($path, $themeImage);
            }
            $jsonData = json_encode($themes);
            $fileName = "themes_{$lang}.json";

            $path = "models/themes/{$fileName}";
            if (!Storage::disk('public')->exists($path))
                Storage::disk('public')->put($path, $jsonData);
                
            return [
                'success' => true,
                'code' => 200,
                'msg' => 'Dữ liệu đã được lưu vào file JSON.'
            ];
        } catch (\Exception $e) {
            logger($e->getMessage());
            return [
                'success' => false,
                'code' => 500,
                'msg' => 'Có lỗi xảy ra'
            ];
        }
    }
    public function crawlImages($request)
    {
        $request['lang'] = $request['lang'] ?? 'vi';
        try {
            $response = Http::get('https://api.thebetter.ai/api/v1/theme/images', $request);

            $isOK = $response['msg'] == 'OK';
            return $isOK ? ($response->json()['data'] ?? []) : '';
            
        } catch (\Exception $e) {
            logger($e->getMessage());
            return '';
        }
    }

    public function getThemes($request)
    {
        $lang = $request['lang'] ?? 'vi';
        $fileName = "themes_{$lang}.json";
        try {
            $data = Storage::disk('public')->get("models/themes/{$fileName}") ?? null;
            return $data ? json_decode($data, true) : '';
        } catch (\Exception $e) {
            logger($e->getMessage());
            return '';

        }
    }
    public function storeImages($request)
    {
        $themes = $this->getThemes($request);

        foreach ($themes as $theme) {
            $themeId = $theme['theme_id'];
            $lang    = $request['lang'] ?? 'vi';
            try {
                $request['theme_id'] = $themeId;
                $images = $this->crawlImages($request);
                $directory = "models/images/{$themeId}";
                foreach ($images as $image) {
                    $imageKey = explode('/', $image['key']);
    
                    $imageId = $imageKey[1];
                    $imageImage = file_get_contents($image['thn_url']);
    
                    if (!Storage::disk('public')->exists($directory)) 
                        Storage::disk('public')->makeDirectory($directory);
    
                    if (!Storage::disk('public')->exists("{$directory}/{$imageId}")) 
                        Storage::disk('public')->put("{$directory}/{$imageId}", $imageImage);
                }
                $jsonData = json_encode($images);
                $fileName = "images_{$themeId}_{$lang}.json";
                Storage::disk('public')->put("{$directory}/{$fileName}", $jsonData);
                
            } catch (\Exception $e) {
                logger($e->getMessage());
                continue;
            }
        }

        return [
            'success' => true,
            'code' => 200,
            'msg' => 'Dữ liệu đã được lưu vào file JSON.'
        ];
    }

    public function getImages(Request $request) 
    {
        
    }
}
