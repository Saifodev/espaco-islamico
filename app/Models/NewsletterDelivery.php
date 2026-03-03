<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterDelivery extends Model
{
    use HasFactory;

    protected $table = 'newsletter_deliveries';

    protected $fillable = [
        'newsletter_id',
        'user_id',
        'email',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * Relacionamento com a newsletter
     */
    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class);
    }

    /**
     * Relacionamento com o usuário (se existir)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para entregas pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para entregas enviadas com sucesso
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope para entregas com falha
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Marca a entrega como enviada
     */
    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Marca a entrega como falha
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Verifica se a entrega está pendente
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verifica se a entrega foi enviada
     */
    public function isSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Verifica se a entrega falhou
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }
}