// Nascondi la variante 100x150-cm-100-gr  quando si carica la pagina del prodotto

document.addEventListener('DOMContentLoaded', function() {
    // Controlla se siamo sulla pagina del prodotto
    if (document.body.classList.contains('single-product')) {
        // Sostituisci '100x150-cm-100-gr' con lo slug della variante che desideri nascondere
        var variantSlugToHide = '100x150-cm-100-gr';
        
        // Trova tutte le varianti disponibili
        var variants = document.querySelectorAll('.variations select option');
        
        // Itera su tutte le varianti per trovare quella da nascondere
        variants.forEach(function(variant) {
            if (variant.value === variantSlugToHide) {
                // Nascondi la variante
                variant.style.display = 'none';
            }
        });
    }

});


