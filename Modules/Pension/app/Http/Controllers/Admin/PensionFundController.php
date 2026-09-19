<?php

namespace Modules\Pension\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\Pension\Models\PensionFund;

class PensionFundController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = PensionFund::class;

    protected string $viewPrefix = 'pension::admin.pension-funds.index';

    protected string $routeName = 'admin.pension-funds';

    protected array $extraFields = [
        'email' => ['nullable', 'email', 'max:255'],
        'category' => ['nullable', 'string', 'max:120'],
    ];

    protected array $extraColumns = [
        'email' => 'Email',
        'category' => 'Category',
    ];
}
