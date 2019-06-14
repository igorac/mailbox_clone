<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model 
{
	
	private $id_usuario;
	private $nome;
	private $email;
	private $senha;
	private $confirmar_senha;
	private $sexo;
	private $data_nascimento;
	private $telefone;
	private $cpf;
	private $imagem;

	public function __get($attr)
	{
		return $this->$attr;
	}

	public function __set($attr, $value) 
	{
		return $this->$attr = $value;
	}

	// Cadastra os usuário no banco de dados
	public function cadastrar() 
	{
		$query = "INSERT INTO usuario (nome,email,sexo,senha,confirmar_senha,data_nascimento,telefone) VALUES (:nome,:email,:sexo,:senha,:confirmar_senha,:data_nascimento,:telefone)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':sexo', $this->__get('sexo'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->bindValue(':confirmar_senha', $this->__get('confirmar_senha'));
		$stmt->bindValue(':data_nascimento', $this->__get('data_nascimento'));
		$stmt->bindValue(':telefone', $this->__get('telefone'));

		$stmt->execute();

		return $this;
	}


	// Valida os dados vindo do formulário
	public function validarCadastro() 
	{

		$valido = true;

		if (strlen($this->__get('nome')) < 10 && $this->__get('nome') == '') {
			$valido = false;
		}

		if (strlen($this->__get('email')) < 10 && $this->__get('email') == '') {
			$valido = false;
		}

		if (strlen($this->__get('senha')) < 4 && $this->__get('senha') == '') {
			$valido = false;
		}

		if($this->__get('email') == strtoupper($this->__get('email'))){
			$valido = false;
		}

		if ($this->__get('senha') != $this->__get('confirmar_senha')) {
			$valido = false;
		}

		if ($this->__get('telefone') == '') {
			$valido = false;
		}

		return $valido;
	}

	// Recupera os emails dos usuários
	public function getEmails() 
	{
		$query = "SELECT nome, email FROM usuario WHERE email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Recupera o email e senha do banco de dados
	public function autenticarEmailSenha() 
	{
		$query = "SELECT id_usuario, nome, email FROM usuario WHERE email = :email AND senha = :senha";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();
		$usuario =  $stmt->fetch(\PDO::FETCH_ASSOC);

		if ($usuario['id_usuario'] != '' && $usuario['nome'] != '') {
			$this->__set('id_usuario', $usuario['id_usuario']);
			$this->__set('nome', $usuario['nome']);
		}

		return $this;
	}

	// Recupera as pessoas do banco de dados
	public function getPessoas() 
	{
		//$query = "SELECT id_usuario, nome, email FROM usuario WHERE nome LIKE %:nomePesquisado% AND id_usuario != :id_usuario";
		$query = "SELECT id_usuario, nome, email,imagem FROM usuario WHERE nome like :nomePesquisado AND id_usuario != :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->bindValue(':nomePesquisado',  '%'.$this->__get('nome').'%');
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Recupera as informações do usuário do banco de dados
	public function getInfo()
	{
		$query = "SELECT nome,senha,confirmar_senha,cpf,data_nascimento,telefone,sexo,imagem FROM usuario WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	// Modifica os dados do profile do usuário
	public function setDadosProfile() 
	{
		$query = "UPDATE usuario SET nome = :nome, cpf = :cpf, telefone = :telefone, sexo = :sexo, data_nascimento = :data_nascimento WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':cpf', $this->__get('cpf'));
		$stmt->bindValue(':telefone', $this->__get('telefone'));
		$stmt->bindValue(':sexo', $this->__get('sexo'));
		$stmt->bindValue(':data_nascimento', $this->__get('data_nascimento'));
		$stmt->execute();
	}


	// Altera a senha do usuário logado
	public function alterarSenha() 
	{
		$query = "UPDATE usuario SET senha = :nova_senha , confirmar_senha = :confirmar_nova_senha WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nova_senha', $this->__get('senha'));
		$stmt->bindValue(':confirmar_nova_senha', $this->__get('confirmar_senha'));
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();
	}

	// Recupera o id do usuário de acordo com o email
	public function getId_usuario() 
	{
		$query = "SELECT id_usuario, email FROM usuario WHERE email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	// Recupera todos os emails do banco de dados
	public function validarEmailExistente() 
	{
		$query = "SELECT email FROM usuario";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Modifica a imagem de perfil do usuário
	public function setImagemPerfil() 
	{
		$query = "UPDATE usuario SET imagem = :imagem WHERE id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':imagem', $this->__get('imagem'));
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();
	}

}
