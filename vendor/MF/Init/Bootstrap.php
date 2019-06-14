<?php

	namespace MF\Init;

	abstract class Bootstrap {

		private $routes;

		//Quando define um método abstract em uma classe abstract, ao ser herdado, ele deve ser declarado obrigatóriamente.
		abstract protected function initRoutes();

		public function __construct(){
			//inicia as rotas
			$this->initRoutes();

			//Responsável pela a inicialização do controller e das action das rotas dinâmicamente
			$this->run($this->getUrl());
		}

		public function setRoutes(array $routes){
			$this->routes = $routes;
		}

		public function getRoutes(){
			return $this->routes;
		}


		public function run($url){
			//echo "*********************".$url."************************";
			//$this->getRoutes() - Retorna os arrays de routes que foi setado no Route.php
			foreach ($this->getRoutes() as $path => $route) {
				//echo "<pre>";
				//print_r($route);
				//echo "</pre>";


				//$url -> Seria o getUrl() que está instanciado no construct
				if($url == $route['route']){
					//ucfirst = Faz com que o primeiro caracter fique maiúsculo.
					$class = "App\\Controllers\\".ucfirst($route['controller']);


					//Instancia um controller dinâmicamente atráves de sua namespace
					$controller = new $class;
					$action = $route['action'];

					$controller->$action();
				}
			}
		}


		public function getUrl(){
			//Essa super global é um array que retorna todos os detalhes do servidor.
			return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


			//Exemplo
			//return parse_url('www.google.com.br/gmail?x=10');
		}


	}
?>