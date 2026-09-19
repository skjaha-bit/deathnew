<?php

namespace Modules\AfterMyDay\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\AfterMyDay\Models\CeremonyType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class CeremonyTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = CeremonyType::class;

    protected string $viewPrefix = 'aftermyday::admin.ceremony-types.index';

    protected string $routeName = 'admin.ceremony-types';
}
