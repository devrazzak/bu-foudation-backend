<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("phone_number")->nullable()->after('email');
            $table->string("profile_picture")->nullable()->after('phone_number');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('profile_picture');
            $table->boolean('terms_accepted_at')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('profile_picture');
            $table->dropColumn('status');
            $table->dropColumn('terms_accepted_at');
        });
    }
};
