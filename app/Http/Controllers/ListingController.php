<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class ListingController extends Controller
{
    /**
     * todo migrate to fe controller with mongodb
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = Listing::query()
            ->where('is_active', true)
            ->with('tags')
            ->latest();
        if ($request->has('search')) {
            $search = trim($request->get('search'));
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
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

    /**
     * todo migrate to fe controller with mongodb
     * @param Listing $listing
     * @param Request $request
     * @return Response
     */
    public function show(Listing $listing, Request $request): Response
    {
        /** @noinspection MissedFieldInspection */
        return Inertia::render('Listing/Show', [
            'listing' => $listing
        ]);
    }

    /**
     * todo migrate to fe controller
     * @return Response
     */
    public function create(): Response
    {
        /** @noinspection MissedFieldInspection */
        return Inertia::render('Admin/Listing/Create', [
            'stripe_key' => env('STRIPE_KEY')
        ]);
    }

    /**
     * @param Listing $listing
     * @param Request $request
     * @return RedirectResponse
     */
    public function apply(Listing $listing, Request $request): RedirectResponse
    {
        $listing->clicks()
            ->create([
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

        return redirect()->to($listing->link);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validation = [
            'title' => 'required',
            'company' => 'required',
            //'logo' => 'file|max:2048', //todo
            'location' => 'required',
            'link' => 'required|url',
            'content' => 'required',
            //'payment_method_id' => 'required' //todo
        ];
        // is a user signed in? if not, create one and authenticate
        if (!Auth::check()) {
            $validation = array_merge($validation, [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:5',
                'name' => 'required'
            ]);
        }
        $request->validate($validation);
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
            foreach (explode(',', $request->tags) as $request_tag) {
                $tag = Tag::firstOrCreate([
                    'slug' => Str::slug(trim($request_tag))
                ], [
                    'name' => ucwords(trim($request_tag))
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

    /**
     * @param Request $request
     * @return Response
     */
    public function dashboard(Request $request): Response
    {
        $query = Listing::query()
            ->with('tags')
            ->latest();
        if ($request->has('search')) {
            $search = trim($request->get('search'));
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        /** @noinspection MissedFieldInspection */
        return Inertia::render('Admin/Listing/List', [
            'listings' => $query->get()->toArray(),
            'search' => $request->get('search'),
        ]);
    }

    /**
     * @param Listing $listing
     * @param Request $request
     * @return Response
     */
    public function edit(Listing $listing, Request $request): Response
    {
        /** @noinspection MissedFieldInspection */
        return Inertia::render('Admin/Listing/Edit', [
            'listing' => $listing
        ]);
    }

    /**
     * @param Listing $listing
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Listing $listing, Request $request): RedirectResponse
    {
        $validation = [
            'title' => 'required',
            'company' => 'required',
            //'logo' => 'file|max:2048', //todo
            'location' => 'required',
            'link' => 'required|url',
            'content' => 'required',
        ];
        $request->validate($validation);
        try {
            $md = new \ParsedownExtra();
            $listing->update([
                'title' => $request->title,
                'slug' => $request->title === $listing->title ? $listing->title : Str::slug($request->title) . '-' . random_int(1111, 9999),
                'company' => $request->company,
                //'logo' => basename($request->file('logo')->store('public')), // todo
                'location' => $request->location,
                'link' => $request->link,
                'content' => $md->text($request->input('content')),
                'is_highlighted' => $request->filled('is_highlighted'),
                'is_active' => $request->is_active,
            ]);
            /** @var Tag[] $existing_tags */
            $existing_tags = $listing->tags()->get()->keyBy('name')->all();
            $existing_tag_names = array_keys($existing_tags);
            $updated_tag_names = explode(',', trim($request->tags, ','));
            if (!reset($updated_tag_names) && $existing_tag_names) {
                $listing->tags()->detach();
            } else {
                // remove
                $removed_tags = array_diff($existing_tag_names, $updated_tag_names);
                foreach ($removed_tags as $name) {
                    $existing_tags[$name]->listings()->detach($listing->id);
                }
                // add new
                foreach ($updated_tag_names as $name) {
                    $name = trim($name);
                    if ($name && !isset($existing_tags[$name])) {
                        $tag = Tag::firstOrCreate([
                            'slug' => Str::slug($name),
                            'name' => ucwords($name)
                        ]);
                        $tag->listings()->attach($listing->id);
                    }
                }
            }

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            /** @noinspection MissedFieldInspection */
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
