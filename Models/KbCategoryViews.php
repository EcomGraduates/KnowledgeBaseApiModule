<?php

namespace Modules\KnowledgeBaseApiModule\Models;

use Illuminate\Database\Eloquent\Model;

class KbCategoryViews extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'mailbox_id', 'view_count'
    ];

    /**
     * Increment the view count for a category.
     *
     * @param int $categoryId
     * @param int $mailboxId
     * @return void
     */
    public static function incrementViews($categoryId, $mailboxId)
    {
        $record = self::firstOrNew([
            'category_id' => $categoryId,
            'mailbox_id' => $mailboxId
        ]);

        $record->view_count = ($record->view_count ?? 0) + 1;
        $record->save();
    }

    /**
     * Get top viewed categories for a mailbox.
     *
     * @param int $mailboxId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopCategories($mailboxId = null, $limit = 10)
    {
        $query = self::orderBy('view_count', 'desc');
        
        if ($mailboxId) {
            $query = $query->where('mailbox_id', $mailboxId);
        }
        
        return $query->limit($limit)->get();
    }
} 