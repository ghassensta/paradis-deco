<script>
    (function () {
        // Plusieurs éditeurs peuvent coexister (modale d'ajout + modale d'édition).
        document.querySelectorAll('.faq-editor').forEach(function (section) {
            var rows = section.querySelector('.js-faq-rows');
            var addBtn = section.querySelector('.js-faq-add');
            var tpl = section.querySelector('.js-faq-template');

            if (!rows || !addBtn || !tpl) {
                return;
            }

            // L'index sert uniquement à nommer les champs : Laravel réindexe
            // le tableau à la réception, il n'a pas besoin d'être contigu.
            var nextIndex = rows.querySelectorAll('.faq-row').length;

            addBtn.addEventListener('click', function () {
                rows.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
                nextIndex++;
                rows.lastElementChild.querySelector('input[type="text"]').focus();
            });

            rows.addEventListener('click', function (event) {
                var button = event.target.closest('.js-faq-remove');

                if (button) {
                    button.closest('.faq-row').remove();
                }
            });

            // Utilisé par la modale d'édition pour réinjecter les FAQ chargées en AJAX.
            section.faqEditor = {
                reset: function () { rows.innerHTML = ''; nextIndex = 0; },
                add: function (faq) {
                    rows.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
                    var row = rows.lastElementChild;
                    row.querySelector('input[type="hidden"]').value = faq.id || '';
                    row.querySelector('input[type="text"]').value = faq.question || '';
                    row.querySelector('textarea').value = faq.answer || '';
                    row.querySelector('input[type="number"]').value = faq.position != null ? faq.position : nextIndex;
                    row.querySelector('input[type="checkbox"]').checked = !!faq.is_active;
                    nextIndex++;
                }
            };
        });
    })();
</script>
