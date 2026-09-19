<?php

namespace Modules\CommercialBank\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\FundType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class FundTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = FundType::class;

    protected string $viewPrefix = 'commercialbank::admin.fund-types.index';

    protected string $routeName = 'admin.fund-types';
}
