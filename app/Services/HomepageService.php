<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Feedback;
use App\Models\HomepageContent;
use App\Models\MarketingBanner;
use App\Models\Showcase;

class HomepageService
{
    public function __construct(
        private readonly HomepageContent $content = new HomepageContent(),
        private readonly MarketingBanner $banners = new MarketingBanner(),
        private readonly Showcase $showcases = new Showcase(),
        private readonly Feedback $feedbacks = new Feedback(),
    ) {
    }

    public function getHomepageData(): array
    {
        $content = $this->content->current();
        $stats = json_decode((string) ($content['stats_json'] ?? '{}'), true) ?: [];

        return [
            'content' => $content,
            'banners' => $this->banners->active(),
            'showcases' => $this->showcases->active(),
            'feedbacks' => $this->feedbacks->published(),
            'average_rating' => $this->feedbacks->average(),
            'stats' => $stats,
        ];
    }

    public function updateHomepage(array $payload): bool
    {
        $content = $this->content->current();
        return $this->content->updateContent((int) $content['id'], [
            'hero_title' => trim($payload['hero_title']),
            'hero_subtitle' => trim($payload['hero_subtitle']),
            'hero_cta_text' => trim($payload['hero_cta_text']),
            'hero_secondary_cta_text' => trim($payload['hero_secondary_cta_text']),
            'hero_badge_text' => trim($payload['hero_badge_text']),
            'section_benefits_title' => trim($payload['section_benefits_title']),
            'section_feedback_title' => trim($payload['section_feedback_title']),
            'section_showcase_title' => trim($payload['section_showcase_title']),
            'final_cta_title' => trim($payload['final_cta_title']),
            'final_cta_text' => trim($payload['final_cta_text']),
            'benefits_text' => trim($payload['benefits_text']),
            'stats_json' => json_encode($payload['stats'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
