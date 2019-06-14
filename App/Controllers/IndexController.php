<?php
	
	namespace App\Controllers;

	//Os recursos do miniframework
	use MF\Controller\Action;
	use MF\Model\Container;
	use App\Models\Usuario;


	class IndexController extends Action {

		//Responsável por renderizar a view de index/Raiz
		public function index() {
			$this->render('index','layout');
		}

		//Responsável por renderizar a view de inscreverse
		public function inscreverse() {
			$this->view->erroCadastro = false;
 
			$this->view->usuario = array(
				'nome' => '',
				'email' => '',
				'data' =>'',
				'telefone' => ''
				);

			$this->render('inscreverse','layout');
		}

		//Responsável por renderizar a view de cadastro
		public function cadastradoSucesso() {
			$this->render('cadastradoSucesso','layout');
		}

		public function cadastrar() {

			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $_POST['nome']);
			$usuario->__set('email', $_POST['email']);
			$usuario->__set('sexo', $_POST['sexo']);
			$usuario->__set('senha', md5($_POST['senha']));
			$usuario->__set('confirmar_senha', md5($_POST['senha_confirm']));
			$usuario->__set('data_nascimento', $_POST['data_nascimento']);
			$usuario->__set('telefone', $_POST['telefone']);

			if ($usuario->validarCadastro() && count($usuario->getEmails()) == 0) {
				$usuario->cadastrar();
				header('Location: /cadastradoSucesso');
			} else {

				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'data_nascimento' => $_POST['data_nascimento'],
					'telefone' => $_POST['telefone']
				);

				$this->view->erroCadastro = true;
				$this->render('inscreverse','layout');
				
			}
		}
	}
?>