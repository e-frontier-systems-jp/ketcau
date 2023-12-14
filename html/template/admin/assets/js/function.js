
var mainNav = function() {
    $(function() {
        $('')
    })
};

var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
});


var cardCollapseIconDown = function () {
    $(function () {
        $('.ec-cardCollapse').on('hidden.bs.collapse', function () {
            var id = $(this).attr('id');
            var icon = $('[href="#' + id + '"]').find('i');
            icon.removeClass('fa-angle-up');
            icon.addClass('fa-angle-down')
        });
    });
};
cardCollapseIconDown();

var cardCollapseIconUp = function () {
    $(function () {
        $('.ec-cardCollapse').on('shown.bs.collapse', function () {
           var id = $(this).attr('id');
           var icon = $('[href="' + id + '"]').find('i');
           icon.addClass('fa-angle-up');
        });
    });
};
cardCollapseIconUp();

if (typeof Ladda !== 'undefined') {
    Ladda.bind('button[type=submit]', { timeout: 2000 });
    $('button[type=submit].btn-kc-regular').attr('data-spinner-color', '#595959');
}
