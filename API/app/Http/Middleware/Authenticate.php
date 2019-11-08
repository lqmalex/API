<?php

namespace App\Http\Middleware;

use App\TokenModel;
use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (empty($request->header('Authorization'))) {
            return [
                'token_status' => false,
            ];
        } else {
            $token = substr($request->header('Authorization'),7);
            $token_model = new TokenModel();
            $res = $token_model->with('user')->where('token', '=', $token)->first();

            if ($res != null) {
                $time = $res->validity;
                $type = $this->currentTime($time);

                if (!$type) {
                    $token_model->where('token', '=', $request->token)->delete();

                    return [
                        'token_status' => false,
                    ];
                } else {
                    return $next($request);
                }
            } else {
                return [
                    'token_status' => false,
                ];
            }
        }
    }

    /**
     * token是否过期
     * @param $time
     * @return bool
     */
    private function currentTime($time)
    {
        $strtotime = strtotime($time);
        $currenttime = time();

        if ($currenttime >= $strtotime) {
            return false;
        } else {
            return true;
        }
    }
}
