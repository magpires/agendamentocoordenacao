@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                 @if(Auth::user()->tipo == 'Secretário')
                    {{-- Vericicamos se o coordenador foi selecionado e exibimos seu nome no calendário --}}
                    {{-- para que o secretário saiba em qual calendário ele está --}}
                    @if(isset($coordenador))
                        <div class="card-header text-center"><h2>Calendário de Coordenadores - {{$coordenador->name}} {{$coordenador->sobrenome}}</h2></div>
                    @else
                        <div class="card-header text-center"><h2>Calendário de Coordenadores</h2></div>
                    @endif
                @elseif(Auth::user()->tipo == 'Aluno')
                    {{-- Vericicamos se o coordenador foi selecionado e exibimos seu nome no calendário --}}
                    {{-- para que o aluno saiba em qual calendário ele está --}}
                    @if(isset($coordenador))
                        <div class="card-header text-center"><h2>Calendário de Coordenadores - {{$coordenador->name}} {{$coordenador->sobrenome}}</h2></div>
                    @elseif(isset($meu_calendario))
                        <div class="card-header text-center"><h2>Meu calendário</h2></div>
                    @else
                        <div class="card-header text-center"><h2>Calendário de Coordenadores</h2></div>
                    @endif
                @else
                    <div class="card-header text-center"><h2>Meu calendário</h2></div>
                @endif

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(session()->has('message'))
                        <div class="alert alert-success text-center">
                            {{ session()->get('message') }}
                        </div>
                    @endif 

                    @if(session()->has('error'))
                        <div class="alert alert-danger text-center">
                            {{ session()->get('error') }}
                        </div>
                    @endif

                    @if(Auth::user()->tipo == 'Secretário')
                        <div class="alert alert-primary col-md-10 offset-1">
                            <div>- Para agendar um horário disponível para um determinado 
                                                coordenador, selecione primeiro um coordenador e em seguida, 
                                                clique em um dia livre para agendar a disponibilidade.<br>

                                                - Selecione un dos horários disponíveis no calendário para agendar uma 
                                                reunião entre um coordenador e solicitante ou fechar o horário.<br>

                                                - Selecione uma reunião para ser editada ou cancelada.<br>
                            </div>
                        </div>
                        

                        <div class="offset-1">
                            <div class="form-group row">
                                <label for="coordenador" class="col-md-2 col-form-label text-md-right">Coordenador</label>
                                <select name="select_coordenador" id="select_coordenador" class="form-control col-md-6" required>
                                    <option value="">Selecione o Coordenador</option>
                                    @foreach($coordenadores as $coordenador)
                                        <option value="{{$coordenador->id}}" title="{{$coordenador->name}} {{$coordenador->sobrenome}}">{{$coordenador->name}} {{$coordenador->sobrenome}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('coordenador'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('coordenador') }}</strong>
                                    </span>
                                @endif

                                <a href="" id="btn-select_coordenador" class="btn btn-primary offset-1eMeio">Selecionar coordenador</a>
                            </div>
                        </div>
                    @elseif(Auth::user()->tipo == 'Aluno')
                        @if(isset($meu_calendario))
                            <div class="alert alert-primary col-md-10 offset-1" role="alert">
                                <h5>Este é seu calendário</h5>
                                <p> - Selecione uma das reuniões no calendário para 
                                                        obter mais detalhes sobre o evento.<br>

                                                        - Você pode editar ou cancelar uma reunião logo após selecioná-la.<br>
                                </p>
                            </div>
                        @else
                            <div class="alert alert-primary col-md-10 offset-1">
                                <div>- Para agendar um horário disponível para um determinado 
                                                    coordenador, selecione primeiro um coordenador e em seguida, 
                                                    selecione um horário disponível no calendário 
                                                    para agendar uma reunião com o coordenador.<br>
                                </div>
                            </div>
                            

                            <div class="offset-1">
                                <div class="form-group row">
                                    <label for="coordenador" class="col-md-2 col-form-label text-md-right">Coordenador</label>
                                    <select name="select_coordenador" id="select_coordenador" class="form-control col-md-6" required>
                                        <option value="">Selecione o coordenador</option>
                                        @foreach($coordenadores as $coordenador)
                                            <option value="{{$coordenador->id}}" title="{{$coordenador->name}} {{$coordenador->sobrenome}}">{{$coordenador->name}} {{$coordenador->sobrenome}}</option>
                                        @endforeach
                                    </select>
    
                                    @if ($errors->has('coordenador'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('coordenador') }}</strong>
                                        </span>
                                    @endif
    
                                    <a href="" id="btn-select_coordenador" class="btn btn-primary offset-1eMeio">Selecionar coordenador</a>
                                </div>
                            </div>
                        @endif
                    @elseif(Auth::user()->tipo == 'Coordenador')
                        <div class="alert alert-primary col-md-10 offset-1" role="alert">
                            <h5>Este é seu calendário</h5>
                            <div>- Selecione un dos horários disponíveis no calendário para agendar uma 
                                reunião entre você e um solicitante ou fechar o horário.<br>

                                - Selecione uma reunião para ser editada ou cancelada.<br>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->tipo == 'Secretário')
                        {{-- O calendário apenas será carregado caso um orientador tenha sido selecionado --}}
                        @if(isset($selecionou_coord))
                            <div id='calendar'></div>
                        @endif
                    @else            
                        @if(isset($meu_calendario))
                            <div id='calendar'></div>
                        @elseif(isset($selecionou_coord))
                            <div id='calendar'></div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Excluir --}}
<div class="modal fade" id="excluir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar cancelamento da reunião</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">
                  &times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{route('excluireuniao')}}" method="post">
                    @csrf
                    <p class="mt-2">Tem certeza de que deseja cancelar sua reunião?</p>
                    <input type="" name="id" id="id" required value="2">
                </div>

                <div class="modal-footer">
                    <div class="form-group"> 
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Não</button>
                        <button type="submit" class="btn btn-danger" id="btnDelete">Sim</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Aqui vou incluir os modais --}}
<script>
    $('.btnEditar').on("click", function () {
        $('.form').slideToggle();
        $('.visualizar').slideToggle();
    });

    $('.btnCancEditar').on("click", function () {
        $('.visualizar').slideToggle();
        $('.form').slideToggle();


    });

    $('.btnExcluir').on("click", function () {
        $('#visualizar').modal('hide');
        $('#excluir').modal();
    });

    $(document).ready(function () {
        $('#visualizar').modal('hide');
        $('#excluir').modal();
    });

    // Mascara de data e hora
    //Mascara de Data e Hora
    function DataHora(evento, objeto) {
        var keypress = (window.event) ? event.keyCode : evento.which;
        campo = eval(objeto);
        if (campo.value == '00/00/0000 00:00:00') {
            campo.value = ""
        }

        caracteres = '0123456789';
        separacao1 = '/';
        separacao2 = ' ';
        separacao3 = ':';
        conjunto1 = 2;
        conjunto2 = 5;
        conjunto3 = 10;
        conjunto4 = 13;
        conjunto5 = 16;
        if ((caracteres.search(String.fromCharCode(keypress)) != -1) && campo.value.length < (19)) {
            if (campo.value.length == conjunto1)
                campo.value = campo.value + separacao1;
            else if (campo.value.length == conjunto2)
                campo.value = campo.value + separacao1;
            else if (campo.value.length == conjunto3)
                campo.value = campo.value + separacao2;
            else if (campo.value.length == conjunto4)
                campo.value = campo.value + separacao3;
            else if (campo.value.length == conjunto5)
                campo.value = campo.value + separacao3;
        } else
            event.returnValue = false;
    }

    $(document).ready(function () {          
        $('#calendar').fullCalendar({
            // themeSystem: 'bootstrap4',
            header: {
                right: 'today,prev,next,month,listMonth'
            },
            
            //Current date
            defaultDate: Date(),
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            // Make the day selectable
            selectable: true,
            //Select the time on the day view
            selectHelper: true,
            eventStartEditable: false,
            eventDurationEditable: false,

            //Can click in a event to view their data
            eventClick: function (event) {
                $('#visualizar #id').text(event.id);
                $('#visualizar #id').val(event.id);

                $('#visualizar #titulo').text(event.title);
                
                //Se não houver aluno agendado, o campo de RM ficará limpo e 
                //disponível para edição.
                //Além disso, o campo título ficará em branco, caso não haja aluno agendado
                if(event.status == 'Disponível') {
                    //O rm só terá o valor vazio caso um secretário ou coordenador esteja cadastrando um aluno.
                    //Se for aluno, automaticamente receberar o RM do aluno logado
                    @if(Auth::user()->tipo == 'Secretário' || Auth::user()->tipo == 'Coordenador')
                        $('#visualizar #rm').val('');
                    @else
                        $('#visualizar #rm').val('{{Auth::user()->rm}}');
                    @endif
                    $('#visualizar #titulo').val('');
                    $('#visualizar #rm').prop("readonly", false);
                } else {
                    $('#visualizar #titulo').val(event.title);
                    $('#visualizar #rm').val(event.rm);
                    $('#visualizar #rm').prop("readonly", true);
                }
                
                $('#visualizar #curso').text(event.curso);
                $('#visualizar #curso').val(event.curso);

                $('#visualizar #disciplina').text(event.disciplina);
                $('#visualizar #disciplina').val(event.disciplina);

                $('#visualizar #professor').text(event.professor);
                $('#visualizar #professor').val(event.professor);

                $('#visualizar #aluno').text(event.aluno);
                $('#visualizar #aluno').val(event.aluno);

                $('#visualizar #rm').text(event.rm);

                $('#visualizar #contato').text(event.contato);
                $('#visualizar #contato').val(event.contato);

                $('#visualizar #status').text(event.status);
                $('#visualizar #novoStatus').val(event.status);

                if(event.status == 'Disponível') {
                    $('#visualizar #cancela_reuniao').show();
                    $('#visualizar #agenda_edita').show();
                    $('#visualizar #exibeStatus').hide();
                    $('#visualizar #novoStatus').attr('required', false);
                } else if(event.status == 'Concluída') {
                    $('#visualizar #cancela_reuniao').hide();
                    $('#visualizar #agenda_edita').hide();
                } else {
                    $('#visualizar #cancela_reuniao').show();
                    $('#visualizar #agenda_edita').show();
                    $('#visualizar #exibeStatus').show();
                    $('#visualizar #novoStatus').attr('required', true);
                }

                $('#visualizar #secretario').text(event.secretario);
                $('#visualizar #secretario').val(event.secretario);

                $('#visualizar #start').text(event.start.format('DD/MM/YYYY HH:mm'));
                $('#visualizar #start').val(event.start.format('DD/MM/YYYY HH:mm'));
                $('#visualizar #end').text(event.end.format('DD/MM/YYYY HH:mm'));
                $('#visualizar #end').val(event.end.format('DD/MM/YYYY HH:mm'));

                $('#visualizar #agenda_edita').text(event.agenda_edita);
                $('#visualizar #cancela_reuniao').text(event.cancela_reuniao);
                
                @if(Auth::user()->tipo == 'Orientador')
                    $('#visualizar #cancela_reuniao').hide();
                    $('#visualizar #agenda_edita').hide();
                @endif

                @if(!Auth::user() == 'Secretário')
                    if(event.id_aluno != {{Auth::user()->id}}) {
                        $('#visualizar #cancela_reuniao').hide();
                    } else {
                        $('#visualizar #cancela_reuniao').show();
                    }
                @endif

                $('#excluir #id').text(event.id);
                $('#excluir #id').val(event.id);
                $('#visualizar').modal();
            },

            // Somente secretários e coordenadores podem criar disponibilidade
            @if(Auth::user()->tipo == 'Secretário' || Auth::user()->tipo == 'Coordenador')
                //Verificamos se o secretário selecionou o professor e assim exibimos o modal de
                //cadastro de disponibilidade
                @if(isset($selecionou_coord))
                    // Can select a date or a group of dates
                    select: function (start, end, event) {
                        var today = moment();
                        start.set({
                            hours: today.hours(),
                        });

                        $("#cadastrar #start").val(start.format('DD/MM/YYYY HH:mm'));
                        $("#cadastrar #end").val(start.add(30, 'minute').format('DD/MM/YYYY HH:mm'));
                        $('#cadastrar').modal();

                        //Esta função serve para adicionar 30 minutos ao tempo final da reunião
                        // toda vez que o usuário altera alguma coisa no campo de data inicial
                        $(document).on('change', '#cadastrar #start', function() {
                            var startModal = moment($('#cadastrar #start').val());
                            console.log(startModal);

                            start.set({
                                hours: startModal.hours(),
                                minutes: startModal.minutes(),
                            });

                            $("#cadastrar #end").val(start.add(30, 'minute').format('DD/MM/YYYY HH:mm'));
                            console.log($("#cadastrar #end").val());
                        })
                    },
                @endif
            @endif

            // Events from the Database
            events: [
                @foreach($reunioes as $reuniao)
                    
                    //Verificamos se o usuário está no calendário do coordenador,
                    //mas isso só vai acontecer, caso ele não seja um secretário ou coordenador
                    @if(Auth::user()->tipo != 'Secretário' || Auth::user()->tipo != 'Coordenador')
                        @if(isset($orientador))
                            // O if serve para esconder o botão de cancelar reuniao
                            // caso ele seja uma disponibilidade de agendamento
                            @if($reuniao->status == 'Disponível')
                                $('#visualizar #cancela_reuniao').hide(),
                            @else
                                $('#visualizar #cancela_reuniao').show(),
                            @endif
                        @endif
                    @endif

                    {
                        id: '{{$reuniao->id}}',
                        id_solicitante: '{{$reuniao->id_solicitante}}',
                        title: '{{$reuniao->titulo}}',
                        coordenador: '{{$reuniao->coordenador_name}} {{$reuniao->coordenador_sobrenome}}',
                        @if($reuniao->status == 'Disponível')
                            rm: 'Nenhum solicitante agendado',
                            curso: 'Nenhum solicitante agendado',
                            solicitante: 'Nenhum solicitante agendado',
                            contato: 'Nenhum solicitante agendado',
                            cancela_reuniao: 'Fechar horário',
                        @else
                            rm: '{{$reuniao->solicitante_rm}}',
                            curso: '{{$reuniao->solicitante_curso}}',
                            disciplina: '{{$reuniao->solicitante_disciplina}}',
                            solicitante: '{{$reuniao->solicitante_name}} {{$reuniao->solicitante_sobrenome}}',
                            contato: '{{$reuniao->solicitante_telefone}}',
                            cancela_reuniao: 'Cancelar reunião',
                        @endif
                        status: '{{$reuniao->status}}',
                        secretario: '{{$reuniao->secretario_nome}} {{$reuniao->secretario_sobrenome}}',
                        start: '{{$reuniao->start}}',
                        end: '{{$reuniao->end}}',
                        @if($reuniao->status == 'Disponível')
                            agenda_edita: 'Agendar reunião',
                        @else
                            agenda_edita: 'Editar reunião',
                        @endif

                        color: '#00285B', // an option!
                        textColor: '#FFFFFF', // an option!
                    },
                @endforeach
            ],
        });
    });

    //Script para atribuir link para o calendário de um coordenador
    $(document).on('click', '#select_orientador', function() {
        var id = $(this).val();
        if(id == 0) {
            $('#btn-select_orientador').attr('href', 'http://localhost/agendamento/calendariocoordenadores/');
        } else {
            $('#btn-select_orientador').attr('href', 'http://localhost/agendamento/calendariocoordenadores/'+ id);
        }
    })

    //Aqui vem a máscara do RM
    $(document).on('keypress', '#rm', function(e) {
        var square = document.getElementById("rm");
        var key = (window.event)?event.keyCode:e.which;
        if((key > 47 && key < 58)) {
            return true;  
        } else {
            return (key == 8 || key == 0)?true:false;          
        }
    });

    $(function () {
        $("#contato").mask("(99) 9 9999-9999");
        $("#contato").on("blur", function () {
            var last = $(this).val().substr($(this).val().indexOf("-") + 1);
            if (last.length == 5) {
                var move = $(this).val().substr($(this).val().indexOf("-") + 1, 1);
                var lastfour = last.substr(1, 4);
                var first = $(this).val().substr(0, 9);
                $(this).val(first + move + '-' + lastfour);
            }
        });
    });

    @if(session()->has('erroCadastro'))
        //Se houver algum erro no cadastro, nós chamamos o modal de cadastro novamente
        $(document).ready(function () {
            $('#cadastrar').modal();
        });
    @endif

    @if(session()->has('erroUpdate'))
        //Se houver algum erro no cadastro, nós chamamos o modal de cadastro novamente
        $(document).ready(function () {
            $('#visualizar').modal();
            $('.visualizar').slideToggle();
            $('.form').slideToggle();
        });
    @endif
</script>

@endsection
