<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'icon_color', // Use underscores for database column naming conventions
        'icon_class',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the Bootstrap background class for the service icon.
     */
    public function getIconColor()
    {
        // 1. Check if a specific color is stored in the database
        if (! empty($this->icon_color)) {
            return $this->icon_color;
        }

        // 2. Fallback logic based on the service name (matches your image colors)
        return match ($this->name) {
            'Fasaxa Dhismaha' => 'bg-success',
            'Diiwaangelinta Dhismeyaasha Wadagga ah' => 'bg-primary',
            'Ruqsadda Ganacsiga', 'Bixinta Ruqsadda Ka Ganacsiga Dhismo Wadaagga' => 'bg-info',
            'Kala Wareejinta' => 'bg-dark',
            default => 'bg-primary',
        };
    }
}
