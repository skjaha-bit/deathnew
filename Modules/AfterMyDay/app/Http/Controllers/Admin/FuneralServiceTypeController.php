<?php

namespace Modules\AfterMyDay\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\AfterMyDay\Models\FuneralServiceType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class FuneralServiceTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = FuneralServiceType::class;

    protected string $viewPrefix = 'aftermyday::admin.funeral-service-types.index';

    protected string $routeName = 'admin.funeral-service-types';
}
