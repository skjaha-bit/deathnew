<?php

namespace Modules\CommercialBank\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\BondType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class BondTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = BondType::class;

    protected string $viewPrefix = 'commercialbank::admin.bond-types.index';

    protected string $routeName = 'admin.bond-types';
}
