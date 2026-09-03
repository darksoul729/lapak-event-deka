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

    public function getEventPoster(Event $event): string
    {
        if ($event->poster_path) {
            return \Illuminate\Support\Facades\Storage::url($event->poster_path);
        }

        $name = strtolower($event->nama_event);

        if (str_contains($name, 'culinary') || str_contains($name, 'kuliner') || str_contains($name, 'food')) {
            return 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&h=338&q=80';
        }

        if (str_contains($name, 'fashion') || str_contains($name, 'kriya') || str_contains($name, 'craft') || str_contains($name, 'expo')) {
            return 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=600&h=338&q=80';
        }

        return 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=600&h=338&q=80';
    }

    public function getEventBadge(Event $event): array
    {
        $name = strtolower($event->nama_event);

        if (str_contains($name, 'culinary') || str_contains($name, 'kuliner') || str_contains($name, 'food')) {
            return ['label' => 'Kuliner & Food Fest', 'bg' => 'bg-amber-500 text-gray-950', 'icon' => 'heroicon-m-fire'];
        }

        if (str_contains($name, 'fashion') || str_contains($name, 'kriya') || str_contains($name, 'craft') || str_contains($name, 'expo')) {
            return ['label' => 'Fashion & Kriya Expo', 'bg' => 'bg-purple-600 text-white', 'icon' => 'heroicon-m-sparkles'];
        }

        return ['label' => 'Youth Art & Creative', 'bg' => 'bg-teal-600 text-white', 'icon' => 'heroicon-m-bolt'];
    }
}
