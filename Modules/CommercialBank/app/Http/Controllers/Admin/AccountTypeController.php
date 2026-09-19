<?php

namespace Modules\CommercialBank\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\AccountType;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;

class AccountTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = AccountType::class;

    protected string $viewPrefix = 'commercialbank::admin.account-types.index';

    protected string $routeName = 'admin.account-types';
}
