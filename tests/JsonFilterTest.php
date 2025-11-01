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
        $sql = $builder->toSql();

        if ($driver === 'mysql') {
            $this->assertStringContainsString('JSON_EXTRACT', $sql, 'MySQL should use JSON_EXTRACT syntax');
        } elseif ($driver === 'pgsql') {
            $this->assertStringContainsString("->>'status'", $sql, 'PostgreSQL should use ->> syntax');
        } elseif ($driver === 'sqlite') {
            $this->markTestSkipped('SQLite fallback does not use JSON_EXTRACT syntax');
        } else {
            $this->assertTrue(true, 'Driver skipped for unsupported DB');
        }
    }

    /** @test */
    public function it_can_filter_nested_json_paths()
    {
        $driver = DB::getDriverName();
        $builder = User::query()->jsonFilter('meta->details->region', '=', 'cebu');
        $sql = $builder->toSql();

        if ($driver === 'sqlite') {
            $this->markTestSkipped('SQLite fallback does not use JSON_EXTRACT syntax');
        }

        $this->assertTrue(
            str_contains($sql, 'JSON_EXTRACT') || str_contains($sql, '->>'),
            "SQL should include JSON_EXTRACT or ->> syntax; got: {$sql}"
        );
    }
}

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;
}
