<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * عرض قائمة المقالات
     */
    public function index(Request $request)
    {
        // استعلام أساسي للمقالات
        $query = Article::with(['category', 'author', 'tags'])
                        ->where('status', 'published');

        // البحث
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%");
            });
        }

        // تصفية حسب التصنيف
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
            $categoryName = Category::find($request->category)->name ?? '';
        }

        // تصفية حسب الوسم
        if ($request->has('tag') && !empty($request->tag)) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
            $tagName = Tag::find($request->tag)->name ?? '';
        }

        // الترتيب
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'popular':
                    $query->orderBy('views_count', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        // الحصول على المقالات مع ترقيم الصفحات
        $articles = $query->paginate(10);

        // الحصول على المقالات المميزة
        $featuredArticles = Article::with(['category', 'author'])
                                  ->where('status', 'published')
                                  ->where('is_featured', true)
                                  ->latest()
                                  ->take(4)
                                  ->get();

        // الحصول على المقالات الشائعة للشريط الجانبي
        $popularArticles = Article::with(['category', 'author'])
                                 ->where('status', 'published')
                                 ->orderBy('views_count', 'desc')
                                 ->take(5)
                                 ->get();

        // الحصول على جميع التصنيفات مع عدد المقالات
        $categories = Category::withCount(['articles' => function($query) {
                                    $query->where('status', 'published');
                                }])
                              ->orderBy('name')
                              ->get();

        // الحصول على جميع الوسوم
        $tags = Tag::withCount(['articles' => function($query) {
                            $query->where('status', 'published');
                        }])
                  ->orderBy('articles_count', 'desc')
                  ->take(15)
                  ->get();

        return view('blog.index', compact(
            'articles', 
            'featuredArticles', 
            'popularArticles', 
            'categories', 
            'tags',
            'categoryName',
            'tagName'
        ));
    }

    /**
     * عرض مقال محدد
     */
    public function show($slug)
    {
        $article = Article::with(['category', 'author', 'tags', 'comments.user', 'comments.replies.user'])
                         ->where('slug', $slug)
                         ->where('status', 'published')
                         ->firstOrFail();

        // زيادة عدد المشاهدات
        $article->increment('views_count');

        // الحصول على المقالات ذات الصلة
        $relatedArticles = Article::with(['category', 'author'])
                                 ->where('id', '!=', $article->id)
                                 ->where('status', 'published')
                                 ->where(function($query) use ($article) {
                                     $query->where('category_id', $article->category_id)
                                           ->orWhereHas('tags', function($q) use ($article) {
                                               $q->whereIn('tags.id', $article->tags->pluck('id'));
                                           });
                                 })
                                 ->inRandomOrder()
                                 ->take(3)
                                 ->get();

        return view('blog.show', compact('article', 'relatedArticles'));
    }

    /**
     * إضافة تعليق على مقال
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|min:3|max:1000',
        ]);

        $article = Article::findOrFail($id);

        $comment = $article->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'تم إضافة تعليقك بنجاح');
    }

    /**
     * إضافة رد على تعليق
     */
    public function reply(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|min:3|max:1000',
        ]);

        $reply = \App\Models\CommentReply::create([
            'comment_id' => $commentId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'تم إضافة ردك بنجاح');
    }
}
