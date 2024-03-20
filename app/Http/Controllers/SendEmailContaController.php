<?php

namespace App\Http\Controllers;

use App\Mail\SendMailContaPagar;
use App\Models\Conta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEmailContaController extends Controller
{
    // Enviar emails contas pendentes
    public function sendEmailPendenteConta()
    {
        try {

            // Obter a data atual
            $dataAtual = Carbon::now()->toDateString();

            // Recuperar as contas do banco de dados
            $contas = Conta::whereDate('vencimento', $dataAtual)
                ->with('situacaoConta')
                ->get();

            // dd($contas);

            // Enviar os dados para enviar e-mail. Obs: neste send(), o parametro que vai lá é do mail que sera executado para a sua criação: php artisan make:mail SendMailContaPagar
            // no sendMailContaPagar enviaram as contas 
            Mail::to(env('MAIL_TO'))->send(new SendMailContaPagar($contas));

            // Redirecionar de volta à página anterior
            return back()->with('success', 'E-mail enviado com sucesso!');
        } catch (Exception $e) {

            // Salvar log
            Log::warning('E-mail não enviado.', ['error' => $e->getMessage()]);

            // redirecionar o usuário, enviar a mensagem de erro
            return back()->with('error', 'E-mail não enviado');
        }
    }
}
