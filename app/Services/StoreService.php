<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class StoreService extends BaseService
{
    public function addCart($request, $id)
    {
        $data = [$id => $request];
        if (Session::has('cart')) {
            $session = Session::get('cart');

            if (array_key_exists($id, $session)) {
                $session[$id]['amount'] += $request['amount'];
                $data = $session;
            } else {
                $data = $session + $data;
            }
        }
        uasort($data, function($a, $b) {
            return $a['time'] < $b['time'] ? 1 : -1;
        });

        Session::put('cart', $data);
//        dd(Session::get('cart'));
    }
}
