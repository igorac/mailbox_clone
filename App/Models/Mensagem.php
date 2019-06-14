<?php

namespace App\Models;

use MF\Model\Model;

class Mensagem extends Model 
{

	private $id_mensagem;
	private $titulo;
	private $id_usuario;
	private $descricao;
	private $email_destinatario;
	private $email_origem;
	private $lido;
	private $enviado;
	private $data;

	public function __get($attr) 
	{
		return $this->$attr;
	}

	public function __set($attr, $value) 
	{
		$this->$attr = $value;
	}

	// Recupera as mensagens do banco de dados
	public function getMensagens()
	{
		$query = "SELECT id_mensagem,email_origem, titulo, descricao, DATE_FORMAT(data,'%d/%m/%Y %H:%i') as data  FROM mensagem WHERE id_usuario_destinatario = :id_usuario ORDER BY data DESC";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Recupera as mensagens do banco de dados e aplica o limit
	public function getMensagensLimit($inicio,$limite)
	{
		$query = "SELECT id_mensagem,email_origem, titulo, descricao, DATE_FORMAT(data,'%d/%m/%Y %H:%i') as data  FROM mensagem WHERE id_usuario_destinatario = :id_usuario ORDER BY data DESC LIMIT ".$inicio.",".$limite." ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Envia as mensagens
	public function sendMensagem() 
	{
		$query = "INSERT INTO mensagem (id_usuario_destinatario,email_destinatario,titulo, descricao, lido, enviado, data, email_origem) VALUES (:id_usuario,:email_destinatario, :titulo, :descricao, :lido, :enviado, :data, :email_origem)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->bindValue(':email_destinatario', $this->__get('email_destinatario'));
		$stmt->bindValue(':titulo', $this->__get('titulo'));
		$stmt->bindValue(':descricao', $this->__get('descricao'));
		$stmt->bindValue(':lido', $this->__get('lido'));
		$stmt->bindValue(':enviado', $this->__get('enviado'));
		$stmt->bindValue(':data', $this->__get('data'));
		$stmt->bindValue(':email_origem', $this->__get('email_origem'));
		$stmt->execute();
	}

	// Deleta as mensagens
	public function deletarMensagem() 
	{
		$query = "DELETE FROM mensagem WHERE id_mensagem = :id_mensagem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_mensagem', $this->__get('id_mensagem'));
		$stmt->execute();
	}

	// Contabiliza a quantidade de mensagem lido / Nao_lido
	public function getCountLido($tipo,$lido) 
	{
		$query = "SELECT COUNT(lido) as ".$tipo." FROM mensagem WHERE id_usuario_destinatario = :id_usuario_destinatario AND lido = :lido";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario_destinatario', $this->__get('id_usuario'));
		$stmt->bindValue(':lido', $lido);
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}


	// Contabiliza a quantidade de mensagens enviadas
	public function getCountEnviado() 
	{
		$query = "SELECT COUNT(enviado) as Enviado FROM mensagem WHERE email_origem = :email_origem AND enviado = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email_origem', $this->__get('email_origem'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}


	// Contabiliza a quantidade de  todas as mensagens
	public function getCountMensagens() 
	{
		$query = "SELECT COUNT(*) as Qtd_mensagem FROM mensagem WHERE id_usuario_destinatario = :id_usuario_destinatario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario_destinatario', $this->__get('id_usuario'));
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	// Modifica a mensagem para lido / nao_lido
	public function setMensagemLido($lido) 
	{
		$query = "UPDATE usuario SET lido = ".$lido." WHERE id_mensagem = :id_mensagem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_mensagem', $_this->__get('id_mensagem'));
		$stmt->execute();
	}

}
