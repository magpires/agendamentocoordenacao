@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">Informe os seus dados</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                id="name" name="name"
                                value="{{ old('name') }}"
                                placeholder="Informe o seu nome"
                                required>
    
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
    
                            <div class="col">
                                <label for="sobrenome">Sobrenome</label>
                                <input type="text" class="form-control{{ $errors->has('sobrenome') ? ' is-invalid' : '' }}"
                                id="sobrenome" name="sobrenome"
                                value="{{ old('sobrenome') }}"
                                placeholder="Informe o seu sobrenome"
                                required>
    
                                @if ($errors->has('sobrenome'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sobrenome') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
    
                        <div class="form-group row">
                            <div class="col">
                                <label for="telefone">Telefone</label>
                                <input type="text" class="form-control{{ $errors->has('telefone') ? ' is-invalid' : '' }}"
                                id="telefone" name="telefone"
                                value="{{ old('telefone') }}"
                                placeholder="Informe o seu telefone de contato"
                                maxlength="15"
                                required>
    
                                @if ($errors->has('telefone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('telefone') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col">
                                <label for="tipo">Tipo</label>
                                <select name="tipo" id="tipo" class="form-control{{ $errors->has('tipo') ? ' is-invalid' : '' }}"
                                required>
                                    <option value="">Selecione</option>
                                    <option value="Aluno" title="Aluno">Aluno</option>
                                    <option value="Professor" title="Professor">Professor</option>
                                    <option value="Funcionário" title="Funcionário">Funcionário</option>
                                    <option value="Público Externo" title="Público Externo">Público Externo</option>
                                </select>
    
                                @if ($errors->has('tipo'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('tipo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
    
                        <div class="form-group row exibeRMECurso" style="display: none;">
                            <div class="col">
                                <label for="rm">RM</label>
                                <input type="rm" class="form-control{{ $errors->has('rm') ? ' is-invalid' : '' }}"
                                id="rm"name="rm"
                                value="{{ old('rm') }}"
                                maxlength="10"
                                placeholder="Informe o seu RM"
                                onkeypress="mascara(this, mRM)"
                                required>
    
                                @if ($errors->has('rm'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('rm') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col">
                                <label for="curso">Curso</label>
                                <select name="curso" id="curso" class="form-control{{ $errors->has('curso') ? ' is-invalid' : '' }}"
                                required>
                                    <option value="">Selecione o Curso</option>
                                    <option value="Administração - Matutino" title="Administração - Matutino" >Administração - Matutino</option>
                                    <option value="Administração - Noturno" title="Administração - Noturno" >Administração - Noturno</option>
                                    <option value="Análise de Sistemas" title="Análise de Sistemas" >Análise de Sistemas</option>
                                    <option value="Arquitetura e Urbanismo - Matutino" title="Arquitetura e Urbanismo - Matutino" >Arquitetura e Urbanismo - Matutino</option>
                                    <option value="Arquitetura e Urbanismo - Noturno" title="Arquitetura e Urbanismo - Noturno" >Arquitetura e Urbanismo - Noturno</option>
                                    <option value="Ciências Biológicas - Matutino" title="Ciências Biológicas - Matutino" >Ciências Biológicas - Matutino</option>
                                    <option value="Ciências Biológicas - Noturno" title="Ciências Biológicas - Noturno" >Ciências Biológicas - Noturno</option>
                                    <option value="Ciências Contábeis" title="Ciências Contábeis" >Ciências Contábeis</option>
                                    <option value="Direito - Matutino" title="Direito - Matutino" >Direito - Matutino</option>
                                    <option value="Direito - Noturno" title="Direito - Noturno" >Direito - Noturno</option>
                                    <option value="Educação Física - Matutino" title="Educação Física - Matutino" >Educação Física - Matutino</option>
                                    <option value="Educação Física - Noturno" title="Educação Física - Noturno" >Educação Física - Noturno</option>
                                    <option value="Enfermagem - Matutino" title="Enfermagem - Matutino" >Enfermagem - Matutino</option>
                                    <option value="Enfermagem - Noturno" title="Enfermagem - Notuno" >Enfermagem - Noturno</option>
                                    <option value="Engenharia Civil - Matutino" title="Engenharia Civil - Matutino" >Engenharia Civil - Matutino</option>
                                    <option value="Engenharia Civil - Noturno" title="Engenharia Civil - Noturno" >Engenharia Civil - Noturno</option>
                                    <option value="Engenharia de Computação" title="Engenharia de Computação" >Engenharia de Computação</option>
                                    <option value="Engenharia de Produção - Matutino" title="Engenharia de Produção - Matutino" >Engenharia de Produção - Matutino</option>
                                    <option value="Engenharia de Produção - Noturno" title="Engenharia de Produção - Notuno" >Engenharia de Produção - Notuno</option>
                                    <option value="Engenharia de Software" title="Engenharia de Software" >Engenharia de Software</option>
                                    <option value="Engenharia Mecânica" title="Engenharia Mecânica" >Engenharia Mecânica</option>
                                    <option value="Farmácia - Matutino" title="Farmácia - Matutino" >Farmácia - Matutino</option>
                                    <option value="Farmácia - Notuno" title="Farmácia - Noturno" >Farmácia - Notuno</option>
                                    <option value="Filosofia" title="Filosofia" >Coord. Curso - Serviço Social</option>
                                    <option value="Fisioterapia - Matutino" title="cFisioterapia - Matutino" >Fisioterapia - Matutino</option>
                                    <option value="Fisioterapia - Noturno" title="Fisioterapia - Noturno" >Fisioterapia - Noturno</option>
                                    <option value="Nutrição - Matutino" title="Nutrição - Matutino" >Nutrição - Matutino</option>
                                    <option value="Nutrição - Noturno" title="Nutrição - Noturno" >Nutrição - Notuno</option>
                                    <option value="Psicologia" title="Psicologia" >Psicologia</option>
                                    <option value="Serviços Sociais - Matutino" title="Serviços Sociais - Matutino" >Serviços Sociais - Matutino</option>
                                    <option value="Serviços Sociais - Notuno" title="Serviços Sociais - Noturno" >Serviços Sociais - Notuno</option>
                                    <option value="Sistemas de Informação - Noturno"
                                    title="Sistemas de Informação - Noturno" >Sistemas de Informação - Noturno</option>
                                    <option value="Tecnologia e Analise em Desenvolvimento de Sistemas" title="TADS - Noturno" >TADS - Noturno</option> 
                                    <option value="Tecnologia em Design de Interiores" title="Tecnologia em Design de Interiores" >Tecnologia em Design de Interiores</option>
                                    <option value="Tecnologia em Logística" title="Tecnologia em Logística" >Tecnologia em Logística</option>
                                    <option value="Tecnologia em Redes de Computadores - Noturno"
                                    title="Tecnologia em Redes de Computadores - Noturno" >Redes de Computadores - Noturno</option>
                                </select>
    
                                @if ($errors->has('curso'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('curso') }}</strong>
                                    </span>
                                 @endif
                            </div>
                        </div>
    
                        <div class="form-group row">
                            <div class="col">
                                <label for="email">E-Mail</label>
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}
                                col-md-6"
                                id="email" name="email"
                                value="{{ old('email') }}"
                                placeholder="Informe o seu e-mail"
                                required>
    
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
    
                        <div class="form-group row">
                            <div class="col">
                                <label for="password">Senha</label>
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                id="password" name="password"
                                placeholder="Informe a sua senha"
                                required>
    
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
    
                            <div class="col">
                                <label for="password-confirm">Confirmar senha</label>
                                <input type="password" class="form-control"
                                id="password-confirm" name="password_confirmation"
                                placeholder="Confirme a sua senha"
                                required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-5">
                                <button type="submit" class="btn btn-primary">
                                    Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Máscara de telefone
    $(function () {
        $("#telefone").mask("(99) 99999-9999");
        $("#telefone").on("blur", function () {
            var last = $(this).val().substr($(this).val().indexOf("-") + 1);
            if (last.length == 5) {
                var move = $(this).val().substr($(this).val().indexOf("-") + 1, 1);
                var lastfour = last.substr(1, 4);
                var first = $(this).val().substr(0, 9);
                $(this).val(first + move + '-' + lastfour);
            }
        });
    });

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

    $(document).on('click', '#tipo', function() {
        if($('#tipo').val() == 'Aluno' || $('#tipo').val() == 'Professor') {
            $('.exibeRMECurso').show();
            $('#curso').attr('required', true);
            $('#curso').attr('disabled', false);
            $('#rm').attr('required', true);
            $('#rm').attr('disabled', false);
        } else {
            $('.exibeRMECurso').hide();
            $('#curso').val('').attr('required', false);
            $('#curso').attr('disabled', true);
            $('#rm').val('').attr('required', false);
            $('#rm').attr('disabled', true);
        }
    })
</script>
@endsection
