<?php

namespace Modules\TradeUnion\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\TradeUnion\Models\TradeUnion;

class TradeUnionController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = TradeUnion::class;

    protected string $viewPrefix = 'tradeunion::admin.trade-unions.index';

    protected string $routeName = 'admin.trade-unions';
}
