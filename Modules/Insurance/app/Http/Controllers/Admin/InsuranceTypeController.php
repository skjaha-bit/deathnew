<?php

namespace Modules\Insurance\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\Insurance\Models\InsuranceType;

class InsuranceTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = InsuranceType::class;

    protected string $viewPrefix = 'insurance::admin.insurance-types.index';

    protected string $routeName = 'admin.insurance-types';
}
