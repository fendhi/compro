<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityWithIp;
use Spatie\Activitylog\LogOptions;

class Kategori extends Model
{
    use HasFactory, LogsActivityWithIp;

    protected $fillable = ['nama', 'deskripsi'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'deskripsi'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Kategori baru ditambahkan',
                'updated' => 'Kategori diperbarui',
                'deleted' => 'Kategori dihapus',
                default => "Kategori {$eventName}"
            });
    }
}
