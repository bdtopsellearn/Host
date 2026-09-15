<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;

class DatacenterDistributionChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected ?string $heading = 'Datacenter Node Fleet';

    protected ?string $description = 'Active VPS distribution across global Tier-3/4 datacenter facilities.';

    protected ?string $maxHeight = '260px';

    protected ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rawDistribution = Service::selectRaw('region, count(*) as count')
            ->groupBy('region')
            ->pluck('count', 'region')
            ->toArray();

        $regionNames = [
            'EU' => '🇪🇺 Germany (EU)',
            'US-central' => '🇺🇸 US Central (STL)',
            'US-east' => '🇺🇸 US East (NYC)',
            'US-west' => '🇺🇸 US West (SEA)',
            'UK' => '🇬🇧 United Kingdom',
            'SIN' => '🇸🇬 Singapore (SIN)',
            'JPN' => '🇯🇵 Japan (Tokyo)',
            'AUS' => '🇦🇺 Australia (SYD)',
        ];

        $labels = [];
        $data = [];
        $palette = [
            '#673DE6', // Electric Royal Purple
            '#06B6D4', // Cyan
            '#10B981', // Emerald
            '#F59E0B', // Amber
            '#EC4899', // Pink
            '#8B5CF6', // Purple
            '#3B82F6', // Blue
            '#64748B', // Slate
        ];
        $backgroundColors = [];

        if (!empty($rawDistribution)) {
            $colorIndex = 0;
            foreach ($rawDistribution as $regionCode => $count) {
                $labels[] = $regionNames[$regionCode] ?? strtoupper($regionCode);
                $data[] = (int) $count;
                $backgroundColors[] = $palette[$colorIndex % count($palette)];
                $colorIndex++;
            }
        } else {
            // Default global network capability distribution
            $labels = [
                '🇪🇺 Germany (EU Central)',
                '🇺🇸 US Central (Missouri)',
                '🇺🇸 US East (New York)',
                '🇬🇧 United Kingdom',
                '🇸🇬 Singapore (APAC)',
            ];
            $data = [40, 25, 20, 10, 5];
            $backgroundColors = array_slice($palette, 0, 5);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Instances',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
