# EON MegaPOS Test

add to providers in app.php ...
```
Datastat\MegaPOS\MegaPOSServiceProvider::class,
```

publish vendor ...
```
php artisan vendor:publish
```

register events in EventServiceProvider.php ...
```
'Datastat\MegaPOS\MegaPOSUpdateWasCalledEvent' => [
    'App\Listeners\MegaPosUpdateWasCalledEventListener',
],
'Datastat\MegaPOS\MegaPOSStatusWasCalledEvent' => [
    'App\Listeners\MegaPosStatusWasCalledEventListener',
],
'Datastat\MegaPOS\MegaPOSCancelTransactionWasCalledEvent' => [
    'App\Listeners\MegaPOSCancelTransactionWasCalledEventCalledEventListener',
],
```

generate handlers ... [then delete the Events/Datastat folder, change namespace alias in handlers]
```
php artisan event:generate    
```

routes visible for production
```
| GET|HEAD  | megapos/update        | megapos.update   | Closure 
| GET|HEAD  | megapos/status        | megapos.status   | Closure 
```

routes visible for testing [enable_test_routes = true in megapos.php config]

```
| GET|HEAD  | megapos/test          | megapos.test     | Closure 
| POST      | megapos/init          | megapos.init     | Closure 
| POST      | megapos/cancel        | megapos.cancel   | Closure 
| POST      | megapos/process       | megapos.process  | Closure 
| GET|HEAD  | megapos/list-gateways | megapos.list     | Closure 
```

from IOC...

```
$megapos = App::make('megapos');
```

facade...

```
\MegaPOS
```

and now, the api...

```
$megapos->init($params);
```

$params rules:

```
'name' => array( 'required', 'alpha_dash', 'max:200' ),
'surname' => array( 'required', 'alpha_dash', 'max:200' ),
'email' => array( 'required', 'email', 'min:6', 'max:200' ),
'language' => array( 'required', 'in:si,en'),
'gateway' => array( 'required', 'in:ACTIVA_PGW,BANKART_PGW,DINERS,EFUNDS,MONETA,KLIK,ABANET'),
'tx_type' => array( 'sometimes|required', 'in:PURCHASE,ORDER'),
'amount' => array( 'required', 'numeric'),
```

```
$megapos->cancel($txId);
```

```
$megapos->listGateways();
```

exceptions to catch

```
Datastat\MegaPOS\MegaPOSException
```
