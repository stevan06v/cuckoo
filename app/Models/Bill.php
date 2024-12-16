<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;
    protected $fillable = [
        "contract_classification_id",
        "contract_classification_id_for_contract",
        "name",
        "orginial_filename",
        "contract_image",
        "created_on",
        "due_to",
        "description"
    ];
    public $casts = [
        'orginial_filename' => 'array',
        'contract_image' => 'array'
    ];
    public function contractClassification(): BelongsTo {
        return $this->belongsTo(ContractClassification::class);
    }
    public function users(): BelongsTo {
        return $this->belongsTo(ContractClassification::class, 'contract_classifications');
    }

    public function contract(): BelongsTo {
        return $this->belongsTo(ContractClassification::class, 'contract_classifications');
    }
}