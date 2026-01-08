/*
function cambiaOpzioni(selectId, radioGroupName) {
    var selectElement = document.getElementById(selectId);
    var radioButtons = document.querySelectorAll('[name="' + radioGroupName + '"]');
    var selectInitialValue = selectElement.value;

    // Aggiungi un event listener a tutti gli elementi radio
    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('click', function() {
            var selectedValue = radioButton.value;

            // Se il valore del radio è "standard" o "ratio", reimposta il valore del select al valore iniziale
            if (selectedValue === 'standard' || selectedValue === 'ratio') {
                selectElement.value = selectInitialValue;
                // Aggiungi gli alert appropriati
                if (selectedValue === 'standard') {
                    alert('Hai cliccato su "standard"');
                } else if (selectedValue === 'ratio') {
                    alert('Hai cliccato su "ratio"');
                }
            }
        });
    });
}

// Utilizzo della funzione: chiamata della funzione cambiaOpzioni
cambiaOpzioni("pa_formati", "wvs_radio_attribute_pa_personalizzazione__37672");
*/


/*
document.addEventListener("DOMContentLoaded", function() {
    // Chiamata alla funzione quando la pagina è completamente caricata
    esistezaValoreRatioAcf();

    // Aggiungi un listener per l'evento 'change' sull'elemento con id 'pa_formati'
    document.getElementById('pa_formati').addEventListener('change', function() {
        // Chiamata alla funzione quando si verifica l'evento 'change'
        esistezaValoreRatioAcf();
    });

    // Aggiungi un listener per l'evento 'change' sui radio button
    var radioButtons = document.querySelectorAll('[name="attribute_pa_personalizzazione"]');
    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('change', function() {
            // Chiamata alla funzione quando si verifica l'evento 'change' sui radio button
            esistezaValoreRatioAcf();
        });
    });
});

  function esistezaValoreRatioAcf() {
    var ratioElement = document.getElementById('ratioacf');

    // Verifica se l'elemento esiste prima di tentare di prelevare il suo valore
    if (ratioElement) {
        // Preleva il testo interno dell'elemento
        var ratioValue = ratioElement.innerText;
        
        // Controlla il valore e esegue azioni diverse
        switch (ratioValue) {
            case '2:3':
                // Esegui azioni specifiche per il valore 2:3
                doSomethingForRatio23(ratioValue);
                break;
            case '5:8':
                // Esegui azioni specifiche per il valore 5:8
                doSomethingForRatio58(ratioValue);
                break;
            default:
                // Esegui azioni predefinite per altri valori
                alert('Il valore non corrisponde a 2:3 o 5:8');
                break;
        }
       
    } else {
       // alert('L\'elemento con id "ratioacf" non è stato trovato.');
    }
}
*/




/*
// Funzione esterna per il caso 2:3
function doSomethingForRatio23(ratioValue) {
    // Intercetta il campo select con id "pa_formati"
    var selectElement = document.getElementById('pa_formati');
    var radioGroup = document.querySelector('[data-attribute_name="attribute_pa_personalizzazione"]');
    alert("Il valore di radioGroup è: " + radioGroup.getAttribute('data-attribute_name'));
  


    // Verifica se l'elemento esiste
    var pippo = 2; // Definizione della variabile "pippo"

    if (selectElement) {
        // Variabile per memorizzare il valore selezionato
        var valorePaFormatiSelezionato = selectElement.value;

        // Array per memorizzare tutti i valori
        var allValues = [];
        
        // Itera su tutte le opzioni
        for (var i = 0; i < selectElement.options.length; i++) {
            // Aggiungi il valore dell'opzione all'array
            allValues.push(selectElement.options[i].value);
        }

        // Condizioni per gestire l'alert in base al valore di "pippo"
        if (pippo === 1) {
            alert("Il valore di pippo è: " + pippo);
        } else if (pippo === 2) {
            alert("Il valore di pippo è: " + pippo);
        }
  
        // Mostra i valori nel campo select "pa_formati" tramite console.log()
        
        console.log('Tutti i valori nel campo select "pa_formati": ' + allValues.join(', '));
        console.log('Il valore selezionato in questo momento è: ' + valorePaFormatiSelezionato);
        
    } else {
        console.log('Campo select con id "pa_formati" non trovato.');
    }


}
*/


/*
// Funzione esterna per il caso 2:3
function doSomethingForRatio23(ratioValue) {
    // Intercetta il campo select con id "pa_formati"
    var selectElement = document.getElementById('pa_formati');

    var radioGroup = document.querySelector('[name="attribute_pa_personalizzazione"]');

    // Verificare se è stato selezionato un radio button e ottenere il suo valore
    if (radioGroup) {
        var selectedRadio = radioGroup.querySelector(':checked');
        if (selectedRadio) {
            var valoreRadioSelezionato = selectedRadio.value;
            alert("Il valore del radio button selezionato è: " + valoreRadioSelezionato);
        } else {
            alert("Nessun radio button selezionato.");
        }
    } else {
        console.log('Radio group non trovato.');
    }

    if (selectElement) {
        // Variabile per memorizzare il valore selezionato
        var valorePaFormatiSelezionato = selectElement.value;

        // Array per memorizzare tutti i valori
        var allValues = [];

        // Itera su tutte le opzioni
        for (var i = 0; i < selectElement.options.length; i++) {
            // Aggiungi il valore dell'opzione all'array
            allValues.push(selectElement.options[i].value);
        }

        // Mostra i valori nel campo select "pa_formati" tramite console.log()
        console.log('Tutti i valori nel campo select "pa_formati": ' + allValues.join(', '));
        console.log('Il valore selezionato in questo momento è: ' + valorePaFormatiSelezionato);

    } else {
        console.log('Campo select con id "pa_formati" non trovato.');
    }
}





// Funzione esterna per il caso 5:8
function doSomethingForRatio58(ratioValue) {
    // Esegui qui le azioni complesse per il caso 5:8
    alert('Esecuzione di azioni complesse per il caso 5:8. Il valore è: ' + ratioValue);
}
*/