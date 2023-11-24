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

            if (subMenu.length)
                subMenu.addClass('active').prev('.sub-btn').find('.dropdown').addClass('rotate');
        }

        $(window).resize(() => {
            if (!$('#btn-sidebar-mobile').is(':visible')) sidebar.classList.remove('active');
        });

        document.addEventListener('click', ({
            target
        }) => {
            if (target.id == 'btn-sidebar-mobile' || target.id == 'btn-sidebar-close')
                sidebar.classList.toggle('active');
        });
    });
</script>
