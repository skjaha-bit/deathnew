<?php

namespace Modules\CommercialBank\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\Bank;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class BankController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = Bank::class;

    protected string $viewPrefix = 'commercialbank::admin.banks.index';

    protected string $routeName = 'admin.banks';
}
