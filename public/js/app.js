$(document).ready(()=>{
	
	// Mascara do campo de telefone
	$('[name=telefone]').mask('(00) 00000-0000');
	$('#id_cpf_cnpj').mask('000.000.000-00');

	$('#form_dados').validate({
		rules: {
			nome: {
				required: true,
				maxlength: 100,
				minlength: 10,
				minWords: 2,
			},
			email: {
				required: true,
				minlength: 10
			},
			senha: {
				required: true,
				minlength: 4

			},
			senha_confirm: {
				required: true,
				equalTo: "#id_senha",
				minlength: 4
			},
			data_nascimento: {
				required: true
			},
			senha_atual: {
				required: true
			}

		},
		messages:{
			senha_confirm: {
				equalTo: "As senhas diferem uma da outra"
			}
		}

	});


	//Convertendo o texto para caixa baixa
	$('.caixa-baixa').keyup(()=>{
		let texto = $('.caixa-baixa').val();
		$('.caixa-baixa').val(texto.toLowerCase());
	});


	$('#btn_editar_campo').click(()=>{
		$('.disabled').attr('disabled',false);
		$('#btn_alterar_profile').attr('disabled',false);
		$('#btn_editar_campo').attr('disabled',true);
	});


	$("#id_imagem_alterar").on('change', function(){
	    const file = $(this)[0].files[0];
	    const fileReader = new FileReader();
	    fileReader.onloadend = function() {
	    	$('.img-alterado').attr('src', fileReader.result);
	    }
	    fileReader.readAsDataURL(file);
	});


	$('#btn_deletar_mensagem').on('click', (e)=>{

		Swal.fire({
          title: 'Você tem certeza?',
          text: "Você não pode reverter isso!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sim, exclua!',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.value) {

			var formData = new FormData();

			var checados = [];
			
			$.each($("input[name='checkbox[]']:checked"),function(){
				checados.push($(this).val());
			});
			
			formData.append('checkbox', checados);

			$.ajax({
				url: '/deletar_mensagem',
				dataType: 'html',
				type: 'post',
				processData: false,
				contentType: false,
				data: formData,
				success: (response)=>{
					console.log(response);
					window.location = "/mailbox";
				},
				error: (error)=>{
					console.log(error.responseText);
				}

			})

			Swal.fire(
              'Deletado!',
              'Seu arquivo foi excluído.',
              'success'
            )
          }

        });
	});

	
	
	



});//Fim do document.ready