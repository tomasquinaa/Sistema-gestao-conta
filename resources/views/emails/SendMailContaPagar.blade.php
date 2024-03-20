@extends('layouts.email')

@section('content')

Olá,

Contas a pagar <br>

    @foreach ($contas as $conta)
        - {{ $conta->nome }}: AOA {{ number_format($conta->valor, 2, ',', '.')}} - {{ $conta->situacaoConta->nome }} - {{ \Carbon\Carbon::parse($conta->vencimento)->format('d/m/Y') }}
    @endforeach

    <br>
   
E-mail enviado pelo sistema Quina

@endsection