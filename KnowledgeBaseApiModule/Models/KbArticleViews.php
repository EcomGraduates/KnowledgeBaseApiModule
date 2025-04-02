<?php

namespace Modules\KnowledgeBaseApiModule\Models;

use Illuminate\Database\Eloquent\Model;

class KbArticleViews extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id', 'category_id', 'mailbox_id', 'view_count'
    ];

    /**
     * Increment the view count for an article.
     *
     * @param int $articleId
     * @param int $categoryId
     * @param int $mailboxId
     * @return void
     */
    public static function incrementViews($articleId, $categoryId, $mailboxId)
    {
        $record = self::firstOrNew([
            'article_id' => $articleId,
            'category_id' => $categoryId,
            'mailbox_id' => $mailboxId
        ]);

        $record->view_count = ($record->view_count ?? 0) + 1;
        $record->save();
    }

    /**
     * Get top viewed articles overall or for a specific mailbox.
     *
     * @param int $mailboxId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopArticles($mailboxId = null, $limit = 10)
    {
        $query = self::orderBy('view_count', 'desc');
        
        if ($mailboxId) {
            $query = $query->where('mailbox_id', $mailboxId);
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Get top viewed articles for a specific category.
     *
     * @param int $categoryId
     * @param int $mailboxId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopArticlesByCategory($categoryId, $mailboxId = null, $limit = 10)
    {
        $query = self::where('category_id', $categoryId)
            ->orderBy('view_count', 'desc');
            
        if ($mailboxId) {
            $query = $query->where('mailbox_id', $mailboxId);
        }
        
        return $query->limit($limit)->get();
    }
} 