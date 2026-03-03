<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable'); // Para comentários em diferentes tipos de conteúdo
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('content');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->json('metadata')->nullable(); // Para armazenar dados adicionais
            $table->enum('status', ['pending', 'approved', 'spam', 'trash'])->default('pending');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Para permitir "apagar" sem perder dados

            // Índices para melhor performance
            $table->index(['commentable_type', 'commentable_id', 'status']);
            $table->index('ip_address');
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
