<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class HomepageContent extends Model
{
    protected string $table = 'homepage_content';

    public function current(): ?array
    {
        return $this->db()->fetch('SELECT * FROM homepage_content ORDER BY id DESC LIMIT 1');
    }

    public function updateContent(int $id, array $data): bool
    {
        return $this->db()->execute('UPDATE homepage_content SET hero_title = :hero_title, hero_subtitle = :hero_subtitle, hero_cta_text = :hero_cta_text, hero_secondary_cta_text = :hero_secondary_cta_text, hero_badge_text = :hero_badge_text, section_benefits_title = :section_benefits_title, section_feedback_title = :section_feedback_title, section_showcase_title = :section_showcase_title, final_cta_title = :final_cta_title, final_cta_text = :final_cta_text, benefits_text = :benefits_text, stats_json = :stats_json WHERE id = :id', $data + ['id' => $id]);
    }
}
