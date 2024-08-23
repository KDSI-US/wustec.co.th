<?php
namespace Mpcheckout;
final class Manager {
    const MPREDIRECT = 'checkout/checkout';

    static function mpssl() {
    	if(VERSION >= '2.1.0.2') {
			return true;
		} else{
			return 'SSL';
		}
    }
}
