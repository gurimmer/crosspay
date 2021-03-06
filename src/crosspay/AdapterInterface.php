<?php

namespace Crosspay;

interface AdapterInterface
{
    public function customer() : CustomerInterface;
    public function charge() : ChargeInterface;
    public function subscription() : SubscriptionInterface;
    public function event() : EventInterface;
}
