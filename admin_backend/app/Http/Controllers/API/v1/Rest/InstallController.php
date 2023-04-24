<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService\UserVerifyService;
use App\Services\ProjectService\ProjectService;
use App\Services\UserServices\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InstallController extends RestBaseController
{
    public function checkInitFile(): JsonResponse
    {
        $result = File::exists(config_path('init.php'));

        if (!$result) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            trans('errors.' . ResponseError::NO_ERROR, [], $this->language),
            config('init')
        );
    }

    public function setInitFile(Request $request): JsonResponse
    {
        $name       = $request->input('name', config('app.name'));
        $name       = Str::of($name)->replace("'", "\'");
        $favicon    = $request->input('favicon', '');
        $logo       = $request->input('logo', '');
        $delivery   = $request->input('delivery', 0);
        $multiShop  = $request->input('multy_shop', 0);

        File::put(config_path('init.php'),
            "<?php\n return [
                        \n'name' => '$name',
                        \n'favicon' => '$favicon',
                        \n'logo' => '$logo',
                        \n'delivery' => '$delivery',
                        \n'shop_type' => '$multiShop',
                        \n];"
        );

        return $this->successResponse(
            trans('errors.' .ResponseError::NO_ERROR, [], $this->language),
            config('init')
        );
    }

    public function setDatabase(Request $request): JsonResponse
    {
        try {
            Artisan::call('optimize:clear');

            $path     = file_get_contents(base_path('.env'));
            $env      = $request->input('env') ? 'production' : 'local';
            $database = $request->input('database', 'laravel');
            $username = $request->input('username', 'root');
            $appName  = config('init.name', 'Laravel');

            file_put_contents($path, str_replace(
                'APP_NAME=' . config('app.name'),
                'APP_NAME=' . Str::slug($appName),
                $path
            ));

            file_put_contents($path, str_replace(
                'APP_ENV=' . config('app.env'),
                'APP_ENV=' . $env,
                $path,
            ));

            file_put_contents($path, str_replace(
                'DB_DATABASE=' . config('database.connections.mysql.database'),
                "DB_DATABASE=$database",
                $path,
            ));

            file_put_contents($path, str_replace(
                'DB_USERNAME=' . config('database.connections.mysql.username'),
                "DB_USERNAME=$username",
                $path,
            ));

            file_put_contents($path, str_replace('DB_PASSWORD=' . config('database.connections.mysql.password'),
                'DB_PASSWORD=' . $request->input('password', ''),
                $path,
            ));

            Artisan::call('config:clear');

            DB::connection()->getPdo();

            return $this->successResponse(
                trans('errors.' .ResponseError::NO_ERROR, [], $this->language), true
            );
        } catch (Exception $e) {
            $this->error($e);
            return $this->successResponse($e->getMessage());
        }

    }

    public function migrationRun(): JsonResponse
    {
        $result = Artisan::call('migrate:fresh --seed --force');

        if (!$result) {
            return $this->successResponse(
                trans('errors.' . ResponseError::NO_ERROR, [], $this->language),
                $result
            );
        }

        return $this->onErrorResponse(['code' => ResponseError::ERROR_501]);
    }

    public function createAdmin(UserUpdateRequest $request): JsonResponse
    {
        $admin = User::orderBy('id')->first();

        if ($admin) {
            $token = $admin->createToken('api_token')->plainTextToken;

            return $this->successResponse('User successfully login', [
                'access_token'  => $token,
                'token_type'    => 'Bearer',
                'user'          => UserResource::make($admin),
            ]);
        }

        $validated = $request->validated();
        $validated['role'] = 'admin';

        $result = (new UserService)->create($validated);

        /** @var User $admin */
        $admin = User::orderBy('id')->first();

        if (!data_get($result, 'status') && !$admin) {
            return $this->onErrorResponse(['code' => ResponseError::ERROR_501]);
        }

        (new UserVerifyService)->verifyEmail($admin);

        $token = $admin->createToken('api_token')->plainTextToken;

        return $this->successResponse('User successfully login', [
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'user'          => UserResource::make($admin),
        ]);
    }

    public function licenceCredentials(Request $request): JsonResponse
    {
        $purchaseId   = $request->input('purchase_id');
        $purchaseCode = $request->input('purchase_code');

        File::put(config_path('credential.php'),
            "<?php\n return [
                        \n'purchase_id' => '$purchaseId',
                            \n'purchase_code' => '$purchaseCode',
                        \n];"
        );

        $response = json_decode((new ProjectService)->activationKeyCheck($purchaseCode, $purchaseId));

        if (
            data_get($response, 'key') == config('credential.purchase_code') &&
            data_get($response, 'active')
        ) {
            return $this->successResponse(
                trans('errors.' .ResponseError::NO_ERROR, [], $this->language),
                $response
            );
        }

        return $this->onErrorResponse([
            'code'    => ResponseError::ERROR_403,
            'message' => __('errors.' . ResponseError::ERROR_403, locale: $this->language),
            'http'    => Response::HTTP_FORBIDDEN,
        ]);
    }

}
