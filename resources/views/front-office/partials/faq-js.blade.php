<script>
    (function () {
        document.querySelectorAll('.js-faq-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var panel = document.getElementById(button.getAttribute('aria-controls'));
                var expanded = button.getAttribute('aria-expanded') === 'true';

                button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                panel.hidden = expanded;
                button.querySelector('.js-faq-icon').style.transform = expanded ? '' : 'rotate(180deg)';
            });
        });
    })();
</script>
