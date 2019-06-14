<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Models\Mensagem;
use App\Models\Usuario;

class AppController extends Action 
{

	// Renderiza a view mailbox
	public function mailbox_view() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* Responsável por recuperar os dados do Profile do usuário, nesse caso apenas a imagem
		*/
		$this->getInfoProfile();

		/* Responsável por recuperar do banco de dados as mensagens
		*/
		$this->getMensagens();
		

		/* Retornam os dados do painel do usuário */
		$this->getInfoPainel();

		/* Renderiza a view mailbox */
		$this->render('mailbox','layout');
	}

	// Renderiza a view mailbox_messagem
	public function mailbox_message_view()
	{
		/* Responsável pela autenticação do usuário.
		*/
		$this->autenticacao();

		/* Renderiza a view mailbox_message */
		$this->render('mailbox-message','layout');
	}

	// Responsável por enviar mensagem
	public function enviar_mensagem() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* Instancia um objeto da classe Mensagem e modifca os valores vindo do formulario e da Session.
		*/
		$mensagem = Container::getModel('mensagem');
		$mensagem->__set('email_destinatario', $_POST['destinatario']);
		$mensagem->__set('email_origem', $_SESSION['email']);
		$mensagem->__set('titulo', $_POST['titulo']);
		$mensagem->__set('descricao', $_POST['descricao']);
		$mensagem->__set('lido', 0);
		$mensagem->__set('enviado', 1);
		$mensagem->__set('data', date('Y-m-d H:i'));

		/* Pego o id_usuario através do email  

		*  Instancia um objeto da classe Usuario para recuperar o id_usuario através do email
		*/
		$usuario = Container::getModel('usuario');
		$usuario->__set('email', $_POST['destinatario']);

		/* Responsável pela busca do id_usuario em relação ao email vinda através da POST['destinatario']
		*/
		$user = $usuario->getId_usuario();

		/* Modifico o id_usuario da class Mensagem para pode fazer referência das mensagem através do id_usuário da classe Usuário */
		$mensagem->__set('id_usuario', $user['id_usuario']);

		/* pegar os emails e comparar com o  para verificar se o email destinatário existe */
		$emails = $usuario->validarEmailExistente();

		/* Verifica se o $_POST['destinatario'] existe.
		*  Caso TRUE => $mensagem->SendMensagem -> Envia a mensagem.
		   modifica a variável $this->view->mensagemEnviado para TRUE para que possa fazer o controle de mensagem

		*  Caso FALSE => modifica a variável $this->view->mensagemEnviado para FALSE, para que possa fazer
		*  o controle de mensagem na view.   
		*/
		foreach ($emails as $value) {
			if ($value['email'] == $_POST['destinatario'] ) {
				$mensagem->sendMensagem();
				$this->view->mensagemEnviado = true;
				/* Necessita do break, quero que ao entrar nesse bloco de comando ele modifica a variável
				*  $this->view->mensagemEnviado para TRUE e saia do foreach, Caso ao contrário, ele modificaria
				*  $this->view->mensagemEnviado para FALSE no momento em que o if fosse falso.
				*/
				break;
			} else {
				// Modifica a $this->view->mensagemEnviado para falso.
				$this->view->mensagemEnviado = false;
			}
		}
		
		// Renderiza a view mailbox-message
		$this->render('mailbox-message','layout');
	}

	// Responsável pela busca de pessoas
	public function search() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		//Apenas em caso de bug, retornar um array vazio em casos de pessoas não encontradas
		$users = Array();

		// Recebe o valor de um $_GET para realizar uma busca, search -> Name do input TEXT da view search
		$pesquisarPor = isset($_GET['search']) ? $_GET['search'] : '';

		// Instancia um objeto da classe Usuário
		$usuario = Container::getModel('Usuario');

		// Modifica o id_usuario de acordo com a $_SESSION['id_usuario']
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);
		
		/* Verifica se o valor do $_GET['search'] é diferente de vazio.
		*  Caso TRUE => modifica a variável nome da classe Usuário pelo o valor vindo do input SEARCH.
        *  Realiza uma busca de pessoas com o possível valor da SEARCH e guarda em $users
		*/
		if ($pesquisarPor != '') {
			$usuario->__set('nome', $pesquisarPor);
			$users = $usuario->getPessoas();
		}	


		// Recebe os valores de $users -> Retorno de uma consulta de nomes de usuários, e passa o valor para search
		$this->view->usuarios = $users;

		// Renderiza a página Search
		$this->render('search', 'layout');
	}

	// Responsável por renderiza a view e retornar os valores do painel e do profile
	public function profile_view() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* Retorna os dados do profile */
		/* Retornam os dados do painel */
		$this->getInfoProfile();

		// Renderiza a view profile
		$this->render('profile', 'layout');
	}

	// Responsável por alterar os dados do profile
	public function alterar_profile() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* Instancia um objeto da classe Usuário e modifica os valores dos atributos da classe usuario,
		*  de acordo com os dados vindo do formulário ou da Session
		*/
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('cpf', $_POST['cpf']);
		$usuario->__set('data_nascimento', $_POST['data_nascimento']);
		$usuario->__set('sexo', $_POST['sexo']);
		$usuario->__set('telefone', $_POST['telefone']);

		
		/* Caso o JS se desativado, evitar que o botao alterar do profile, altere os dados recebendo como value vazio 
		*  $this->getValidaDados() -> Responsável por validar os dados vindo do formulário
		*/
		if ($this->getValidaDados()) {
			// Responsável por modificar os valores do profile
			$usuario->setDadosProfile();
		}

		/* Como a rota /profile faz referência a function profile_view, automaticamente ele já retorna os dados
		*  do profile e do painel.	
		*/
		header('Location: /profile');
	}

	// Renderiza a view Alterar_senha_view
	public function alterar_senha_view() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* Responsável por recuperar os dados do profile 
		*/
		$this->getInfoProfile();

		// Renderiza a view Alterar senha
		$this->render('alterar-senha', 'layout');
	}


	// Responsável por alterar senha
	public function alterar_senha() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();
		
		// Instancia um objeto da classe Usuario e modifica o valor do id_usario de acordo com a $_SESSION
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);
		
		/* Pega o valor do input Senha Atual para que possa ser comparado */
		$senhaAtual = md5($_POST['senha_atual']);

		// Retorna os dados de um usuário de acordo com o id_usuario
		$this->view->profile = $usuario->getInfo();
		
		/* Verifica se a senha retornada da consulta do Banco de dados for igual a senha vinda do formulario.
		*  Caso TRUE => Modifica as variáveis (senha, confirmar_senha) da classe Usuario e altera a senha.
		*/
		if ($this->view->profile['senha'] == $senhaAtual) {
			$usuario->__set('senha', md5($_POST['senha']));
			$usuario->__set('confirmar_senha', md5($_POST['senha_confirm']));
			// Método para alterar a senha
			$usuario->alterarSenha();
		}

		// Renderiza a index
		header('Location: /');
	}

	// Responsável por deletar mensagem do banco de dados
	public function deletar_mensagem() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		// Cria um objeto da classe Mensagem
		$mensagem = Container::getModel('mensagem');
		
		/* Cria uma variável chamada pieces -> Será responsável por guardas valores dos checkbox.
		*  Aplica-se explode para que possa desmembrar a string vinda do formulário, passada pelo JS.
		*/
		$pieces = explode(",", $_POST['checkbox']);
		
		/* Faz uma iteração entre os dados da variável pieces e ao mesmo tempo moificando o id_mensagem
		*  de acordo com o valor do $piece
		*/
		foreach ($pieces as $checkbox) {
			$mensagem->__set('id_mensagem', $checkbox);

			// Deleta a mensagem de acordo com o id_mensagem vindo dos checkbox da view mailbox
			$mensagem->deletarMensagem();
		}

		/* Ao dar um header no /mailbox, como ele já tem o método getInfoProfile() / getMensagens() implementado, não é necessario 
		*  implementá-lo nesse método.
		*/
		//header('Location: /mailbox');
	}

	// Responsável por renderizar a view alterar-img
	public function alterar_imagem_view() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		// Retorna os dados do profile do usuario para que possa ser passada para view alterar-img.
		$this->getInfoProfile();

		// Renderiza a view alterar-img.
		$this->render('alterar-img', 'layout');
	}

	// Responsável por alterar a imagem de perfil
	public function alterar_imagem() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		/* $extensao -> Guarda o valor da extensão de uma imagem (.png)	
		*  strtolower -> Deixa os valores em caixa baixa.
		*  substr -> quebra a string $_FILES['imagem']['name'] a partir das últimas casas.
		*/
		$extensao  = strtolower(substr($_FILES['imagem']['name'], -4));

		// Gera um nome aleatório e concatena com a $extensao
		$novo_nome = md5(time()).$extensao;

		/* Define o diretório na qual as imagens ficarão guardadas, para que em breve possa ser comparadas
		*  com os nomes vindo do banco de dados e consequentemente exibidas.
		*/
		$diretorio = 'upload/';

		/* function nativa do php, para movimentar a imagem que está na pasta tmp para a pasta que foi definida acima.
		*/
		move_uploaded_file($_FILES['imagem']['tmp_name'],  $diretorio.$novo_nome);

		// Criar um objeto da classe Usuario e modifica os valores de acordo com os parâmetros passados.
		$usuario = Container::getModel('Usuario');
		$usuario->__set('imagem', $novo_nome);
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);

		// Responsável por modificar a imagem de perfil do usuário.
		$usuario->setImagemPerfil(); 

		/* Ao dá um header no /profile, ele já tem implementado o método getInfoProfile() - Que retorna os dados 
		*  do usuário e do painel de usuário
		*/
		header('Location: /profile');
	}

	// Responsável pela autenticação de toda a aplicação e evita que o usuário acesse a url diretamente.
	public function autenticacao() 
	{
		// Starta um session
		session_start();

		/* Verifica se a $_SESSION['id_usuario'] não existe OU
		*  $_SESSION['id_usuario'] seja null ou vazia OU
		*  se a $_SESSION['nome'] não existe OU
		*  $_SESSION['nome'] seja null ou vazia
		* 
		*  Caso TRUE => Dá um redirect para a url index com um parâmetro de erro, para que possa 
		*  exibir uma mensagem de controle.
		*/
		if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		}
	}

	// Responsável por recuperar as mensagens do banco de dados
	public function getMensagens() 
	{
		/* Trazer as mensagens através do ID do usuario e não do email do usuário, para quando um usuário ser deletado, e 
		* depois criado novamente com o mesmo email, as mensagens que já tinha vinculados a aquele email anterior 
		* (Excluído), não estejam mais lá.
		*/

		// Criar um objeto da classe Mensagem e modifica seu ID de acordo com a $_SESSION['id_usuario']
		$mensagem = Container::getModel('Mensagem');
		$mensagem->__set('id_usuario', $_SESSION['id_usuario']);
		
		// Limite -> Define o limite de registros por página
		$limite = 2;

		/* Verifica se o valor vindo via URL é vazia.
		*  Caso TRUE -> Define por padrão o valor 1.
		*  Caso FALSE -> Define o valor da página(valor) clicada 
		*/
		$paginacao = empty($_GET['pag']) ? 1: $_GET['pag'];

		// Inicio = Define o valor de inicio de cada valor em cada página
		$inicio = ($limite * $paginacao) - $limite;

		// Define a última página dos registros da paginação
		$ultima_pagina = ceil(count($mensagem->getMensagens()) / $limite);
		
		/* Passa o valor da $ultima_pagina para view, para que possa ser comparada
		*  e verificada para controle de exibição dos botões de ida e volta.
		*/
		$this->view->ultima_pagina = $ultima_pagina;

		/* Passa os valores da consulta do getMensagensLimit para a variável
		*  $this->view->mensagem, para que possa ser iterada na view e exibida posteriormente.
		*/
		$this->view->mensagem = $mensagem->getMensagensLimit($inicio,$limite);
		
		/* Verifica se a consulta retornou valor ou não
		*  Caso TRUE => Altera o valor da variável $this->view->mensagem_controle para TRUE.
		*  Caso FALSE => Altera o valor da variável $this->view->mensagem_controle para FALSE.
		*/
		if (!$mensagem->getMensagens()) {
			// Controle de mensagem na view
			$this->view->mensagem_controle = true;
		} else {
			// Controle de mensagem na view
			$this->view->mensagem_controle = false;
		}
	}

	// Retorna os dados do perfil
	public function getInfoProfile() 
	{
		// Instancia um objeto da classe Usuario e modifica o valor do id_usuario de acordo com o parâmetro
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);

		// Guarda o valor da consulta dos dados do perfil do usuário.
		$dadosProfile = $usuario->getInfo();

		// Retorna os dados do painel de usuário.
		$this->getInfoPainel();

		// Passa os valor da consulta do dados da info do usuário para a variável $this->view->profile.
		$this->view->profile = $dadosProfile;
	}

	// Responsável por recuperar os dados do painel do usuário
	public function getInfoPainel() 
	{
		// Instancia um objeto da classe Mensagem e modifica os valores do seus atributos de acordo com os parâmetros
		$mensagem = Container::getModel('Mensagem');
		$mensagem->__set('id_usuario', $_SESSION['id_usuario']);
		$mensagem->__set('email_origem', $_SESSION['email']);

		//  Guarda a quantidade de mensagens na variável $this->view->qtdMensagem
		$this->view->qtdMensagem = $mensagem->getCountMensagens();

		/* Parâmetro - Tipo (Lido / Nao_Lido) e Boolean (1 - Lido /0 - Não Lido) */
		/* Para reaproveitamente de código, foi utilizado passagem de parâmetros e concatenado na function
		*  para definir se a contagem da busca de mensagens é Lido, ou mensagem Não lido.
		*/
		$this->view->qtdMensagemLido = $mensagem->getCountLido('Lido',1);
		$this->view->qtdMensagemNaoLido = $mensagem->getCountLido('Nao_Lido',0);

		// Recupera a contagem das mensagem enviado pelo usuário logado.
		$this->view->qtdMensagemEnviado = $mensagem->getCountEnviado();
	}


	/* Evitar que seja enviado dados para o banco de dados vazio */
	public function getValidaDados() 
	{
		// Instancia um objeto da classe Usuario
		$usuario = Container::getModel('Usuario');

		/* Inicializa a variável com true, pois quando uma das condições abaixo forem verdadeira,
		*  automaticamente o valor do validador modifica para FALSE, sendo assim, function retorna FALSO
		*  e consequentemente dados não são enviados para o banco de dados, pois essa mesma function é 
		*  utilizada na condiçao de inserção
		*/
		$validator = true;

		/* Verifica se o valores das $_POST são vázias
		*/
		if ($_POST['nome'] == '') {
			$validator = false;
		} 

		if ($_POST['data_nascimento'] == '') {
			$validator = false;
		}

		if ($_POST['sexo'] == '') {
			$validator = false;
		}

		if ($_POST['telefone'] == '') {
			$validator = false;
		}

		// Retorna o valor da validador, podendo ser TRUE / FALSE.
		return $validator;
	}

	// Responsável por modificar as mensagens como lido
	public function marcar_lido() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		// Cria um objeto da classe Usuário
		$mensagem->getModel('Usuario');

		/* FALTA IMPLEMENTAÇÀO
		foreach ($_POST['checkbox'] as $checkbox) {
			$mensagem->__set('id_mensagem', $checkbox);
			$mensagem->setMensagemLido('1');
		}*/

		// Redireciona para a view mailbox.
		header('Location: /mailbox');
	}

	// Responsável por modificar as mensagens como não lido
	public function marcar_nao_lido() 
	{
		/* Responsável pela autenticação do usuário e a não permissão para entrar na URL direto sem o login.
		*/
		$this->autenticacao();

		// Cria um objeto da classe Usuário
		$mensagem->getModel('Usuario');

		/* FALTA IMPLEMENTAÇÀO
		foreach ($_POST['checkbox'] as $checkbox) {
			$mensagem->__set('id_mensagem', $checkbox);
			$mensagem->setMensagemLido('0');
		}*/

		// Redireciona para a view mailbox.
		header('Location: /mailbox');
	}

}
