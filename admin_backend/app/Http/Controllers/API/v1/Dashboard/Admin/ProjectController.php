<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Helpers\ResponseError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ProjectController extends AdminBaseController
{
    /**
     * @param Request $request
     * @throws Throwable
     * @return JsonResponse
     */
    public function projectUpload(Request $request): JsonResponse
    {

        $file            = $request->file('file');
        $destinationPath = base_path();
        $fileName        = 'foodyman.zip';

        if (empty($file)) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_432]);
        }

        if ($file->extension() !== 'zip') {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_432, 'message' => 'incorrect zip']);
        }

        try {
            $file->move($destinationPath, $fileName);

            return $this->successResponse(
                __('errors.' . ResponseError::SUCCESS, locale: $this->language),
                ['title' => $fileName]
            );
        } catch (Throwable $e) {
            $this->error($e);
            return $this->onErrorResponse(['code' => ResponseError::ERROR_501]);
        }
    }

    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function projectUpdate(): JsonResponse
    {
        try {
            Artisan::call('project:update');

            return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language)
        );
        } catch (Throwable $e) {
            $this->error($e);
            return $this->onErrorResponse(['code' => ResponseError::ERROR_502]);
        }
    }
}
