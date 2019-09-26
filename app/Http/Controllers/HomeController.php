<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reuniao;
use App\User;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        if (Auth::user()->tipo == 'Aluno') {
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id_solicitante', Auth::user()->id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso',]);

            $coordenadores = User::query()
                    ->where('users.tipo', 'coordenador')
                    ->get();

            //Exibimos o calendário do usuário logado
            $meu_calendario = true;
            
            return view('home', compact('reunioes', 'coordenadores', 'meu_calendario'));
        }

        if (Auth::user()->tipo == 'Coordenador') {
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id_coordenador', Auth::user()->id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso',]);
        }

        if (Auth::user()->tipo == 'Secretário') {
            return redirect()->route('calendariocoordenadores');
            Auth::logout();
        }

        //Exibimos o calendário do usuário logado
        $meu_calendario = true;

        return view('home', compact('reunioes', 'meu_calendario'));
        
        return view('home');
    }
}
