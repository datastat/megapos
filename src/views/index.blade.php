{!! Form::open(array('route' => 'megapos.init', 'method' => 'post')) !!}

<div>
    {!! Form::label('name', 'name')!!}
    {!! Form::text('name', 'Janez')!!}
</div>

<br/>

<div>
    {!! Form::label('surname', 'surname')!!}
    {!! Form::text('surname', 'Novak')!!}
</div>

<br/>

<div>
    {!! Form::label('email', 'e-mail')!!}
    {!! Form::text('email', 'janez.novak@gmail.com')!!}
</div>

<br/>

<div>
    {!! Form::label('gateway', 'gateway')!!}
    {!! Form::select('gateway', array(
        'ACTIVA_PGW' => 'Activa',
        'BANKART_PGW' => 'Bankart',
        'DINERS' => 'Diners',
        'EFUNDS' => 'Efunds',
        'MONETA' => 'Moneta',
        'KLIK' => 'Kliki',
        'ABANET' => 'Abanet',
    ), 'S')!!}
</div>

<br/>

<div>
    {!! Form::label('language', 'language')!!}
    {!! Form::select('language', array('si' => 'Slovenian', 'en' => 'English'), 'si')!!}
</div>

<br/>

<div>
    {!! Form::label('amount', 'amount')!!}
    {!! Form::text('amount','1.11')!!}
</div>

<br/>

{!! Form::submit('Initialize')!!}

{!! Form::close() !!}


----------------------------------------
<br/>
<br/>
{!! Form::open(array('route' => 'megapos.cancel', 'method' => 'post')) !!}

<div>
    {!! Form::label('transaction_id', 'transaction id')!!}
    {!! Form::text('transaction_id')!!}
</div>

<br/>

{!! Form::submit('Cancel')!!}

{!! Form::close() !!}


----------------------------------------
<br/>
<br/>
{!! Form::open(array('route' => 'megapos.process', 'method' => 'post')) !!}

<div>
    {!! Form::label('transaction_id', 'transaction id')!!}
    {!! Form::text('transaction_id')!!}
</div>

<br/>

{!! Form::submit('Process')!!}

{!! Form::close() !!}