<?php
namespace App\Validator;

use Respect\Validation\Validator as v;
use App\Http\Exceptions\HttpException;

class OrderValidator {
    public static function validateCreate(array $req): void {
         $errors = [];

        if (!isset($req['items']) || (isset($req['items']) && (!is_array($req['items']) || count($req['items']))) === 0) {
            $errors['items'] = 'items must be a non-empty array of item names.';
        } else {
            foreach ($req['items'] as $i => $it) {
                if (!is_string($it) || trim($it) === '') {
                    $errors["items[$i]"] = 'each item must be a non-empty string';
                }
            }
        }
        if (isset($req['pickup_time']) && !empty($req['pickup_time'])) {
            $d = date_create($req['pickup_time']);
            if ($d === false) {
                $errors['pickup_time'] = 'pickup_time must be an ISO-8601 datetime string';
            }
        } else {
            $errors['pickup_time'] = 'pickup_time is required';
        }
        if (!empty($errors)) {
            throw new HttpException(json_encode($errors), 400);
        }
    }
}