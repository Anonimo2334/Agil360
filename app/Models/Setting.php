<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;

        switch ($setting->type) {
            case 'boolean':
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $setting->value;
            case 'json':
                return json_decode($setting->value, true);
            default:
                return $setting->value;
        }
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        $stringValue = $value;
        if ($type === 'boolean') {
            $stringValue = $value ? '1' : '0';
        } elseif ($type === 'json' && is_array($value)) {
            $stringValue = json_encode($value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue, 'type' => $type, 'group' => $group]
        );
    }
}
