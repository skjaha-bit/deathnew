<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Relatives\Models\Relative;

class RelativeController extends Controller
{
    public function index(Request $request)
    {
        $relatives = Relative::query()
            ->with('user:id,name,email')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->query('search').'%';
                // relatives.email is encrypted at rest and can't be LIKE-searched; match the owning user (exact email) instead.
                $q->where('name', 'like', $term)
                    ->orWhereHas('user', fn ($u) => $u->whereEmail($request->query('search')));
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('invite_status', $request->query('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::relatives.index', compact('relatives'));
    }

    public function show(Relative $relative)
    {
        $relative->load('user');

        return view('admin::relatives.show', compact('relative'));
    }

    public function destroy(Relative $relative)
    {
        $relative->delete();

        return back()->with('status', 'Relative entry removed.');
    }
}
