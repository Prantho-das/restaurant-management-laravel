<x-filament-widgets::widget>
    <x-filament::section>
        <div class="fi-quick-actions-container">
            @foreach($this->getActions() as $action)
                <a 
                    href="{{ $action['url'] }}" 
                    class="fi-qa-card fi-qa-color-{{ $action['color'] }}"
                >
                    <div class="fi-qa-icon-wrapper">
                        <x-filament::icon
                            :icon="$action['icon']"
                        />
                    </div>
                    
                    <div class="fi-qa-content">
                        <span class="fi-qa-label">{{ $action['label'] }}</span>
                        <span class="fi-qa-desc">{{ $action['description'] ?? '' }}</span>
                    </div>

                    <div class="fi-qa-arrow">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>

    <style>
        .fi-quick-actions-container {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        @media (min-width: 640px) {
            .fi-quick-actions-container {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .fi-quick-actions-container {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .fi-quick-actions-container {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }
        }

        .fi-qa-card {
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 1rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .dark .fi-qa-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .fi-qa-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-500, #808000);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.12);
        }

        .fi-qa-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            transition: transform 0.4s;
        }

        .fi-qa-card:hover .fi-qa-icon-wrapper {
            transform: scale(1.1) rotate(4deg);
        }

        /* Color Variants using standard palettes or primary */
        .fi-qa-color-primary .fi-qa-icon-wrapper,
        .fi-qa-color-info .fi-qa-icon-wrapper { background: #f0f9ff; color: #0284c7; }
        .dark .fi-qa-color-primary .fi-qa-icon-wrapper,
        .dark .fi-qa-color-info .fi-qa-icon-wrapper { background: rgba(2, 132, 199, 0.1); color: #38bdf8; }

        .fi-qa-color-success .fi-qa-icon-wrapper { background: #f0fdf4; color: #16a34a; }
        .dark .fi-qa-color-success .fi-qa-icon-wrapper { background: rgba(22, 163, 74, 0.1); color: #4ade80; }

        .fi-qa-color-danger .fi-qa-icon-wrapper { background: #fef2f2; color: #dc2626; }
        .dark .fi-qa-color-danger .fi-qa-icon-wrapper { background: rgba(220, 38, 38, 0.1); color: #f87171; }

        .fi-qa-color-warning .fi-qa-icon-wrapper { background: #fffbeb; color: #d97706; }
        .dark .fi-qa-color-warning .fi-qa-icon-wrapper { background: rgba(217, 119, 6, 0.1); color: #fbbf24; }
        
        /* Specific override for PRIMARY if Olive is used */
        .fi-qa-color-primary .fi-qa-icon-wrapper { background: #f5f5f0; color: #808000; }
        .dark .fi-qa-color-primary .fi-qa-icon-wrapper { background: rgba(128, 128, 0, 0.1); color: #a4a400; }

        .fi-qa-icon-wrapper svg { width: 1.25rem; height: 1.25rem; }

        .fi-qa-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.125rem;
            transition: color 0.3s;
        }

        .dark .fi-qa-label { color: #ffffff; }

        .fi-qa-card:hover .fi-qa-label { color: var(--primary-600, #808000); }

        .fi-qa-desc {
            display: block;
            font-size: 0.6875rem;
            color: #6b7280;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dark .fi-qa-desc { color: #9ca3af; }

        .fi-qa-arrow {
            position: absolute;
            bottom: 1rem;
            right: 1.25rem;
            width: 0.875rem;
            height: 0.875rem;
            color: var(--primary-600, #808000);
            opacity: 0;
            transform: translateX(-4px);
            transition: all 0.3s;
        }

        .fi-qa-card:hover .fi-qa-arrow {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</x-filament-widgets::widget>
