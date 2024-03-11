<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $query = Listing::query()
            ->where('is_active', true)
            ->with('tags')
            ->latest();
        if ($request->has('search')) {
            $searchQuery = trim($request->get('search'));
            $query->where(function (Builder $builder) use ($searchQuery) {
                $builder
                    ->orWhere('title', 'like', "%{$searchQuery}%")
                    ->orWhere('company', 'like', "%{$searchQuery}%")
                    ->orWhere('location', 'like', "%{$searchQuery}%");
            });
        }
        if ($request->has('tag')) {
            $tag = $request->get('tag');
            $query->whereHas('tags', function (Builder $builder) use ($tag) {
                $builder->where('slug', $tag);
            });
        }
        $listings = $query->get();
        // include tags list
        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        /** @noinspection MissedFieldInspection */
        return Inertia::render('Listing/Index', [
            'active_tag' => $request->get('tag', ''),
            'listings' => $listings->toArray(),
            'search' => $request->get('search'),
            'tags' => $tags->toArray()
        ]);
    }

    public function show(Listing $listing, Request $request): \Inertia\Response
    {
        /** @noinspection MissedFieldInspection */
        return Inertia::render('Listing/Show', [
            'listing' => $listing
        ]);
    }

    public function apply(Listing $listing, Request $request): \Illuminate\Http\RedirectResponse
    {
        $listing->clicks()
            ->create([
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

        return redirect()->to($listing->link);
    }

    public function create(): \Inertia\Response
    {
        /** @noinspection MissedFieldInspection */
        return Inertia::render('Admin/Listing/Create', [
            'stripe_key' => env('STRIPE_KEY')
        ]);
    }

    public function store(Request $request)
    {
        // process the listing creation form
        $validationArray = [
            'title' => 'required',
            'company' => 'required',
            //'logo' => 'file|max:2048', //todo
            'location' => 'required',
            'link' => 'required|url',
            'content' => 'required',
            //'payment_method_id' => 'required' //todo
        ];
        if (!Auth::check()) {
            $validationArray = array_merge($validationArray, [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:5',
                'name' => 'required'
            ]);
        }
        $request->validate($validationArray);
        // is a user signed in? if not, create one and authenticate
        $user = Auth::user();
        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            // todo
            //$user->createAsStripeCustomer();
            Auth::login($user);
        }
        // process the payment and create the listing
        try {
            // todo
            /*$amount = 9900; // $99.00 USD in cents
            if ($request->filled('is_highlighted')) {
                $amount += 1900;
            }*/
            //$user->charge($amount, $request->payment_method_id);
            $md = new \ParsedownExtra();
            $listing = $user->listings()
                ->create([
                    'title' => $request->title,
                    'slug' => Str::slug($request->title) . '-' . random_int(1111, 9999),
                    'company' => $request->company,
                    //'logo' => basename($request->file('logo')->store('public')), // todo
                    'location' => $request->location,
                    'link' => $request->link,
                    'content' => $md->text($request->input('content')),
                    'is_highlighted' => $request->filled('is_highlighted'),
                    'is_active' => true
                ]);
            foreach (explode(',', $request->tags) as $requestTag) {
                $tag = Tag::firstOrCreate([
                    'slug' => Str::slug(trim($requestTag))
                ], [
                    'name' => ucwords(trim($requestTag))
                ]);
                $tag->listings()->attach($listing->id);
            }

            return redirect()->route('listings.index');
        } catch (\Exception $e) {

            /** @noinspection MissedFieldInspection */
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
