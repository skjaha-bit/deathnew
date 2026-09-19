<?php

namespace Modules\Property\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Admin\Concerns\ManagesSimpleLookup;
use Modules\Property\Models\PropertyType;

class PropertyTypeController extends Controller
{
    use ManagesSimpleLookup;

    protected string $model = PropertyType::class;

    protected string $viewPrefix = 'property::admin.property-types.index';

    protected string $routeName = 'admin.property-types';
}
