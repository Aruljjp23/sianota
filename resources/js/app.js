document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-confirm]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            const message = button.getAttribute('data-confirm');

            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-table-search]').forEach(function (input) {

        const tableId = input.getAttribute('data-table-search');
        const table = document.getElementById(tableId);

        if (!table) {
            return;
        }

        input.addEventListener('input', function () {

            const keyword = input.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {

                const text = row.innerText.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }

            });

        });

    });

    document.querySelectorAll('[data-date-now]').forEach(function (element) {

        const today = new Date();

        element.textContent = today.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

    });

});