<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompromissoRequest;
use App\Http\Requests\UpdateCompromissoRequest;
use App\Models\Compromisso;
use App\Models\Processo;
use App\Models\User;
use Illuminate\Http\Request;

class CompromissosController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        return view('agenda', array_merge($this->formData('pesquisa'), [
            'compromissos' => $query->orderBy('data_hora')->get(),
            'request' => $request,
        ]));
    }

    public function incluir(StoreCompromissoRequest $request)
    {
        if ($request->isMethod('post')) {
            $compromisso = Compromisso::create([
                'titulo' => $request->input('titulo'),
                'tipo' => $request->input('tipo'),
                'data_hora' => $request->input('data_hora'),
                'processo_id' => $request->input('processo_id') ?: null,
                'responsavel_id' => $request->input('responsavel_id') ?: auth()->id(),
                'created_by' => auth()->id(),
                'observacoes' => $request->input('observacoes'),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'compromisso' => $compromisso]);
            }

            return redirect()->route('agenda')->with('success', 'Compromisso incluído com sucesso.');
        }

        return view('agenda', $this->formData('incluir'));
    }

    public function alterar(UpdateCompromissoRequest $request)
    {
        if ($request->isMethod('post')) {
            $compromisso = Compromisso::query()->findOrFail((int) $request->input('id'));

            $compromisso->update([
                'titulo' => $request->input('titulo'),
                'tipo' => $request->input('tipo'),
                'data_hora' => $request->input('data_hora'),
                'processo_id' => $request->input('processo_id') ?: null,
                'responsavel_id' => $request->input('responsavel_id') ?: $compromisso->responsavel_id,
                'status' => $request->input('status'),
                'observacoes' => $request->input('observacoes'),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'compromisso' => $compromisso]);
            }

            return redirect()->route('agenda')->with('success', 'Compromisso atualizado com sucesso.');
        }

        $compromisso = Compromisso::query()->with(['processo', 'responsavel'])->findOrFail((int) $request->input('id'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id' => $compromisso->id,
                'titulo' => $compromisso->titulo,
                'tipo' => $compromisso->tipo,
                'data_hora' => $compromisso->data_hora->format('Y-m-d\TH:i'),
                'processo_id' => $compromisso->processo_id,
                'responsavel_id' => $compromisso->responsavel_id,
                'status' => $compromisso->status,
                'observacoes' => $compromisso->observacoes,
            ]);
        }

        return view('agenda', array_merge($this->formData('alterar'), [
            'compromisso' => $compromisso,
        ]));
    }

    public function concluir(Request $request)
    {
        $validated = $request->validate(['id' => 'required|integer|exists:compromissos,id']);

        $compromisso = Compromisso::findOrFail((int) $validated['id']);
        $compromisso->status = Compromisso::STATUS_CONCLUIDO;
        $compromisso->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Compromisso concluído com sucesso.');
    }

    public function excluir(Request $request)
    {
        $validated = $request->validate(['id' => 'required|integer|exists:compromissos,id']);

        $compromisso = Compromisso::findOrFail((int) $validated['id']);
        $compromisso->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Compromisso excluído com sucesso.');
    }

    /**
     * Feed de eventos para o FullCalendar.
     */
    public function feed(Request $request)
    {
        $query = Compromisso::query()->with('processo');

        if ($request->filled('start')) {
            $query->where('data_hora', '>=', $request->input('start'));
        }

        if ($request->filled('end')) {
            $query->where('data_hora', '<=', $request->input('end'));
        }

        $cores = [
            Compromisso::TIPO_AUDIENCIA => '#dc3545',
            Compromisso::TIPO_PRAZO_FATAL => '#fd7e14',
            Compromisso::TIPO_REUNIAO => '#0d6efd',
            Compromisso::TIPO_OUTRO => '#6c757d',
        ];

        $eventos = $query->get()->map(function (Compromisso $compromisso) use ($cores) {
            return [
                'id' => $compromisso->id,
                'title' => $compromisso->titulo . ($compromisso->processo ? ' — ' . $compromisso->processo->numero_processo : ''),
                'start' => $compromisso->data_hora->toIso8601String(),
                'color' => $cores[$compromisso->tipo] ?? '#6c757d',
                'classNames' => $compromisso->status === Compromisso::STATUS_CONCLUIDO ? ['compromisso-concluido'] : [],
            ];
        });

        return response()->json($eventos);
    }

    private function formData(string $tela): array
    {
        return [
            'tela' => $tela,
            'nome_tela' => 'Agenda',
            'rotaAlterar' => 'alterar-agenda',
            'rotaIncluir' => 'incluir-agenda',
            'tipoOptions' => Compromisso::TIPO_OPTIONS,
            'statusOptions' => Compromisso::STATUS_OPTIONS,
            'processosOptions' => Processo::query()->orderByDesc('id')->pluck('numero_processo', 'id'),
            'responsaveisOptions' => User::query()->orderBy('name')->pluck('name', 'id'),
        ];
    }

    private function baseQuery(Request $request)
    {
        $query = Compromisso::query()->with(['processo', 'responsavel']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('processo_id')) {
            $query->where('processo_id', (int) $request->input('processo_id'));
        }

        return $query;
    }
}
