<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Table;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

#[Signature('seo:generate-sitemap')]
#[Description('Generate the sitemap.xml for SEO.')]
class GenerateSitemap extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // 1. Static Routes
        $sitemap->add(Url::create('/')
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        $sitemap->add(Url::create('/menu')
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        $sitemap->add(Url::create('/reservation')
            ->setPriority(0.7)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));

        $sitemap->add(Url::create('/order')
            ->setPriority(0.7)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));

        // 2. Dynamic Pages
        Page::where('is_active', true)->get()->each(function (Page $page) use ($sitemap) {
            $sitemap->add(Url::create("/{$page->slug}")
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        // 3. Table QR Menus (Optional, depends if we want search engines to find them)
        Table::all()->each(function (Table $table) use ($sitemap) {
            $sitemap->add(Url::create("/table/{$table->slug}")
                ->setPriority(0.3)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully in public/sitemap.xml');
    }
}
