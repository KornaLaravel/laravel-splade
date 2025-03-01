<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DialogTest extends DuskTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_hides_the_dialog_on_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/navigation/one')
                ->waitForText('NavigationOne')
                ->pause(250)
                ->click('@open-dialog')
                ->pause(250)
                ->click('@dialog-two')
                ->waitForText('NavigationTwo')
                ->assertMissing('@dialog-two')
                ->back()
                ->pause(250)
                ->assertPresent('@dialog-two');
        });
    }
}
