$('#add-images').click(function(){

    // je récupére le numéro du champ que je veux insérer
    const index = +$('#widgets-counter').val();

    console.log(index);

    // je récupére le prototype du champ que je veux insérer
    const tmpl=$('#ad_images').data('prototype').replace(/__name__/g, index);
    
    // j'injecte le code contenu dans le tmpl
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index + 1)

    // Je gére le bouton supprimer

    handleDeleteButtons();
});

function handleDeleteButtons(){

    // Je récupére tous les boutons qui ont un attribut 'ata-action="delete"'
    
    $('button[data-action="delete"]').click(function(){

        //this représenete l'élément qui déclenche l'action ici c'est 'button', et dataset veut dire tous
        //les élement qui ont "data" suivi de quelques choses exple data-target ou data-action

        const target = this.dataset.target;
    
    //    console.log(target);

        $(target).remove();
    });
}

function updateCounter(){

    const count = +$('#ad_images div.form-group').length;

    $('#widgets-counter').val(count + 1);
}
updateCounter();
handleDeleteButtons();