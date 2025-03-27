<?php

namespace Modules\KnowledgeBaseApiModule\Http\Controllers;

use App\Conversation;
use App\Mailbox;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Modules\KnowledgeBase\Entities\KbCategory;
use Modules\KnowledgeBase\Entities\KbArticle;
use Modules\KnowledgeBase\Entities\KbArticleKbCategory;

class KnowledgeBaseApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Token validation is handled by middleware
    }

    /**
     * Get all categories for a mailbox.
     *
     * @param Request $request
     * @param int $mailboxId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $mailboxId)
    {
        try {
            $mailbox = Mailbox::findOrFail($mailboxId);
            if ($mailbox === null) {
                return Response::json(['error' => 'Mailbox not found'], 404);
            }
            $categories = \KbCategory::getTree($mailbox->id, [], 0, true);

            $locale = $request->input('locale') ?? \Kb::defaultLocale($mailbox);

            $items = [];

            foreach ($categories as $c) {
                if (!$c->checkVisibility()) {
                    continue;
                }
                $items[] = (object)[
                    'id' => $c->id,
                    'name' => $c->getAttributeInLocale('name', $locale),
                    'description' => $c->getAttributeInLocale('description', $locale)
                ];
            }

            return Response::json([
                'mailbox_id' => $mailbox->id,
                'name' => $mailbox->name,
                'categories' => $items,
            ], 200);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a specific category with its articles.
     *
     * @param Request $request
     * @param int $mailboxId
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function category(Request $request, $mailboxId, $categoryId)
    {
        try {
            $mailbox = Mailbox::findOrFail($mailboxId);
            if ($mailbox === null) {
                return Response::json(['error' => 'Mailbox not found'], 404);
            }

            $category = KbCategory::findOrFail($categoryId);
            if (!$category->checkVisibility()) {
                $category = null;
            }
            if ($category === null) {
                return Response::json(['error' => 'Category not found or not visible'], 404);
            }
            
            $articles = [];
            if ($category) {
                $sortedArticles = $category->getArticlesSorted(true);
            }

            $locale = $request->input('locale') ?? \Kb::defaultLocale($mailbox);

            foreach ($sortedArticles as $a) {
                $a->setLocale($locale);
                $articles[] = (object)[
                    'id' => $a->id, 
                    'title' => $a->getAttributeInLocale('title', $locale), 
                    'text' => $a->getAttributeInLocale('text', $locale)
                ];
            }

            return Response::json([
                'id' => 0,
                'mailbox_id' => $mailbox->id,
                'name' => $mailbox->name,
                'category' => (object)[
                    'id' => $category->id,
                    'name' => $category->getAttributeInLocale('name', $locale),
                    'description' => $category->getAttributeInLocale('description', $locale),
                ],
                'articles' => $articles,
            ], 200);
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }
}
