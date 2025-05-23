<?php

namespace Tests\Browser\Table;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

#[\PHPUnit\Framework\Attributes\Group('table')]
class PaginationTest extends DuskTestCase
{
    public static function simpleUrls()
    {
        return [
            ['/table/users/spatie/', 'simple'],
            ['/table/users/spatie/', 'cursor'],
            ['/table/users/splade/', 'simple'],
            ['/table/users/splade/', 'cursor'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('tableUrls')]
    public function it_generates_a_paginator_with_links($url)
    {
        $this->browse(function (Browser $browser) use ($url) {
            $users = User::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $browser
                ->visit($url)
                ->resize(1920, 1080)
                ->assertMissing('@pagination-simple-previous')
                ->assertMissing('@pagination-simple-next')

                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@pagination-previous')
                ->press('@pagination-next')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->press('@pagination-3')
                ->waitUntilMissingText($users->get(10)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(20)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(29)->name)
                ->press('@pagination-previous')
                ->waitUntilMissingText($users->get(20)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->press('@pagination-10')
                ->waitUntilMissingText($users->get(10)->name)
                ->assertMissing('@pagination-next')

                ->press('@pagination-1')
                ->waitUntilMissingText($users->get(99)->name)

                // mobile pagination
                ->resize(320, 480)

                ->assertMissing('@pagination-next')
                ->assertMissing('@pagination-previous')

                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@pagination-simple-previous')
                ->press('@pagination-simple-next')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->visit($url . '?page=10')
                ->waitUntilMissingText($users->get(10)->name)
                ->assertMissing('@pagination-simple-next')
                ->press('@pagination-simple-previous')
                ->waitUntilMissingText($users->get(99)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(80)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(89)->name);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('booleanDataset')]
    public function it_disabled_the_per_page_selection_on_less_than_two_options($spladeQueryBuilder)
    {
        $this->browse(function (Browser $browser) use ($spladeQueryBuilder) {
            $users = User::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $browser->visit('/table/noPerPage/' . (int) $spladeQueryBuilder)
                ->resize(1920, 1080)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertMissing('select[name="per_page"]');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('simpleUrls')]
    public function it_generates_a_simple_paginator($url, $method)
    {
        $url .= $method;

        $this->browse(function (Browser $browser) use ($url, $method) {
            $users = User::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $cursor = '?cursor=' . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode([
                'name'               => $users->get(89)->name,
                '_pointsToNextItems' => true,
            ])));

            $pageTen = $method === 'simple' ? '?page=10' : $cursor;

            $browser
                ->visit($url)
                ->resize(1920, 1080)

                ->assertMissing('@pagination-next')
                ->assertMissing('@pagination-previous')

                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@pagination-simple-previous')
                ->press('@pagination-simple-next')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->visit($url . $pageTen)
                ->waitUntilMissingText($users->get(10)->name)
                ->assertMissing('@pagination-simple-next')
                ->press('@pagination-simple-previous')
                ->waitUntilMissingText($users->get(99)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(80)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(89)->name)

                // mobile pagination
                ->visit($url)
                ->resize(320, 480)

                ->assertMissing('@pagination-next')
                ->assertMissing('@pagination-previous')

                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@pagination-simple-previous')
                ->press('@pagination-simple-next')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->visit($url . $pageTen)
                ->waitUntilMissingText($users->get(10)->name)
                ->assertMissing('@pagination-simple-next')
                ->press('@pagination-simple-previous')
                ->waitUntilMissingText($users->get(99)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(80)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(89)->name);
        });
    }
}
