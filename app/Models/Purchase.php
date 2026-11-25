<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'ingredient_id',
        'quantity',
        'unit_price',
        'total_price',
        'purchase_date',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function expense()
    {
        return $this->hasOne(Expense::class);
    }
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date');
            $table->string('invoice_number');
            $table->string('supplier');
            $table->text('items'); // Kita akan menyimpan multi-item dalam bentuk JSON/Teks
            $table->integer('total_amount');
            $table->string('payment_method'); // Tunai, Transfer, Tempo/Hutang
            $table->string('status'); // Lunas, Hutang (Jatuh Tempo: 30 Nov), dll
            $table->timestamps();
        });
    }
}