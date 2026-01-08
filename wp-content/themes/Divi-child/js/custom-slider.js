// il codice cattura tutte le immagini dell'elemento HTML con classe "slider-item" e inizializza un indice per tener traccia dell'immagine attualmente visualizzata. Due pulsanti, "nextSlide" e "prevSlide", consentono di scorrere avanti e indietro tra le immagini. */

document.addEventListener("DOMContentLoaded", function () {
  // Attendi che il documento HTML sia completamente caricato prima di eseguire il codice.

  const slides = document.querySelectorAll(".slider-item");
  // Seleziona tutti gli elementi con classe "slider-item" e li mette in una NodeList.

  let currentSlide = 0;
  // Inizializza una variabile per tenere traccia dell'indice dell'immagine corrente.

  function showSlide(index) {
    // Definisce una funzione chiamata "showSlide" che prende un indice come argomento.
    slides.forEach((slide, i) => {
      // Itera su tutti gli elementi della NodeList "slides".
      slide.style.display = i === index ? "block" : "none";
      // Imposta lo stile "display" degli elementi in modo che solo l'elemento con l'indice corrente sia visibile (display: block) e tutti gli altri siano nascosti (display: none).
    });
  }

  document.getElementById("nextSlide").addEventListener("click", () => {
    // Seleziona l'elemento con l'ID "nextSlide" e aggiunge un listener di evento per il clic.
    currentSlide = (currentSlide + 1) % slides.length;
    // Aumenta l'indice dell'immagine corrente e lo fa tornare al primo se si supera il numero di immagini disponibili.
    showSlide(currentSlide);
    // Chiama la funzione "showSlide" per mostrare l'immagine corrente.
  });

  document.getElementById("prevSlide").addEventListener("click", () => {
    // Seleziona l'elemento con l'ID "prevSlide" e aggiunge un listener di evento per il clic.
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    // Decrementa l'indice dell'immagine corrente e lo fa tornare all'ultimo se si va al di sotto del primo.
    showSlide(currentSlide);
    // Chiama la funzione "showSlide" per mostrare l'immagine corrente.
  });
});

