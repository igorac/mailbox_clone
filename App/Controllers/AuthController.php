<?php
	
namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Models\Usuario;

class AuthController extends Action {

	public function autenticar() {
		$usuario = Container::getModel('usuario');
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));
		$usuario->autenticarEmailSenha();


		if ($usuario->__get('id_usuario')!= '' && $usuario->__get('nome') != '' && $usuario->__get('email') != ''){
			session_start();

			//Cria uma sessão com o nome id_usuario
			$_SESSION['id_usuario'] = $usuario->__get('id_usuario');
			$_SESSION['nome'] = $usuario->__get('nome');
			$_SESSION['email'] = $usuario->__get('email');


			//Não se usa o render nesse caso, pois não tem uma view na pasta auth da pasta raiz Views.
			//$this->render('mailbox', 'layout');
			header('Location: /mailbox');
		} else {
			header('Location: /?login=erro');
		}
	}

	public function sair() {
		session_start();
		session_destroy();
		header('Location: /');
	}

}