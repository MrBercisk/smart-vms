<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfNotExists('visits', 'arrival_time');
        $this->addIndexIfNotExists('visits', 'status');
        $this->addIndexIfNotExists('visits', 'department_id');
        $this->addIndexIfNotExists('visits', 'employee_id');

        $this->addIndexIfNotExists('appointments', 'status');
        $this->addIndexIfNotExists('appointments', 'appointment_date');

        $this->addIndexIfNotExists('audit_logs', 'module');
        $this->addIndexIfNotExists('audit_logs', 'activity');
        $this->addIndexIfNotExists('audit_logs', 'created_at');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('visits', 'arrival_time');
        $this->dropIndexIfExists('visits', 'status');
        $this->dropIndexIfExists('visits', 'department_id');
        $this->dropIndexIfExists('visits', 'employee_id');

        $this->dropIndexIfExists('appointments', 'status');
        $this->dropIndexIfExists('appointments', 'appointment_date');

        $this->dropIndexIfExists('audit_logs', 'module');
        $this->dropIndexIfExists('audit_logs', 'activity');
        $this->dropIndexIfExists('audit_logs', 'created_at');
    }

    private function addIndexIfNotExists(string $table, string $column): void
    {
        $indexName = $table . '_' . $column . '_index';
        $exists = DB::select(
            "SHOW INDEX FROM {$table} WHERE Key_name = ?",
            [$indexName]
        );

        if (empty($exists)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                $tableBlueprint->index($column);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $column): void
    {
        $indexName = $table . '_' . $column . '_index';
        $exists = DB::select(
            "SHOW INDEX FROM {$table} WHERE Key_name = ?",
            [$indexName]
        );

        if (!empty($exists)) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                $tableBlueprint->dropIndex([$column]);
            });
        }
    }
};