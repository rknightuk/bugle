<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileLink extends Model
{
    use HasFactory;

    protected $table = 'profile_link';

    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function serialize()
    {
        return [
            'type' => 'PropertyValue',
            'name' => $this->title,
            'value' => $this->formatValue(),
        ];
    }

    public function isUrl()
    {
        return str_starts_with($this->link, 'https') || str_starts_with($this->link, 'http');
    }

    private function formatValue()
    {
        $value = $this->link;

        if ($this->isUrl())
        {
            $urlParts = parse_url($value);
            $scheme = $urlParts['scheme'] . '://';
            unset($urlParts['scheme']);
            $link = implode('', array_values($urlParts));
            return '<a href="' . $value . '" target="_blank" rel="nofollow noopener noreferrer me"><span class="invisible">' . $scheme . '</span><span class="">' . $link . '</span><span class="invisible"></span></a>';
        }

        return $value;
    }
}
