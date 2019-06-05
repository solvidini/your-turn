$('.dropdown-menu').on('click', function(event){
    event.stopPropagation();
    event.preventDefault();
});
$('.stop-propagation').on('click', function(event){
    event.stopPropagation();
    event.preventDefault();
});