<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 18:02
 */

use Atxy2k\Essence\Eloquent\Setting;
use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use Atxy2k\Essence\Infraestructure\Repository;
use Throwable;

class SettingsRepository extends Repository
{

    protected $model = Setting::class;

    public function findByKey(string $key, int $user_id = null) : ?Setting
    {
        return !is_null($user_id) ? $this->query->where('key', $key)->where('user_id', $user_id)->first() :
            $this->query->where('key', $key)->whereNull('user_id')->first();
    }

    public function getValue(string $key, int $user_id = null)
    {
        $response = null;
        $system_option = $this->findByKey($key);
        $user_option   = $this->findByKey($key, $user_id);
        if( !is_null($user_option) )
            $response = $user_option->value;
        else
            $response = !is_null($system_option) ? $system_option->value : null;
        return $response;
    }

    public function setValue(string $key, string $value, int $user_id = null, $encode = false) : ?Setting
    {
        $return = null;
        $current = $this->findByKey($key, $user_id);
        if( !is_null($current) )
        {
            $current->encode = $encode;
            $current->value = $value;
            $current->save();
            $return = $current;
        }
        else
        {
            $current = $this->create(['key' => $key, 'value' => $value, 'encode' => $encode, 'user_id' => $user_id]);
            throw_if(is_null($current), UnexpectedException::class);
            $return = $current;
        }
        return $return;
    }

    public function setEncodedValue(string $key, array $value, int $user_id = null) : ?Setting
    {
        $return = null;
        try {
            $return = $this->setValue($key, json_encode($value), $user_id, true);
        } catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $return;
    }

}
