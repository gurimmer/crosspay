<?php

namespace Crosspay\Payjp;

use Crosspay\Adapter\AbstractEvent;
use Crosspay\exception\CrosspayException;
use Crosspay\response\Event;
use Exception;

class PayjpEvent extends AbstractEvent
{

    public function retrieve($id, $options = null): Event
    {
        try {
            $event = \Payjp\Event::retrieve($id, $options);
            return new PayjpEventResponse($event);
        } catch (Exception $e) {
            throw new CrosspayException('event retrieve exception from payjp', 0, $e);
        }
    }

    public function all($params = null, $options = null): array
    {
        try {
            $events = \Payjp\Event::all($params, $options);
        } catch (Exception $e) {
            throw new CrosspayException('event all exception from payjp', 0, $e);
        }

        $results = [];
        foreach ($events as $event) {
            $results[] = new PayjpEventResponse($event);
        }
        return $results;
    }
}
