<?php
	namespace MF\Controller;



	abstract class Action {

		//O motivo da visibilidade protected - é para que ele possa ser herdado.
		protected $view;

		public function __construct(){
			//stdClass - é uma classe padrão e vazia do PHP
			$this->view = new \stdClass();
		}

		protected function render($view, $layout){
			$this->view->page = $view;

			if(file_exists("../App/Views/".$layout.".phtml")){
				require_once "../App/Views/".$layout.".phtml";
			}else{
				$this->content();
			}
			
		}

		protected function content(){
			$classAtual = get_class($this);

			//Primeiro parâmetro - O texto que será substituído
			//Segundo parâmetro - O texto que irá substituir
			//Terceiro parâmetro - De que atribuito é o texto
			$classAtual =  str_replace('App\\Controllers\\', '', $classAtual);

			$classAtual = strtolower(str_replace('Controller', '' , $classAtual));

			//echo $classAtual;

			require_once "../App/Views/".$classAtual."/".$this->view->page.".phtml";
		}


	}
?>