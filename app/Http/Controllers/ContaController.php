<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContaRequest;
use App\Models\Conta;
use App\Models\SituacaoConta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;

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
        //dd('teste');
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

    // gerar CSV
    public function gerarCsv(Request $request)
    {
        // Recuperar os registros do banco dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
            ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
            })->with('situacaoConta')->orderBy('vencimento')->get();

        // dd($contas);

        //Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');

        // Criar o arquivo temporario
        $csvNomeArquivo = tempnam(sys_get_temp_dir(), 'csv_' .  Str::ulid());

        // Abrir o arquivo na forma de escrita
        $arquivoAberto = fopen($csvNomeArquivo, 'w');

        // Criar o cabeçalho do excel - Usar a função mb_convert_encoding para converter caracteres especiais
        $cabecalho = ['id', 'Nome', 'Vencimento', mb_convert_encoding('Situação', 'ISO-8859-1', 'UTF-8'), 'Valor'];

        // Escrever o cabeçalho no arquivo
        fputcsv($arquivoAberto, $cabecalho, ';');

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            // criar o array com os dados da linha Excel
            $contaArray = [
                'id' => $conta->id,
                'nome' => mb_convert_encoding($conta->nome, 'ISO-8859-1', 'UTF-8'),
                'vencimento' => $conta->vencimento,
                'situacao' => mb_convert_encoding($conta->situacaoConta->nome, 'ISO-8859-1', 'UTF-8'),
                'valor' => number_format($conta->valor, 2, ',', '.'),
            ];

            // Escrever o conteúdo no arquivo
            fputcsv($arquivoAberto, $contaArray, ';');
        }

        // criar o rodapé do Excel
        $rodape = ['', '', '', '', number_format($totalValor, 2, ',', '.')];

        // Escrever o conteúdo no arquivo
        fputcsv($arquivoAberto, $rodape, ';');

        // Fechar o arquivo após escrita
        fclose($arquivoAberto);

        // Realiza o download do arquivo
        return response()->download($csvNomeArquivo, 'relatorio_contas_quina_' . Str::ulid() . '.csv');
    }


    // gerar Word
    public function gerarWord(Request $request)
    {
        // Recuperar os registros do banco dados
        $contas = Conta::when($request->has('nome'), function ($whenQuery) use ($request) {
            $whenQuery->where('nome', 'like', '%' . $request->nome . '%');
        })
            ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                $whenQuery->where('vencimento', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
            })->with('situacaoConta')->orderBy('vencimento')->get();

        //dd($contas);

        //Calcular a soma total dos valores
        $totalValor = $contas->sum('valor');

        // Criar uma instancia do PhpWord
        $phpWord = new PhpWord();

        // Adicionar conteúdo ao documento
        $section = $phpWord->addSection();

        // Adicionar uma tabela
        $table = $section->addTable();

        // Definir as configurações de borda
        $borderStyle = [
            'borderColor' => '000000',
            'borderSize' => 6,
        ];

        // Adicionar o cabeçalho da tabela
        $table->addRow();
        $table->addCell(2000, $borderStyle)->addText("id");
        $table->addCell(2000, $borderStyle)->addText("Nome");
        $table->addCell(2000, $borderStyle)->addText("Vencimento");
        $table->addCell(2000, $borderStyle)->addText("Situação");
        $table->addCell(2000, $borderStyle)->addText("Valor");

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            // Adicionar a linha da tabela 
            $table->addRow();
            $table->addCell(2000, $borderStyle)->addText($conta->id);
            $table->addCell(2000, $borderStyle)->addText($conta->nome);
            $table->addCell(2000, $borderStyle)->addText(Carbon::parse($conta->vencimento)->format('d/m/Y'));
            $table->addCell(2000, $borderStyle)->addText($conta->situacaoConta->nome);
            $table->addCell(2000, $borderStyle)->addText(number_format($conta->valor, 2, ',', '.'));
        }

        // Adicionar o total na tabela 
        $table->addRow();
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000)->addText('');
        $table->addCell(2000, $borderStyle)->addText(number_format($totalValor, 2, ',', '.'));

        // criar o nome do arquivo
        $filename = 'relatorio_contas_quina.docx';

        // Obter o caminho completo onde o arquivo gerado pelo PhpWord será salvo
        $savePath = storage_path($filename);

        // Salvar o arquivo
        $phpWord->save($savePath);

        // Forçar o dwonload do arquivo no caminho indicado, após o download remover
        return response()->download($savePath)->deleteFileAfterSend(true);
    }
}
