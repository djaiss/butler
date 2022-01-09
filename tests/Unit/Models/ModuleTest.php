<?php

namespace Tests\Unit\Models;

use App\Models\Module;
use Tests\TestCase;
use App\Models\Template;
use App\Models\TemplatePage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModuleTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_account()
    {
        $module = Module::factory()->create();

        $this->assertTrue($module->account()->exists());
    }

    /** @test */
    public function it_has_many_template_pages(): void
    {
        $module = Module::factory()->create();

        $templatePage = TemplatePage::factory()->create();
        $module->templatePages()->sync([$module->id]);

        $this->assertTrue($module->templatePages()->exists());
    }
}
