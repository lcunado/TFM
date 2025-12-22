function googleTranslateElementInit() {
  new google.translate.TranslateElement(
    {
      pageLanguage: 'es', // Idioma original de la página
      includedLanguages: 'es,en,fr' // Idiomas permitidos en el selector
    },
    'google_translate_element' // ID del contenedor
  );
}