<?php
	
	namespace App;

	use MF\Init\BootStrap;

	class Route extends Bootstrap{

		protected function initRoutes(){
			$routes['home'] = array('route' => '/',	'controller' => 'indexController','action' => 'index');

			$routes['inscreverse'] = array('route' => '/inscreverse', 'controller' => 'indexController', 'action' => 'inscreverse');
			$routes['cadastrar'] = array('route' => '/cadastrar', 'controller' => 'indexController', 'action' => 'cadastrar');
			$routes['cadastradoSucesso'] = array('route' => '/cadastradoSucesso', 'controller' => 'indexController', 'action' => 'cadastradoSucesso');

			$routes['autenticar'] = array('route' => '/autenticar', 'controller' => 'AuthController', 'action' => 'autenticar');
			$routes['sair'] = array('route' => '/sair', 'controller' => 'AuthController', 'action' => 'sair' );



			$routes['mailbox_view'] = array('route' => '/mailbox', 'controller' => 'AppController', 'action' => 'mailbox_view');
			$routes['mailbox_message_view'] = array('route' => '/mailbox-message', 'controller' => 'AppController', 'action' => 'mailbox_message_view');
			$routes['enviar_mensagem'] = array('route' => '/enviar_mensagem', 'controller' => 'AppController', 'action' => 'enviar_mensagem');
			$routes['deletar_mensagem'] = array('route' => '/deletar_mensagem', 'controller' => 'AppController', 'action' => 'deletar_mensagem');
			

			//$routes['marcar_lido'] = array('route' => '/marcar_lido', 'controller' => 'AppController', 'action' => 'marcar_lido');
			//$routes['marcar_nao_lido'] = array('route' => '/marcar_nao_lido', 'controller' => 'AppController', 'action' => 'marcar_nao_lido');


			$routes['search_view'] = array('route' => '/search', 'controller' => 'AppController', 'action' => 'search');

			$routes['profile_view'] = array('route' => '/profile', 'controller' => 'AppController', 'action' => 'profile_view');
			$routes['alterar_profile'] = array('route' => '/alterar_profile', 'controller' => 'AppController', 'action' => 'alterar_profile');


			$routes['alterar_senha_view'] = array('route' => '/alterar_senha_view', 'controller' => 'AppController', 'action' => 'alterar_senha_view');
			$routes['alterar_senha'] = array('route' => '/alterar_senha', 'controller' => 'AppController', 'action' => 'alterar_senha');



			$routes['alterar_imagem_view'] = array('route' => '/alterar_imagem_view', 'controller' => 'AppController', 'action' => 'alterar_imagem_view');

			$routes['alterar_imagem'] = array('route' => '/alterar_imagem', 'controller' => 'AppController', 'action' => 'alterar_imagem');
	
		



			$this->setRoutes($routes);
		}



	}

?>