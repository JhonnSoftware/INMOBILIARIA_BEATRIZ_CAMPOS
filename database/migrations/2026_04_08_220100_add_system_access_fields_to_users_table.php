<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 60)->nullable()->after('name');
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->json('paginas_permitidas')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('paginas_permitidas');
            $table->index('is_active');
            $table->index('last_login_at');
        });

        $used = [];
        DB::table('users')->orderBy('id')->get()->each(function ($user) use (&$used) {
            $base = '';

            if (! empty($user->email)) {
                $base = Str::before((string) $user->email, '@');
            }

            if ($base === '') {
                $base = Str::slug((string) $user->name, '_');
            }

            $base = trim($base) !== '' ? strtolower(trim($base)) : 'usuario';
            $candidate = $base;
            $suffix = 2;

            while (in_array($candidate, $used, true) || DB::table('users')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                $candidate = $base . $suffix;
                $suffix++;
            }

            $used[] = $candidate;

            DB::table('users')->where('id', $user->id)->update([
                'username' => $candidate,
                'is_active' => true,
            ]);
        });

        DB::statement('ALTER TABLE users MODIFY username VARCHAR(60) NOT NULL');
        DB::statement('ALTER TABLE users ADD UNIQUE INDEX users_username_unique (username)');
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP INDEX users_username_unique');
        DB::statement("UPDATE users SET email = CONCAT(username, '@local.test') WHERE email IS NULL");
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['last_login_at']);
            $table->dropColumn(['username', 'role_id', 'is_active', 'paginas_permitidas', 'last_login_at']);
        });
    }
};
