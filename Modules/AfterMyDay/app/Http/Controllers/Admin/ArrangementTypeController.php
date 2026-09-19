<?php

namespace Modules\AfterMyDay\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\AfterMyDay\Models\ArrangementType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class ArrangementTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = ArrangementType::class;

    protected string $viewPrefix = 'aftermyday::admin.arrangement-types.index';

    protected string $routeName = 'admin.arrangement-types';
}
