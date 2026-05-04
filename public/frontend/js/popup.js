$('.popup-with-form').magnificPopup({
    type: 'inline',
    preloader: false,
    focus: '#name',

    // When elemened is focused, some mobile browsers in some cases zoom in
    // It looks not nice, so we disable it:
    callbacks: {
        beforeOpen: function() {
            console.log(123);
        }
    }
});
function quickview(id){
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: "/quickview",
        method: "POST",
        data: {
            'id': id
        },
        success: function (response) {
            console.log(response.html);
          $(".viewquick").html(response.html);
        },
    });
}
function quickviewDocs(id){
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: "/quickviewdocs",
        method: "POST",
        data: {
            'id': id
        },
        success: function (response) {
            console.log(response.html);
          $(".viewquick").html(response.html);
        },
    });
}