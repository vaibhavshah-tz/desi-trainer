<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'subject', 'slug', 'body', 'keywords', 'status' 
    ];

    /**
     * Set serialize keywords
     *
     * @param  string  $value keywords array
     * @return string serialized keywords
     */
    public function setKeywordsAttribute($value)
    {
        if(is_array($value)) {
            foreach($value as $key => $val) {
                if(empty($val['key']) || empty($val['description'])){
                    unset($value[$key]);
                }
            }
        }
        $this->attributes['keywords'] = serialize($value);
    }

    /**
     * Get the unserialized keywords
     *
     * @return array
     */
    public function getKeywordsAttribute()
    {
        return unserialize($this->attributes['keywords']);
    }
}
