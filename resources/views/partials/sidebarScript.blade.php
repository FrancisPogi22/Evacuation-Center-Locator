<script>
    $(document).ready(() => {
        const sidebar = document.querySelector('.sidebar'),
            activeLink = window.location.href.split('?')[0],
            menuLink = $('.menu-link').filter(function() {
                return $(this).attr('href') == activeLink;
            });

        if (menuLink.length) {
            menuLink.addClass('active-link');
            const subMenu = menuLink.closest('.sub-menu');

            if (subMenu.length) {
                const subBtn = subMenu.prev('.sub-btn');
                subBtn.find('.dropdown').addClass('rotate');
                subMenu.addClass('active');
            }
        }

        $(window).resize(() => {
            if (!$('#btn-sidebar-mobile').is(':visible')) sidebar.classList.remove('active');
        });

        document.addEventListener('click', ({
            target
        }) => {
            const element = target;

            if (element.id == 'btn-sidebar-mobile' || element.id == 'btn-sidebar-close')
                sidebar.classList.toggle('active');
        });
    });
</script>
