<?php

namespace Modules\KnowledgeBaseApiModule\Models;

use Illuminate\Database\Eloquent\Model;

class KbSearchQuery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mailbox_id', 'query', 'results_count', 'search_count', 'locale'
    ];

    /**
     * Track a search query.
     *
     * @param int $mailboxId
     * @param string $query
     * @param int $resultsCount
     * @param string|null $locale
     * @return void
     */
    public static function trackSearch($mailboxId, $query, $resultsCount, $locale = null)
    {
        // Normalize the query (lowercase, trim)
        $normalizedQuery = mb_strtolower(trim($query));
        
        // Find existing record or create new one
        $searchRecord = self::where('mailbox_id', $mailboxId)
            ->where('query', $normalizedQuery)
            ->first();
            
        if ($searchRecord) {
            // Update existing record
            $searchRecord->search_count += 1;
            $searchRecord->results_count = $resultsCount; // Update with latest result count
            $searchRecord->locale = $locale; // Update locale if provided
            $searchRecord->save();
        } else {
            // Create new record
            self::create([
                'mailbox_id' => $mailboxId,
                'query' => $normalizedQuery,
                'results_count' => $resultsCount,
                'search_count' => 1,
                'locale' => $locale
            ]);
        }
    }

    /**
     * Get top searches.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopSearches($mailboxId = null, $limit = 10)
    {
        $query = self::orderBy('search_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Get searches with no results.
     *
     * @param int|null $mailboxId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getZeroResultSearches($mailboxId = null, $limit = 10)
    {
        $query = self::where('results_count', 0)
            ->orderBy('search_count', 'desc');
        
        if ($mailboxId) {
            $query->where('mailbox_id', $mailboxId);
        }
        
        return $query->limit($limit)->get();
    }
} 