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

    public function test_it_filters_records_by_json_key_value_using_jsonFilter(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonFilter('meta->status', '=', 'active')
            ->get();

        $this->assertCount(1, $results);
        $this->assertStringContainsString('active', $results->first()->meta);
    }

    public function test_it_orders_records_by_json_value_using_jsonOrderBy(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonOrderBy('meta->score', 'desc')
            ->get();

        $first = json_decode($results->first()->meta, true)['score'];
        $last = json_decode($results->last()->meta, true)['score'];

        $this->assertTrue($first >= $last);
    }

    public function test_it_filters_using_jsonWhereIn(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonWhereIn('meta->status', ['active', 'pending'])
            ->get();

        $this->assertCount(2, $results);
    }

    public function test_it_filters_using_jsonContains(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonContains('meta->tags', 'dev')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
    }

    public function test_it_checks_if_json_path_exists(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonExists('meta->subscription->plan')
            ->get();

        $this->assertTrue($results->count() > 0);
    }

    public function test_it_can_select_json_key_as_alias(): void
    {
        $this->skipIfUnsupported();

        $results = DB::table('users')
            ->jsonSelect('meta->profile->country as country')
            ->get();

        $this->assertTrue(property_exists($results->first(), 'country'));
        $this->assertContains($results->first()->country, ['PH', 'US', 'AU']);
    }
}
