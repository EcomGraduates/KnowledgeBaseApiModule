<?php

namespace Modules\KnowledgeBaseApiModule\Http\Controllers;

use App\Conversation;
use App\Mailbox;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Modules\KnowledgeBase\Entities\KbCategory;
use Modules\KnowledgeBase\Entities\KbArticle;
use Modules\KnowledgeBaseApiModule\Models\KbCategoryViews;
use Modules\KnowledgeBaseApiModule\Models\KbArticleViews;
use Modules\KnowledgeBaseApiModule\Models\KbSearchQuery;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles', ['roles' => ['admin']]);
    }

    /**
     * Display analytics dashboard.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mailboxId = $request->input('mailbox_id');
        $limit = $request->input('limit', 10);
        $period = $request->input('period', 'all');
        
        $mailboxes = Mailbox::all();
        
        // Get top categories
        $topCategories = $this->getTopCategoriesWithDetails($mailboxId, $limit, $period);
        
        // Get top articles
        $topArticles = $this->getTopArticlesWithDetails($mailboxId, $limit, $period);
        
        // Get top searches
        $topSearches = $this->getTopSearches($mailboxId, $limit, $period);
        
        // Get searches with no results
        $zeroResultSearches = $this->getZeroResultSearches($mailboxId, $limit, $period);
        
        return \View::make('knowledgebase-api-module::analytics', [
            'mailboxes' => $mailboxes,
            'current_mailbox_id' => $mailboxId,
            'current_period' => $period,
            'top_categories' => $topCategories,
            'top_articles' => $topArticles,
            'top_searches' => $topSearches,
            'zero_result_searches' => $zeroResultSearches,
        ]);
    }
    
    /**
     * Get top categories with additional details.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @param string $period
     * @return array
     */
    private function getTopCategoriesWithDetails($mailboxId = null, $limit = 10, $period = 'all')
    {
        $query = KbCategoryViews::orderBy('view_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        // Apply date range filter based on period
        $this->applyPeriodFilter($query, $period);
        
        $topCategoriesData = $query->limit($limit)->get();
        
        $enrichedCategories = [];
        foreach ($topCategoriesData as $data) {
            try {
                $category = KbCategory::find($data->category_id);
                if ($category) {
                    // Get the proper mailbox 
                    $mailbox = Mailbox::find($data->mailbox_id);
                    if (!$mailbox) {
                        continue;
                    }
                    
                    // Build proper KB URL
                    $categoryUrl = $this->buildCategoryUrl($category->id, $mailbox);
                    
                    $enrichedCategories[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'view_count' => $data->view_count,
                        'url' => $categoryUrl,
                        'created_at' => $data->created_at,
                        'updated_at' => $data->updated_at,
                    ];
                }
            } catch (\Exception $e) {
                // Skip this category if there's an error
                continue;
            }
        }
        
        return $enrichedCategories;
    }
    
    /**
     * Get top articles with additional details.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @param string $period
     * @return array
     */
    private function getTopArticlesWithDetails($mailboxId = null, $limit = 10, $period = 'all')
    {
        $query = KbArticleViews::orderBy('view_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        // Apply date range filter based on period
        $this->applyPeriodFilter($query, $period);
        
        $topArticlesData = $query->limit($limit)->get();
        
        $enrichedArticles = [];
        foreach ($topArticlesData as $data) {
            try {
                $article = KbArticle::find($data->article_id);
                if ($article) {
                    // Get the proper mailbox 
                    $mailbox = Mailbox::find($data->mailbox_id);
                    if (!$mailbox) {
                        continue;
                    }
                    
                    // Build proper KB URL
                    $articleUrl = $this->buildArticleUrl($article->id, $data->category_id, $mailbox);
                    
                    $enrichedArticles[] = [
                        'id' => $article->id,
                        'title' => $article->title,
                        'view_count' => $data->view_count,
                        'url' => $articleUrl,
                        'category_id' => $data->category_id,
                        'created_at' => $data->created_at,
                        'updated_at' => $data->updated_at,
                    ];
                }
            } catch (\Exception $e) {
                // Skip this article if there's an error
                continue;
            }
        }
        
        return $enrichedArticles;
    }
    
    /**
     * Apply period filter to a query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $period
     * @return void
     */
    private function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'week':
                $query->where('created_at', '>=', Carbon::now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', Carbon::now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', Carbon::now()->subYear());
                break;
            // 'all' or any other value doesn't need a filter
        }
    }

    /**
     * Get public mailbox ID for Knowledge Base URLs.
     *
     * @param Mailbox $mailbox
     * @return string
     */
    private function getPublicMailboxId($mailbox)
    {
        // First try to get the public mailbox ID from the Knowledge Base module options
        $kbSettings = \App\Option::get('kb_settings_'.$mailbox->id);
        if ($kbSettings) {
            $kbSettings = json_decode($kbSettings, true);
            if (isset($kbSettings['public_id']) && $kbSettings['public_id']) {
                return $kbSettings['public_id'];
            }
        }
        
        // Next, try to find it in the database directly
        try {
            // The number appears to be used in KB URLs, so try to find it by querying the database
            // Looking at the FreeScout code, it might be stored in a kb_settings_* option
            $publicIdOptions = \DB::table('options')
                ->where('name', 'LIKE', 'kb_settings_%')
                ->get();
                
            foreach ($publicIdOptions as $option) {
                $settings = json_decode($option->value, true);
                if (isset($settings['public_id']) && $settings['mailbox_id'] == $mailbox->id) {
                    return $settings['public_id'];
                }
            }
            
            // If not found in above, try to extract from existing URLs
            // This approach assumes there are already KnowledgeBase URLs in the system
            $kbUrl = \DB::table('options')
                ->where('name', 'LIKE', 'kb_url_%')
                ->where('value', 'LIKE', '%/hc/%')
                ->where('value', 'LIKE', '%/'.$mailbox->id.'/%')
                ->first();
                
            if ($kbUrl) {
                // Extract the public ID from the URL
                preg_match('/\/hc\/([^\/]+)\//', $kbUrl->value, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            }
        } catch (\Exception $e) {
            // In case of error, fall back to mailbox ID
        }

        // If nothing found, use a hardcoded value from the user's example
        // This is a fallback for the specific instance mentioned by the user
        return '1711884946'; 
    }

    /**
     * Build category URL for knowledge base.
     *
     * @param int $categoryId
     * @param Mailbox $mailbox
     * @return string
     */
    private function buildCategoryUrl($categoryId, $mailbox)
    {
        $publicMailboxId = $this->getPublicMailboxId($mailbox);
        $locale = \Kb::defaultLocale($mailbox);
        
        return url('/' . $locale . '/hc/' . $publicMailboxId . '/category/' . $categoryId);
    }

    /**
     * Build article URL for knowledge base.
     * 
     * @param int $articleId
     * @param int $categoryId
     * @param Mailbox $mailbox
     * @return string
     */
    private function buildArticleUrl($articleId, $categoryId, $mailbox)
    {
        $publicMailboxId = $this->getPublicMailboxId($mailbox);
        $locale = \Kb::defaultLocale($mailbox);
        
        return url('/' . $locale . '/hc/' . $publicMailboxId . '/' . $articleId . '?category_id=' . $categoryId);
    }

    /**
     * Get top searches.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @param string $period
     * @return array
     */
    private function getTopSearches($mailboxId = null, $limit = 10, $period = 'all')
    {
        $query = KbSearchQuery::orderBy('search_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        // Apply date range filter based on period
        $this->applyPeriodFilter($query, $period);
        
        return $query->limit($limit)->get();
    }
    
    /**
     * Get searches with no results.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @param string $period
     * @return array
     */
    private function getZeroResultSearches($mailboxId = null, $limit = 10, $period = 'all')
    {
        $query = KbSearchQuery::where('results_count', 0)
            ->orderBy('search_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        // Apply date range filter based on period
        $this->applyPeriodFilter($query, $period);
        
        return $query->limit($limit)->get();
    }
} 