<?php

namespace Modules\CommercialBank\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\LoanType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class LoanTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = LoanType::class;

    protected string $viewPrefix = 'commercialbank::admin.loan-types.index';

    protected string $routeName = 'admin.loan-types';
}
