<?php
/**
 * Created by PhpStorm.
 * User: chenyihong
 * Date: 16/8/1
 * Time: 14:53
 */

namespace Leo108\CAS\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Leo108\CAS\Contracts\Models\UserModel;

/**
 * Class Ticket
 * @package Leo108\CAS\Models
 *
 * @property integer   $id
 * @property string    $ticket
 * @property string    $service_url
 * @property integer   $service_id
 * @property integer   $user_id
 * @property array     $proxies
 * @property integer   $created_at
 * @property integer   $expire_at
 * @property UserModel $user
 */
class Ticket extends Model
{
    protected $table = 'cas_tickets';
    public $timestamps = false;
    protected $fillable = ['ticket', 'service_url', 'proxies', 'expire_at', 'created_at'];

    public function getProxiesAttribute()
    {
        if (!$this->isProxy()) {
            return null;
        }

        return json_decode($this->attributes['proxies'], true);
    }

    public function setProxiesAttribute($value)
    {
        if ($this->id && !$this->isProxy()) {
            return;
        }
        $this->attributes['proxies'] = json_encode($value);
    }

    public function isExpired()
    {
        return (new Carbon($this->expire_at))->getTimestamp() < time();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(config('cas.user_table.model'), 'user_id', config('cas.user_table.id'));
    }

    public function isProxy()
    {
        return !is_null($this->attributes['proxies']);
    }
}
