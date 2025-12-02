
// ===============================
// CKEDITOR 5 PARA DESCRIPCIÓN EN PRODUCTOS
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    const descriptionField = document.querySelector('#description');
    if (descriptionField) {
        ClassicEditor
            .create(descriptionField, {
                language: 'es',
                toolbar: {
                    items: [
                        'heading', '|',
                        'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'alignment', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', 'mediaEmbed', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => {
                window.productEditor = editor;
                console.log('CKEditor 5 cargado en descripción de producto.');
            })
            .catch(error => {
                console.error('Error al iniciar CKEditor 5 en productos:', error);
            });
    }
});
