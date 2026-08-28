<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuse_secrets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('encrypted_value');
            $table->text('description')->nullable();
            $table->string('version')->default('1');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuse_secrets');
    }
};
