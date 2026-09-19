<?php

namespace Modules\Insurance\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\Insurance\Models\InsuranceCompany;

class InsuranceCompanyController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = InsuranceCompany::class;

    protected string $viewPrefix = 'insurance::admin.insurance-companies.index';

    protected string $routeName = 'admin.insurance-companies';
}
