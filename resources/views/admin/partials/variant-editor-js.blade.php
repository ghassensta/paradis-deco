<script>
    (function () {
        document.querySelectorAll('.variant-editor').forEach(function (section) {
            var rows = section.querySelector('.js-variant-rows');
            var addBtn = section.querySelector('.js-variant-add');
            var tpl = section.querySelector('.js-variant-template');

            if (!rows || !addBtn || !tpl) {
                return;
            }

            // L'index sert uniquement à nommer les champs : il n'a pas besoin
            // d'être contigu, mais ne doit jamais être réutilisé.
            var nextIndex = rows.querySelectorAll('.variant-row').length;

            addBtn.addEventListener('click', function () {
                rows.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
                nextIndex++;
                rows.lastElementChild.querySelector('input[type="text"]').focus();
            });

            rows.addEventListener('click', function (event) {
                var button = event.target.closest('.js-variant-remove');

                if (button) {
                    button.closest('.variant-row').remove();
                }
            });
        });
    })();
</script>
