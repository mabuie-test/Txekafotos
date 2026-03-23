<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class MarketingBanner extends Model
{
    protected string $table = 'marketing_banners';

    public function active(): array
    {
        return $this->db()->fetchAll('SELECT * FROM marketing_banners WHERE is_active = 1 ORDER BY created_at DESC');
    }
}
