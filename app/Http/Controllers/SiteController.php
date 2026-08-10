<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\OutboundClick;
use App\Models\QuickLink;
use App\Models\SearchEvent;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('home', ['categories' => ServiceCategory::withCount(['services' => fn ($q) => $q->published()])->orderBy('sort_order')->get(), 'services' => Service::published()->with('category')->where('is_featured', true)->latest()->take(6)->get(), 'articles' => Article::published()->latest('published_at')->take(3)->get(), 'faqs' => Faq::published()->where('is_featured', true)->orderBy('sort_order')->take(6)->get(), 'links' => QuickLink::where('is_published', true)->orderBy('sort_order')->get(), 'contacts' => Contact::where('is_published', true)->orderBy('sort_order')->get()]);
    }

    public function services(Request $r): View
    {
        $q = Service::published()->with('category');
        if ($r->filled('category')) {
            $q->whereHas('category', fn ($x) => $x->where('slug', $r->category));
        } if ($r->filled('audience')) {
            $q->where('audience', 'like', '%'.$r->audience.'%');
        }

        return view('services.index', ['services' => $q->latest()->paginate(9)->withQueryString(), 'categories' => ServiceCategory::orderBy('sort_order')->get()]);
    }

    public function service(Service $service): View
    {
        abort_unless($service->is_published, 404);

        return view('services.show', ['service' => $service->load('category'), 'related' => Service::published()->where('service_category_id', $service->service_category_id)->whereKeyNot($service->id)->take(3)->get()]);
    }

    public function articles(): View
    {
        return view('articles.index', ['articles' => Article::published()->latest('published_at')->paginate(9)]);
    }

    public function article(Article $article): View
    {
        abort_unless($article->is_published, 404);

        return view('articles.show', compact('article'));
    }

    public function faqs(Request $r): View
    {
        $q = Faq::published();
        if ($r->filled('q')) {
            $q->where(fn ($x) => $x->where('question', 'like', '%'.$r->q.'%')->orWhere('answer', 'like', '%'.$r->q.'%'));
        }

        return view('faqs', ['faqs' => $q->orderBy('sort_order')->paginate(15)->withQueryString()]);
    }

    public function contact(): View
    {
        return view('contact', ['contacts' => Contact::where('is_published', true)->orderBy('sort_order')->get()]);
    }

    public function search(Request $r): View
    {
        $term = trim((string) $r->q);
        $services = collect();
        $articles = collect();
        $faqs = collect();
        if (mb_strlen($term) >= 2) {
            $like = '%'.$term.'%';
            $services = Service::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('summary', 'like', $like)->orWhere('keywords', 'like', $like))->take(20)->get();
            $articles = Article::published()->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('keywords', 'like', $like))->take(20)->get();
            $faqs = Faq::published()->where(fn ($q) => $q->where('question', 'like', $like)->orWhere('answer', 'like', $like))->take(20)->get();
            SearchEvent::create(['query' => $term, 'result_count' => $services->count() + $articles->count() + $faqs->count(), 'ip_hash' => hash('sha256', (string) $r->ip())]);
        }

        return view('search', compact('term', 'services', 'articles', 'faqs'));
    }

    public function track(Request $r): JsonResponse
    {
        $data = $r->validate(['label' => 'required|string|max:255', 'url' => 'required|url|max:2048', 'source' => 'nullable|string|max:255']);
        OutboundClick::create($data + ['ip_hash' => hash('sha256', (string) $r->ip())]);

        return response()->json(['ok' => true]);
    }
}
