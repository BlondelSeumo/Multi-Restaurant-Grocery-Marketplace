<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Http\Requests\FilterParamsRequest;
use App\Repositories\StoryRepository\StoryRepository;
use Illuminate\Support\Facades\Artisan;

class StoryController extends RestBaseController
{
    /**
     * @param FilterParamsRequest $request
     * @return array
     */
    public function paginate(FilterParamsRequest $request): array
    {
        Artisan::call('remove:expired:stories');

        return (new StoryRepository)->list($request->all());
    }

}
