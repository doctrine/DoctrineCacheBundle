/**
 * Loads data dump via AJAX.
 */

(function (Sfjs) {
    'use strict';

    var dumpLinks = document.querySelectorAll('a.show-dump');

    for (var i = 0, total = dumpLinks.length; i < total; i++) {
        dumpLinks[i].addEventListener('click', function (e) {
            e.preventDefault();
            showDump(e.currentTarget);

            return false;
        });
    }

    function showDump(link) {
        var images = link.children;
        var target = link.getAttribute('data-target-id');

        Sfjs.toggle(target, images[0], images[1]).load(
            target,
            link.href,
            null,
            function (xhr, el) {
                el.innerHTML = 'An error occurred while loading the data dump';
                Sfjs.removeClass(el, 'loading');
            }
        );

        return false;
    }

})(Sfjs);
