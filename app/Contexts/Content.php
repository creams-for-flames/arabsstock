<?php

namespace App\Contexts;

trait Content
{
    public function getReservedAttribute()
    {
        if (auth()->check() && auth()->id() == $this->reserved_to)
            return false;
        return !is_null($this->reserved_until) && now()->lt($this->reserved_until);
    }

    public static function standard_credits()
    {
        return self::section_const()->standard_credits;
    }

    public static function enhanced_credits()
    {
        return self::section_const()->enhanced_credits;
    }

    public static function exclusive_credits()
    {
        return self::section_const()->exclusive_credits;
    }

    public function can_reserve()
    {
        $type = $this->type();
        if ($this->reserved)
            return false;
        if ($this->{"contributor_{$type}_id"})
            return false;
        $item = $this;
        if (cache()->tags([$type, 'old_download', "{$type}_{$this->id}"])->remember("{$type}_{$this->id}_old_downloads", now()->addWeek(), function () use ($item) {
            return $item->old_downloads()->whereHas('plan', function ($q) {
                $q->where('free', 0);
            })->count();
        }))
            return false;
        if (cache()->tags([$type, 'download', "{$type}_{$this->id}"])->remember("{$type}_{$this->id}_old_downloads", now()->addWeek(), function () use ($item) {
            return $item->downloads()->count();
        }))
            return false;
        return true;
    }

    private function type()
    {
        return strtolower(class_basename($this));
    }

    public function scopeFromArabsstock($q)
    {
        $type = $this->type();
        $q->where("contributor_{$type}_id", '=', 0);
    }

    public function scopeFromContributors($q)
    {
        $type = $this->type();
        $q->where("contributor_{$type}_id", '!=', 0);
    }

    public function scopeCanReserve($q)
    {
        $q->active()->fromArabsstock()
            ->whereDoesnthave('downloads')->whereDoesnthave('old_downloads', function ($q) {
                $q->whereHas('plan', function ($q) {
                    $q->where('free', 0);
                });
            });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getImgCaptionAttribute()
    {
        if ($this->title) {
            $len = strlen($this->title) >= 150 ? 150 : strlen($this->title);
            return substr($this->title, 0, strpos($this->title, ' ', $len));
        }
        return '';
    }
}
