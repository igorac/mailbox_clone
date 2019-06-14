<?php
	
	namespace MF\Model;
	use App\Connection;

	class Container{

		public static function getModel($model){

			//Retornar o model solicitado já istanciado, inclusive com a conexão já estabelecida.

			$class = "\\App\\Models\\".ucfirst($model);

			//Instância de conexão
			//o uso dos :: significa que está acessando um método estático da classe
			$conn = Connection::getDb();
			return new $class($conn);
		}
	}

?>