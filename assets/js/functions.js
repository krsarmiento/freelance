$(document).ready(function() {
    
    $('.loadMovies').on('click', function() {
       var button = $(this);
       var id = this.id.split(',');
       var rating = id[0];
       var times = id[1];
       
       $.ajax({
           url: 'ajax/movies/load/' + rating + '/' + times,
       }).done(function(movies) {
           if (movies) {
                button.before(movies);
                button.attr('id', rating+','+(parseInt(times)+1));
           } else {
               button.attr('disabled', true);
           }
       });
       
    });
    
    $('.displayMovie').on('click', function() {
       var id = this.id;
       
       $.ajax({
           url: 'ajax/movie/display/' + id
       }).done(function (modal) {
           alert(modal);
           $('body').append(modal);
           $('#movieModal').modal();
       });
    });
    
    
});