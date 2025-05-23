<?php

namespace Tests\Browser\Table;

use App\Models\Project;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

#[\PHPUnit\Framework\Attributes\Group('table')]
class PreserveScrollTest extends DuskTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_preserve_the_scroll_value_with_a_form_component()
    {
        return $this->markTestSkipped('Breaks in CI');

        $this->browse(function (Browser $browser) {
            $latestProject = Project::orderByDesc('name')->first();
            $company       = $latestProject->name;

            $browser
                ->visit('table/preserveScrollForm')
                ->resize(1920, 540)
                ->scrollIntoView('tr:nth-child(30)');

            $scrollY = $browser->script('return window.scrollY');

            $this->assertTrue($scrollY > 0);

            $browser
                ->assertSeeIn('tr:nth-child(30) td:nth-child(1)', $latestProject->name)
                ->pause(250)
                ->press('tr:nth-child(30) td:nth-child(2) button')
                ->waitForText('Project updated!')
                ->pause(250);

            $this->assertEquals("updated-{$company}", $latestProject->fresh()->name);
            $this->assertEquals($scrollY, $browser->script('return window.scrollY'));
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_preserve_the_scroll_value_with_a_link_component()
    {
        return $this->markTestSkipped('Breaks in CI');

        $this->browse(function (Browser $browser) {
            $latestProject = Project::orderByDesc('name')->first();
            $company       = $latestProject->name;

            $browser
                ->visit('table/preserveScrollForm')
                ->resize(1920, 540)
                ->scrollIntoView('tr:nth-child(30)');

            $scrollY = $browser->script('return window.scrollY');

            $this->assertTrue($scrollY > 0);

            $browser
                ->assertSeeIn('tr:nth-child(30) td:nth-child(1)', $latestProject->name)
                ->press('tr:nth-child(30) td:nth-child(2) a')
                ->waitForText('Project updated!');

            $this->assertEquals("updated-{$company}", $latestProject->fresh()->name);
            $this->assertEquals($scrollY, $browser->script('return window.scrollY'));
        });
    }
}
