<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_room_id', 'sender_type', 'message', 'file', 'is_read'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['file_url', 'media_type','file_type'];

    /**
     * chat room associated with chat message
     */
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Get the timezone wise date
     * 
     * @param $createdAt date
     * @param $timezone timezone
     * @return formated time
     */
    public static function getTimezoneDate($createdAt, $timezone)
    {
        if ($createdAt) {
            $createdAt = new Carbon($createdAt);
            $currentTime = CommonHelper::convertDateToSpecificTimezone($createdAt, $timezone);

            return $currentTime->format('h:i A');
        }
    }

    /**
     * Get the formatted time
     */
    // public function getTimeAttribute()
    // {
    //     if (!empty($this->attributes['created_at'])) {

    //         return self::getTimezoneDate($this->attributes['created_at'], $this->chatRoom->ticket->timezone->timezone);
    //     }

    //     return '';
    // }

    /**
     * Upload the file
     *
     * @param  string  $value image instase
     * @return string image hase name
     */
    public function setFileAttribute($value)
    {
        $imageName = '';
        if (!empty($value)) {
            @list($type, $file_data) = explode(';', $value);
            @list(, $file_data) = explode(',', $file_data);
            $extension = explode('/', $type);
            if (($extension[1] == 'msword')) {
                $extension[1] = 'doc';
            }
            if (($extension[1] == 'vnd.openxmlformats-officedocument.wordprocessingml.document')) {
                $extension[1] = 'docx';
            }
            $imageName = time() . rand(1111, 9999) . '.' . $extension[1];
            Storage::disk(env('STORAGE_DISK'))->put(config('constants.IMAGE_PATH.CHAT_IMAGE') . '/' . $imageName, base64_decode($file_data));
        }

        $this->attributes['file'] = !empty($imageName) ? $imageName : null;
    }

    /**
     * Get the File URL
     *
     * @return string file path
     */
    public function getFileAttribute()
    {
        return !empty($this->attributes['file']) ? config('constants.IMAGE_PATH.CHAT_IMAGE') . '/' . $this->attributes['file'] : '';
    }

    /**
     * Get the file URL
     *
     * @return string file path
     */
    public function getFileUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getFileAttribute());

        return !empty($imageUrl) ? url($imageUrl) : '';
    }

    /**
     * Check the image type and set the image
     */
    public function getFileTypeAttribute()
    {
        $img = '';
        if (isset($this->attributes['file'])) {
            $file = explode('.', $this->attributes['file']);
            switch ($file[1]) {
                case 'pdf':
                    $img = url('media/svg/files/pdf.svg');
                    break;
                case 'doc':
                    $img = url('media/svg/files/doc.svg');
                    break;
                case 'docx':
                    $img = url('media/svg/files/doc.svg');
                    break;
                default:
                    $img = $this->getFileUrlAttribute();
                    break;
            }
        }

        return $img;
    }


    /**
     * Get the media type
     * 
     * @return int
     */
    public function getMediaTypeAttribute()
    {
        $type = config('constants.CHAT_MEDIA_TYPE.TEXT');
        if (isset($this->attributes['file'])) {
            $file = explode('.', $this->attributes['file']);
            switch ($file[1]) {
                case 'pdf':
                    $type = config('constants.CHAT_MEDIA_TYPE.PDF');
                    break;
                case 'doc':
                    $type = config('constants.CHAT_MEDIA_TYPE.DOC');
                    break;
                case 'docx':
                    $type = config('constants.CHAT_MEDIA_TYPE.DOC');
                    break;
                default:
                    $type = config('constants.CHAT_MEDIA_TYPE.IMAGE');
                    break;
            }
        }

        return $type;
    }
}
