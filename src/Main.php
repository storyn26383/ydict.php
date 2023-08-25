<?php

namespace App;

class Main
{
    public function foo(Foo $foo)
    {
        return $foo->foo();
    }
}
