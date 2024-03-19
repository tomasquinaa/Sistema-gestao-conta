<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContaRequest;
use App\Models\Conta;
use App\Models\SituacaoConta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Barryvdh\DomPDF\Facade\Pdf;

class ContaController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Recuperar os registros do banco dados
            $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
                $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
            })
                ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                    $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
                })
                ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                    $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
                })->with('situacaoConta')->orderByDesc('created_at')->paginate(2)->withQueryString();

            // dd($contas);

            Log::info('Conta Listada', ['conta' => $contas]);
            //  dd($contas);

            return view('contas.index', [
                'contas' => $contas,
                'nome' => $request->nome,
                'data_inicio' => $request->data_inicio,
                'data_fim' => $request->data_fim,
            ]);
        } catch (Exception $e) {

            Log::warning('Error! a listagem de conta', ['error' => $e->getMessage()]);
            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Erro, alguma inconformidade.');
        }
    }

    public function create()
    {
        // Recuperar do banco de dados as situações
        $situacoesContas = SituacaoConta::orderBy('nome', 'asc')->get();

        return view('contas.create', [
            'situacoesContas' => $situacoesContas
        ]);
    }

    // public function store(Request $request)
    public function store(ContaRequest $request)
    {
        // Validar o formulario
        // $request->validate();
        // Validar o formulário usando as regras definidas no ContaRequest
        $request->validate($request->rules(), $request->messages());

        try {
            // $conta = Conta::create($request->all());

            $conta = Conta::create([
                'nome' => $request->nome,
                'valor' => str_replace(',', '.', str_replace('.', '', $request->valor)),
                'vencimento' => $request->vencimento,
                'situacao_conta_id' => $request->situacao_conta_id,
            ]);

            Log::info('Conta cadastrada com sucesso!', ['conta' => $conta->id, 'conta' => $conta]);

            return redirect()->route('conta.show', ['conta' => $conta->id])->with('success', 'Conta cadastrada com sucesso');
        } catch (Exception $e) {
            // Salavra Log
            Log::warning('Erro! Conta não cadastrada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Erro, alguma inconformidade.');
        }
    }

    public function show(Conta $conta)
    {

        // dd($conta);
        return view('contas.show', ['conta' => $conta]);
    }

    public function edit(Conta $conta)
    {
        // dd($conta);

        // Recuperar do banco de dados as situações
        $situacoesContas = SituacaoConta::orderBy('nome', 'asc')->get();

        return view('contas.edit', ['conta' => $conta, 'situacoesContas' => $situacoesContas]);
    }

    public function update(ContaRequest $request, Conta $conta)
    {
        $request->validate($request->rules(), $request->messages());

        try {
            //Editar as informações do registro no banco de dados
            $conta->update([
                'nome' => $request->nome,
                'valor' => str_replace(',', '.', str_replace('.', '', $request->valor)),
                'vencimento' => $request->vencimento,
                'situacao_conta_id' => $request->situacao_conta_id,
            ]);

            // Salvar log
            Log::info('Curso atualizado com sucesso', ['id' => $conta->id, 'conta' => $conta]);

            return redirect()->route('conta.show', ['conta' => $conta->id])->with('success', 'Conta atualizado com sucesso');
        } catch (Exception $e) {
            // Salvar log
            Log::warning('Conta não editada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Erro, revisa o seu codigo.');
        }
    }

    public function destroy(Conta $conta)
    {
        // Excluir o registro do banco de dados
        $conta->delete();

        // Redirecionar o usuário, enviar a mensagem de sucesso
        return redirect()->route('conta.index')->with('success', 'Conta apagada com sucesso!');
    }

    public function gerarPdf(Request $request)
    {

        // Recuperar os registros do banco dados
        // $contas = Conta::orderByDesc('created_at')->get();

        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
            ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
            })->orderByDesc('created_at')->get();

        // ->orderByDesc('created_at')->get()->withQueryString();

        //Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');


        // Carregar a string com o HTML/conteúdo e determinar a orientação e o tamanho do arquivo
        $pdf = Pdf::loadView('contas.gerar-pdf', [
            'contas' => $contas,
            'totalValor' => $totalValor
        ])->setPaper('a4', 'portrait');

        return $pdf->download('listar_contas.pdf');
    }

    // Alterar situação da conta Status
    public function changeSituation(Conta $conta)
    {
        try {
            // Editar as informações do registro no banco de dados
            $conta->update([
                // indicado o numero, estamos afirmar que sempre que mudar o status, colocamos paga
                // 'situacao_conta_id' => 1,
                'situacao_conta_id' => $conta->situacao_conta_id == 1 ? 2 : 1,
            ]);

            // Salvar log
            Log::info('Situação da conta atualizado com sucesso', ['id' => $conta->id, 'conta' => $conta]);

            return back()->with('success', 'Situação da conta atualizado com sucesso!');
        } catch (Exception $e) {
            // Salvar log
            Log::warning('Situação da conta não editada', ['error' => $e->getMessage()]);

            // Redirecionar o usuário, enviar a mensagem de erro
            return back()->with('error', 'Situação da conta não editada!');
        }
    }
}
