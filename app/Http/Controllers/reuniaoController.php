<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Reuniao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Gate;
use App\Http\Controllers\Controller;

class reuniaoController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    //Aqui será implementada a função converte data, mas creio que nem irei precisar usá-la.
    public function converteData($data) {
        //Converter a data e hora do formato brasileiro para o formato do Bando de Dados
        $dataAux = explode(" ", $data);

        //Cria uma Array de datas
        list($dataAux, $hora) = $dataAux;

        //Inverter a data de DD/MM/YYY para YYY/MM/DD e tirar o "/" usando explode
        $data_invertida = array_reverse(explode("/", $dataAux));

        //Adicionar o "-" no Banco de Dados
        $data_invertida = implode("-", $data_invertida);

        //Adicionando a hora na nova variavel data
        $data_invertida = $data_invertida . " " . $hora;

        return $data_invertida;
    }

    //Função responsável por criar um horário disponível no calendário do professor.

    // OBS: Secretário e Coordenador tem formas diferentes de cadastrar a disponibilidade. Isso será arrumado
    // posteriormente
    public function criaDisponibilidade(Request $request) {

        $coordenador = User::findOrFail($request['id_coordenador']);
        
        if (Auth::user()->tipo == 'Secretário') {

            //Verificamos se o coordenador informado é realmente um coordenador. Caso contrário,
            //retornamos uma mensagem de erro
            if($coordenador->tipo != 'coordenador') {
                return redirect()->back()->with('error', "O usuário informado não é um coordenador.");
            }

            $validator = Validator::make($request->all(), [
                'id_coordenador' => ['required'],
                'local' => ['required', 'max:191'],
                'start' => ['required'],
                'duracao' => ['required'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('erroCadastro', '') //É retornado para a view saber qual modal deve ser
                                                            //exibido
                    ->withErrors($validator)
                    ->withInput();
            }

            //Converte as data inicial e final para o formato do banco
            // $start_invertida = $this->converteData($request['start']);
            // $end_invertida = $this->converteData($request['end']);

            // Criamos a data base que terá o acréscimo dos minutos. Vamos criá-la  e em seguida, converte-la em
            // segundos
            $dataBase = strtotime($request['start']);
            
            // Vamos converter a duração da reunião em segundos para que então a gente
            // possa adicionar esta duração a data final da reuniao.
            $duracaoEmSegundos = $request['duracao'] * 60;

            // Por fim, criamos a data final da reunião, somando a data base mais a duração em segundos
            $dataFinal = $dataBase + $duracaoEmSegundos;

            Reuniao::create([
                'titulo' => 'Disponível',
                'id_coordenador' => $request['id_coordenador'],
                'id_solicitante' => $request['id_solicitante'],
                'id_secretario' => Auth::user()->id,
                'local' => $request['local'],
                'start' => $dataBase,
                'end' => $dataFinal,
            ]);

            return redirect()->back()->with('message', "Disponibilidade cadastrada com sucesso");
        } else {
            return redirect()->back()->with('error', "Você não tem permissão para fazer esta operação.");
        }
    }

    //Função responsável por agendar ou alterar uma reunião com o professor

    //Será adaptada posteriormente
    public function agendaReuniao(Request $request) {       
        try {
            $mensagem; //É por ela que vamos informar ao usuário o que acontece no sistema

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'titulo' => 'required|string|max:255|',
                'rm' => 'required|string|max:10|sometimes|',
                'novoStatus' => 'required|sometimes|',
                'start' => 'required|sometimes|',
                'end' => 'required|sometimes|',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('erroUpdate', '') //É retornado para a view saber qual modal deve ser
                                                            //exibido
                    ->withErrors($validator)
                    ->withInput();
            }

            //Converte as data inicial e final para o formato do banco
            $start_invertida = $this->converteData($request['start']);
            $end_invertida = $this->converteData($request['end']);

            //Somente secretários podem alterar a data de uma reunião ou de uma disponibilidade
            if (Auth::user()->tipo == 'Secretário') {
                $reuniao = Reuniao::findOrFail($request['id']);
                
                //Precisamos saber se o secretário está cadastrando uma reunião ou alterando o título de uma reunnião.
                //para não alterarmos seus campos "em_espera" e "reunioes_agendadas" quando ele apenas atualizar
                //o título de sua reunião.
                if($reuniao->status == 'Disponível') {
                    //Antes de agendar uma reunião, vamos procurar o aluno pelo seu RM
                    $aluno = User::query()
                            ->where('users.rm', $request['rm'])
                            ->first();

                    //Se o RM digitado for incorreto, falamos que o aluno não foi encontrado
                    if($aluno == null) {
                        $mensagem = 'Aluno não encontrado.';
                        return redirect()->back()->with('error', $mensagem);
                    }

                    //Antes de agendarmos a reunião do aluno, verificamos se ele já não 
                    //excedeu o limite máximo de agendamentos permitidos
                    if($aluno->reunioes_agendadas >= 2) {
                        $mensagem = 'O aluno informado não pode mais agendar reuniões, 
                                        pois ele excedeu o limite máximo de reuniões 
                                        marcadas.';
                        
                        return redirect()->back()->with('error', $mensagem);
                    }

                    //Verificamos também se ele já não está esperando pela primeira reunião.
                    //com o seu orientador. Caso ele já tenha uma reunião marcada, ele ficará
                    //impossibilitado de realizar a segunda reunião
                    if($aluno->em_espera) {
                        $mensagem = 'O aluno informado não pode agendar reuniões, pois 
                                        já existe uma reunião para ser realizada 
                                        em seu calendário.';

                        return redirect()->back()->with('error', $mensagem);
                    }

                    //Se o RM pertencer a um aluno que faz TCC II, vamos verificar primeiro se ele escolheu 
                    // o professor dono do calendário do qual o secretário está e deppis vamos retornar outra mensagem
                    //de erro, caso esse aluno não tenha escolhido este professor
                    if($aluno->disciplina == 'TCC II') {
                        if($aluno->id_orientador != $reuniao->id_orientador) {
                            $mensagem = 'O aluno informado faz a disciplina TCC II e 
                                            este não é o seu orientador.';
                            
                            return redirect()->back()->with('error', $mensagem);
                        }
                    }

                    //Quando o aluno agenda uma reunião, nós atualizamos sua quantidade
                    //de agendamentos feitos no sistema
                    $reunioes_agendadas = $aluno->reunioes_agendadas + 1;

                    Reuniao::findOrFail($request['id'])
                    ->update([
                        'titulo' => $request['titulo'],
                        'id_aluno' => $aluno->id,
                        'id_secretario' => Auth::user()->id,
                        'status' => "Em espera",
                    ]);

                    User::findOrFail($aluno->id)
                    ->update([
                        'reunioes_agendadas' => $reunioes_agendadas,
                        'em_espera' => true,
                    ]);

                    $mensagem = 'Reunião agendada com sucesso.';
                } else if($reuniao->status == 'Em espera' || $reuniao->status == 'Marcada') {
                    //Só vamos atualizar o status da reuniao caso ele seja informado
                    if($request['novoStatus'] == '') {
                        Reuniao::findOrFail($request['id'])
                        ->update([
                            'titulo' => $request['titulo'],
                            'start' => $start_invertida,
                            'end' => $end_invertida,
                        ]);
                    } else {
                        Reuniao::findOrFail($request['id'])
                        ->update([
                            'titulo' => $request['titulo'],
                            'status' => $request['novoStatus'],
                            'start' => $start_invertida,
                            'end' => $end_invertida,
                        ]);

                        $reuniao = Reuniao::findOrFail($request['id']);

                        if($request['novoStatus'] == 'Concluída') {
                            User::findOrFail($reuniao->id_aluno)
                            ->update([
                                'em_espera' => false,
                            ]);
                        }
                    }

                    $mensagem = 'Reunião alterada com sucesso.';
                } else {
                    $mensagem = 'Você está tentando alterar uma reunião já concluída.';
                    
                    return redirect()->back()->with('error', $mensagem);
                }
            } else if (Auth::user()->tipo == 'Aluno') {
                $reuniao = Reuniao::findOrFail($request['id']);
                
                //Precisamos saber se o usuário está cadastrando uma reunião ou alterando o título de uma reunnião.
                //para não alterarmos seus campos "em_espera" e "reunioes_agendadas" quando ele apenas atualizar
                //o título de sua reunião.
                if($reuniao->status == 'Disponível') {
                    //Antes de agendarmos a reunião do aluno, verificamos se ele já não 
                    //excedeu o limite máximo de agendamentos permitidos
                    if(Auth::user()->reunioes_agendadas >= 2) {
                        $mensagem = 'Você não pode mais agendar reuniões, pois excedeu o limite máximo de reuniões 
                                        marcadas.';
                        
                        return redirect()->back()->with('error', $mensagem);
                    }

                    //Verificamos também se ele já não está esperando pela primeira reunião.
                    //com o seu orientador. Caso ele já tenha uma reunião marcada, ele ficará
                    //impossibilitado de realizar a segunda reunião
                    if(Auth::user()->em_espera) {
                        $mensam = 'Você não pode agendar reuniões, pois já existe uma reunião para ser realizada 
                                    em seu calendário.';
                        
                        return redirect()->back()->with('error', $mensagem);
                    }

                    //Quando o aluno agenda uma reunião, nós atualizamos sua quantidade
                    //de agendamentos feitos no sistema
                    $reunioes_agendadas = Auth::user()->reunioes_agendadas + 1;
                    
                    Reuniao::findOrFail($request['id'])
                    ->update([
                        'titulo' => $request['titulo'],
                        'id_aluno' => Auth::user()->id,
                        'status' => "Em espera",
                    ]);

                    User::findOrFail(Auth::user()->id)
                    ->update([
                        'reunioes_agendadas' => $reunioes_agendadas,
                        'em_espera' => true,
                    ]);

                    $mensagem = 'Reunião agendada com sucesso.';
                } else if($reuniao->status == 'Em espera') {
                    Reuniao::findOrFail($request['id'])
                    ->update([
                        'titulo' => $request['titulo'],
                    ]);

                    $mensagem = 'Reunião alterada com sucesso.';
                } else {
                    $mensagem = 'Você está tentando alterar uma reunião já concluída';
                    
                    return redirect()->back()->with('error', $mensagem);
                }
            } else {
                $mensagem = 'Você não tem permissão para fazer esta operação';
                
                return redirect()->back()->with('error', $mensagem);
            }
            return redirect()->back()->with('message', $mensagem);
        } catch (\Exception $e) {
            $mensagem = 'O sistema encontrou um erro inesperado.';
            
            return redirect()->back()->with('error', $mensagem);
        }
    }

    //Mostra o calendário de todos os coordenadores
    public function calendarioCoordenadores() {
        $coordenadores = User::query()
                        ->where('users.tipo', 'coordenador')
                        ->orderBy('name')
                        ->orderBy('sobrenome')
                        ->get(['users.name',
                                'users.sobrenome',
                                'users.id']);
        
        if (Auth::user()->tipo == 'Aluno') {
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS orientador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id_solicitante', Auth::user()->id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso',]);

            if(Auth::user()->disciplina == 'TCC II') {
                $coordenador = User::find(Auth::user()->id_coordenador);
                $id = $coordenador->id;
            }

            //Informamos que o professor não foi selecionado para que então
            //o modal de cadastro de horário disponível não seja exibido
            $selecionou_coord = false;

            return view('home', compact('reunioes', 'coordenador', 'coordenadores', 'selecionou_coord'));
        }

        if (Auth::user()->tipo == 'coordenador') {
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
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso',]);
        }

        if (Auth::user()->tipo == 'Secretário') {
            $coordenadores = User::query()
                        ->where('users.tipo', 'coordenador')
                        ->orderBy('name')
                        ->orderBy('sobrenome')
                        ->get(['users.name',
                                'users.sobrenome',
                                'users.id']);
            
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso',]);

                
                //Informamos que o professor não foi selecionado para que então
                //o modal de cadastro de horário disponível não seja exibido
                $selecionou_coord = false;

                return view('home', compact('reunioes', 'coordenadores', 'selecionou_coord'));
        }

        $coordenador = User::find(Auth::user()->id_coordenador);
        $id = $coordenador->id;

        return view('home', compact('reunioes', 'coordenador'));
    }

    //Mostra o calendário de um coordenador específico
    public function calendarioCoordenador($id) {
        if(Auth::user()->tipo == 'Aluno') {
            $orientador = User::findOrFail($id);
            
            $reunioes = Reuniao::query()
                ->join('users AS aluno', 'reuniaos.id_aluno', 'aluno.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.status', 'Disponível')
                ->where('reuniaos.id_coordenador', $id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'aluno.name AS aluno_name',
                    'aluno.sobrenome AS aluno_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'aluno.rm AS aluno_rm',
                    'coordenador.telefone AS coordenador_telefone',
                    'aluno.telefone AS aluno_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'aluno.curso AS aluno_curso']);


            $orientadores = User::query()
                        ->where('users.tipo', 'coordenador')
                        ->orderBy('name')
                        ->orderBy('sobrenome')
                        ->get(['users.name',
                                'users.sobrenome',
                                'users.id']);
    
            //Informamos que o professor foi selecionado para que então
            //o modal de cadastro de horário disponível seja exibido
            $selecionou_coord = true;
            
            return view('home', compact('reunioes', 'coordenador', 'coordenadores', 'selecionou_coord'));
        } else if (Auth::user()->tipo == 'Secretário') {
            $coordenador = User::findOrFail($id);

            // //Se o id do coordenador passado não for de um coordenador, retornamos uma mensagem de erro
            if($coordenador->tipo != 'coordenador') {
                return redirect()->back()->with('error', "O ID não pertence a um coordenador válido");
            }
            
            $reunioes = Reuniao::query()
                ->join('users AS aluno', 'reuniaos.id_aluno', 'aluno.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id_coordenador', $id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'aluno.name AS aluno_name',
                    'aluno.sobrenome AS aluno_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'aluno.rm AS aluno_rm',
                    'coordenador.telefone AS coordenador_telefone',
                    'aluno.telefone AS aluno_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'aluno.curso AS aluno_curso']);

            $orientadores = User::query()
                            ->where('users.tipo', 'coordenador')
                            ->orderBy('name')
                            ->orderBy('sobrenome')
                            ->get(['users.name',
                                    'users.sobrenome',
                                    'users.id']);
            
            //Informamos que o professor foi selecionado para que então
            //o modal de cadastro de horário disponível seja exibido
            $selecionou_coord = true;

            return view('home', compact('reunioes', 'id', 'coordenadores', 'selecionou_coord', 'coordenador'));
        }else {
            return redirect('home');
        }
    }

    //Função responsável por exibir o histórico de todas as reuniões cadastradas no sistema para os
    //usuários.
    public function historicoReunioes() {     
        //Só secretários podem ver o histórico de reuniões de todo mundo.
        if(Auth::user()->tipo == 'Secretário') {
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.status', '!=', 'Disponível')
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso']);
        } else if (Auth::user()->tipo == 'Aluno'){
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS orientador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.status', '!=', 'Disponível')
                ->where('reuniaos.id_solicitante', Auth::user()->id)
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso']);
        } else {
            $reunioes = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id_coordenador', Auth::user()->id)
                ->where('reuniaos.status', '!=', 'Disponível')
                ->get(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'coordenador.telefone AS coordenador_telefone',
                    'solicitante.telefone AS solicitante_telefone',
                    'secretario.telefone AS secretario_telefone',
                    'solicitante.curso AS solicitante_curso']);
        }

        return view('historicoreunioes', compact('reunioes'));
    }

    //Método que, junto com um jQuery, retorna os dados de uma reuniao instantaneamente para um modal
    public function infoReuniao($id) {
        $reuniao = Reuniao::query()
                ->join('users AS solicitante', 'reuniaos.id_solicitante', 'solicitante.id')
                ->join('users AS coordenador', 'reuniaos.id_coordenador', 'coordenador.id')
                ->join('users AS secretario', 'reuniaos.id_secretario', 'secretario.id')
                ->where('reuniaos.id', $id)
                ->where('reuniaos.status', '!=', 'Disponível')
                ->firstOrFail(['reuniaos.*',
                    'coordenador.name AS coordenador_name',
                    'coordenador.sobrenome AS coordenador_sobrenome',
                    'solicitante.name AS solicitante_name',
                    'solicitante.sobrenome AS solicitante_sobrenome',
                    'solicitante.rm AS solicitante_rm',
                    'secretario.name AS secretario_nome',
                    'secretario.sobrenome AS secretario_sobrenome',
                    'solicitante.telefone AS solicitante_telefone',
                    'solicitante.curso AS solicitante_curso',
                    'solicitante.disciplina AS solicitante_disciplina']);

        return $reuniao;
    }

    //Método que, junto com o jQuery, retorna de forma dinâmica os dados do aluno quando o secretário informa o seu
    //RM
    public function infoSolicitante($rm) {
        //Antes de agendar uma reunião, vamos procurar o aluno pelo seu RM
        $aluno = User::query()
                ->where('users.rm', $rm)
                ->first(['users.name',
                    'users.sobrenome',
                    'users.curso',
                    'users.telefone']);

        //Se o RM digitado for incorreto, retornamos null para o script em forma de string
        if($aluno == null) {
            $mensagem = 'null';
            return $mensagem;
        }

        return $aluno;
    }

    //Esta função cancela uma reunião ou fecha um horário
    public function delete(Request $request) {
        $reuniao = Reuniao::findOrFail($request['id']);

        //Verificamos se a reuniao é uma reunião ou se é apenas um horário disponível
        if($reuniao->status == 'Disponível') {
            //Antes de fechar um horário, verificamos se o usuário é um secretário
            if(Auth::user()->tipo != 'Secretário') {
                return redirect()->back()->with('error', "Você não tem permissão para fazer esta operação");
            }

            Reuniao::destroy($request['id']);
            return redirect()->back()->with('message', "Horário fechado com sucesso.");
        }

        
        //Antes de cancelar a reunião, verificamos se o usuário logado é um aluno, para
        //que possamos fazer controle do que ele pode cancelar ou não
        if(Auth::user()->tipo == 'Aluno') {
            //Antes de cancelar uma reunião, verificamos se a reunião pertence ao aluno,
            //caso ele tente, de alguma forma, cancelar o agendamento de outro aluno
            if(Gate::denies('cancela-reuniao', $reuniao)) {
                return redirect()->back()->with('error', "Você não tem permissão para excluir");
            }
        }
        
        Reuniao::findOrFail($request['id'])
        ->update([
            'titulo' => 'Disponível',
            'id_solicitante' => $reuniao->id_coordenador,
            'status' => "Disponível",
        ]);

        //Como o aluno cancelou a reunião, colocamos como falso o campo
        //em espera
        User::findOrFail($reuniao->id_solicitante)
        ->update([
            'em_espera' => false,
        ]);

        return redirect()->back()->with('message', "Reunião cancelada com sucesso.");
    }
}
