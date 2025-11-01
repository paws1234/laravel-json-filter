<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class JsonMacroTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed example data
        DB::table('users')->insert([
            ['meta' => json_encode(['status' => 'active', 'score' => 90, 'tags' => ['dev', 'php'], 'subscription' => ['plan' => 'pro'], 'profile' => ['country' => 'PH']])],
            ['meta' => json_encode(['status' => 'inactive', 'score' => 75, 'tags' => ['laravel', 'vue'], 'subscription' => ['plan' => 'basic'], 'profile' => ['country' => 'US']])],
            ['meta' => json_encode(['status' => 'pending', 'score' => 85, 'tags' => ['dev', 'js'], 'subscription' => [], 'profile' => ['country' => 'AU']])],
        ]);
    }

    protected function skipIfUnsupported(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not support advanced JSON functions.');
        }
    }

    /** @test */
    public function it_filters_records_by_json_key_value_using_jsonFilter()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonFilter('meta->status', '=', 'active')
            ->get();

        $this->assertCount(1, $results);
        $this->assertStringContainsString('active', $results->first()->meta);
    }

    /** @test */
    public function it_orders_records_by_json_value_using_jsonOrderBy()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonOrderBy('meta->score', 'desc')
            ->get();

        $first = json_decode($results->first()->meta, true)['score'];
        $last = json_decode($results->last()->meta, true)['score'];

        $this->assertTrue($first >= $last);
    }

    /** @test */
    public function it_filters_using_jsonWhereIn()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonWhereIn('meta->status', ['active', 'pending'])
            ->get();

        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_filters_using_jsonContains()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonContains('meta->tags', 'dev')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
    }

    /** @test */
    public function it_checks_if_json_path_exists()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonExists('meta->subscription->plan')
            ->get();

        $this->assertTrue($results->count() > 0);
    }

    /** @test */
    public function it_can_select_json_key_as_alias()
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonSelect('meta->profile->country as country')
            ->get();

        $this->assertTrue(property_exists($results->first(), 'country'));
        $this->assertContains($results->first()->country, ['PH', 'US', 'AU']);
    }
}
