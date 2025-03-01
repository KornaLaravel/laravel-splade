<?php

namespace Tests\Unit;

use ProtoneMedia\Splade\Facades\SEO;
use ProtoneMedia\Splade\Head;
use Tests\TestCase;

class HeadTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_trims_the_title()
    {
        $head = new Head;
        $head->title(' Laravel Splade ');

        $this->assertEquals('Laravel Splade', $head->getTitle());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prepends_the_prefix()
    {
        config(['splade.seo.title_prefix' => 'Prefix']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Prefix Laravel Splade', $head->getTitle());

        config(['splade.seo.title_prefix' => 'Prefix']);
        config(['splade.seo.title_separator' => '|']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Prefix | Laravel Splade', $head->getTitle());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_doesnt_prepend_the_prefix_if_its_the_same_as_the_title()
    {
        config(['splade.seo.title_prefix' => 'Laravel Splade']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Laravel Splade', $head->getTitle());

        config(['splade.seo.title_prefix' => 'Laravel Splade']);
        config(['splade.seo.title_separator' => '|']);

        $head = (new Head)->title('Laravel Splade');

        $this->assertEquals('Laravel Splade', $head->getTitle());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_appends_the_prefix()
    {
        config(['splade.seo.title_suffix' => 'Suffix']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Laravel Splade Suffix', $head->getTitle());

        config(['splade.seo.title_suffix' => 'Suffix']);
        config(['splade.seo.title_separator' => '|']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Laravel Splade | Suffix', $head->getTitle());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_doesnt_append_the_suffix_if_its_the_same_as_the_title()
    {
        config(['splade.seo.title_suffix' => 'Laravel Splade']);

        $head = (new Head)->title('Laravel Splade');
        $this->assertEquals('Laravel Splade', $head->getTitle());

        config(['splade.seo.title_suffix' => 'Laravel Splade']);
        config(['splade.seo.title_separator' => '|']);

        $head = (new Head)->title('Laravel Splade');

        $this->assertEquals('Laravel Splade', $head->getTitle());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fill_the_twitter_defaults()
    {
        config([
            'splade.seo.defaults.title'       => 'Default Title',
            'splade.seo.defaults.description' => 'Default Description',
            'splade.seo.twitter.auto_fill'    => true,
            'splade.seo.twitter.title'        => null,
            'splade.seo.twitter.description'  => null,
        ]);

        $head = new Head;

        $this->assertEquals($head->getMetaByName('twitter:title')->first()->content, 'Default Title');
        $this->assertEquals($head->getMetaByName('twitter:description')->first()->content, 'Default Description');

    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fill_the_open_graph_defaults()
    {
        config([
            'splade.seo.open_graph.auto_fill' => true,
            'splade.seo.defaults.title'       => 'Default Title',
            'splade.seo.open_graph.title'     => null,
        ]);

        $head = new Head;

        $this->assertEquals($head->getMetaByProperty('og:title')->first()->content, 'Default Title');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fill_the_twitter_defaults_and_then_auto_fills()
    {
        config([
            'splade.seo.twitter.auto_fill'   => true,
            'splade.seo.twitter.card'        => 'summary_large_image',
            'splade.seo.twitter.site'        => '@username',
            'splade.seo.twitter.title'       => 'Default Title',
            'splade.seo.twitter.description' => 'Default Description',
            'splade.seo.twitter.image'       => 'http://image',
        ]);

        $head = new Head;

        $this->assertEquals($head->getMetaByName('twitter:card')->first()->content, 'summary_large_image');
        $this->assertEquals($head->getMetaByName('twitter:site')->first()->content, '@username');
        $this->assertEquals($head->getMetaByName('twitter:title')->first()->content, 'Default Title');
        $this->assertEquals($head->getMetaByName('twitter:description')->first()->content, 'Default Description');
        $this->assertEquals($head->getMetaByName('twitter:image')->first()->content, 'http://image');

        $head->title('Updated Title');
        $head->description('Updated Description');

        $this->assertEquals($head->getMetaByName('twitter:title')->first()->content, 'Updated Title');
        $this->assertEquals($head->getMetaByName('twitter:description')->first()->content, 'Updated Description');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fill_the_open_graph_defaults_and_then_auto_fills()
    {
        config([
            'splade.seo.open_graph.auto_fill' => true,
            'splade.seo.open_graph.type'      => 'WebPage',
            'splade.seo.open_graph.site_name' => 'Default Site Name',
            'splade.seo.open_graph.title'     => 'Default Title',
            'splade.seo.open_graph.url'       => 'http://url',
            'splade.seo.open_graph.image'     => 'http://image',
        ]);

        $head = new Head;

        $this->assertEquals($head->getMetaByProperty('og:type')->first()->content, 'WebPage');
        $this->assertEquals($head->getMetaByProperty('og:site_name')->first()->content, 'Default Site Name');
        $this->assertEquals($head->getMetaByProperty('og:title')->first()->content, 'Default Title');
        $this->assertEquals($head->getMetaByProperty('og:url')->first()->content, 'http://url');
        $this->assertEquals($head->getMetaByProperty('og:image')->first()->content, 'http://image');

        $head->title('Updated Title');

        $this->assertEquals($head->getMetaByProperty('og:title')->first()->content, 'Updated Title');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_render_the_canonical_link()
    {
        $head = new Head;
        $head->canonical('https://splade.dev');

        $this->assertStringContainsString('<link rel="canonical" href="https://splade.dev">', $head->renderHead()->toHtml());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_macroable()
    {
        SEO::macro('openGraphLocale', function (string $value) {
            return $this->metaByProperty('og:locale', $value);
        });

        $head = new Head;
        $head->openGraphLocale('nl');

        $this->assertStringContainsString('<meta property="og:locale" content="nl" />', $head->renderHead()->toHtml());
    }
}
