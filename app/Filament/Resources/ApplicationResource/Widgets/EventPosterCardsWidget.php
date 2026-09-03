<?php

namespace App\Filament\Resources\ApplicationResource\Widgets;

use App\Models\Event;
use Filament\Widgets\Widget;

class EventPosterCardsWidget extends Widget
{
    protected static string $view = 'filament.resources.application.widgets.event-poster-cards-widget';

    protected int | string | array $columnSpan = 'full';

    public function getEvents()
    {
        return Event::where('status', 'pendaftaran_dibuka')
            ->latest()
            ->take(3)
            ->get();
    }
}
