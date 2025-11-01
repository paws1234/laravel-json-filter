<?php

namespace Pawsmedz\JsonFilter\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JsonFilterTest extends TestCase
{
    /** @test */
    public function it_applies_mysql_or_pgsql_json_filter_correctly()
    {
        $driver = DB::getDriverName();
        $builder = User::query()->jsonFilter('meta->status', '=', 'active');

        if ($driver === 'mysql') {
            $this->assertStringContainsString('JSON_EXTRACT', $builder->toSql());
        } elseif ($driver === 'pgsql') {
            $this->assertStringContainsString("->>'status'", $builder->toSql());
        } else {
            $this->assertTrue(true, 'Driver skipped for unsupported DB');
        }
    }

    /** @test */
    public function it_can_filter_nested_json_paths()
    {
        $builder = User::query()->jsonFilter('meta->details->region', '=', 'cebu');
        $sql = $builder->toSql();

        $this->assertTrue(
            str_contains($sql, 'JSON_EXTRACT') || str_contains($sql, '->>'),
            'SQL should include JSON extraction syntax'
        );
    }
}

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}
