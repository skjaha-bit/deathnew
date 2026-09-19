<?php

namespace Modules\UserSubscriptions\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\UserSubscriptions\Models\SubscriptionType;

class SubscriptionTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = SubscriptionType::class;

    protected string $viewPrefix = 'usersubscriptions::admin.subscription-types.index';

    protected string $routeName = 'admin.subscription-types';
}
