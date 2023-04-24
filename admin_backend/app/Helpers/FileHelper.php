<?php

namespace App\Helpers;

use App\Models\Gallery;
use App\Models\Settings;
use Illuminate\Http\UploadedFile;
use Throwable;

class FileHelper
{
    /**
     * Upload file function
     * @param UploadedFile $file
     * @param string $path
     * @return array
     */
    public static function uploadFile(UploadedFile $file, string $path): array
    {
        try {
            $isAws = Settings::adminSettings()->where('key', 'aws')->first();

            $options = [];

            if (data_get($isAws, 'value')) {
                $options = ['disk' => 's3'];
            }

            $id = auth('sanctum')->id() ?? "0001";
            $ext = strtolower(
                preg_replace("#.+\.([a-z]+)$#i", "$1",
                    str_replace(['.png', '.jpg'], '.webp', $file->getClientOriginalName())
                )
            );

            $fileName = $id . '-' . now()->unix() . '.' . $ext;

            $url = $file->storeAs("public/images/$path", $fileName, $options);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => config('app.img_host') . str_replace('public/images/', '', $url)
            ];
        } catch (Throwable $e) {

            $message = $e->getMessage();

            if ($message === "Class \"finfo\" not found") {
                $message = __('errors.' . ResponseError::FIN_FO, locale: request('language', 'en'));
            }

            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_400,
                'message'   => $message
            ];
        }
    }

    /**
     * Delete file function
     * @param $path
     * @return bool
     */
    public static function deleteFile($path): bool
    {
        return Gallery::where('path', $path)->delete();
    }

    /**
     * Обрезка картинки под стандарты системы
     * @param $target
     * @param $dest
     * @param $widthMax
     * @param $heightMax
     * @param $ext
     * @return void
     */
    public static function resize($target, $dest, $widthMax, $heightMax, $ext): void
    {

        list($wOrig, $hOrig) = getimagesize($target);

        $ratio = $wOrig / $hOrig;

        if (($widthMax / $heightMax) > $ratio) {
            $widthMax = $heightMax * $ratio;
        } else {
            $heightMax = $widthMax / $ratio;
        }

        $img = match ($ext) {
            'gif' => imagecreatefromgif($target),
            'png' => imagecreatefrompng($target),
            default => imagecreatefromjpeg($target),
        };

        $newImg = imagecreatetruecolor($widthMax, $heightMax);

        if ($ext == 'png') {
            imagesavealpha($newImg, true);
            $transPng = imagecolorallocatealpha($newImg, 0,0,0,127);
            imagefill($newImg, 0,0, $transPng);
        }

        imagecopyresampled($newImg, $img, 0,0,0,0, $widthMax, $heightMax, $wOrig, $hOrig);

        switch ($ext) {
            case('gif'):
                imagegif($newImg, $dest);
                break;
            case('png'):
                imagepng($newImg, $dest);
                break;
            default:
                imagejpeg($newImg,$dest);
        }

        imagedestroy($newImg);
    }

}
