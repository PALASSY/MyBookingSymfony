

// Instruction avec JS pour récupérer/modifier/re-injecter la data-prototype  c'est la suite de factorisation dans src/templates/ad/_collections.html.twig

// Quand on click sur le button Ajouter une image
		$('#addcollection-image').click(function () { 
			//Récupérer la value du champ <input> type=hidden
			const index = +$('#widget-count').val();
	
			// Récupérer la data-prototype pour modifier l'ID de ses input avec Regex
			const tmpl = $('#annonce_yes').data('prototype').replace(/__name__/g, index);
	
			//Ré-injecter les champs modifiés
			$('#annonce_yes').append(tmpl);
	
			//Pour que l'ID soit incrémenter à chaque ajout des champs de collection, on indique que la value du <input> type=hidden sera incrémenter de 1
      $('#widget-count').val(index + 1);
      
			//Mais pour éviter les bugs de ces index en cas des modifications ou suppression des champs de collection, on va créer des une function pour le comptage d'Index par rapport aux nombres de champs crées<div>
	
			//Ici On rajoute également le <button> de suppression des champs de collection
			deleteButtons()
	
		});
	
		//Comptage de nombre de champs de collection crées
		function updateCounter(){
			//Récupération de tous les <div> mode append en nombre à ne pas oublier le +
			const count = +$('#annonce_yes div.form-group').length;
			//MAJ la value de <input> type=hidden
			$('#widget-count').val(count);
		}
				
	
		//Suppression des champs <div> append  en ciblant l'action (qui est particulié). On utilisera cette function à chaque fois qu'on charge la page et à chaque création des champs
		function deleteButtons(){
			$('button[data-action = "delete"]').click(function(){
				//Récupérer tous les data-target
				const target = this.dataset.target;
				//On supprime 
				$(target).remove();
			});
		}
	
		//Appeler la function(initialisation des nombre de <div> append)
    updateCounter();
    
		//Appeller la function(initialisation pour la suppression de <div> append) 
		deleteButtons()
	
	
	
	