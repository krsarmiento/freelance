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
       
       
       $.getJSON( 'ajax/movie/data/' + id, function( data ) {
            $('#movieModalTitle').html(data.movie.title);
            $('#movieModalRating').html(data.movie.my_rating);
            $('#movieModalImdbUrl').attr('href', data.imdb_url +data.movie.code);
            $('#movieModalPoster').html("<img src='load/poster/"+data.movie.id+"' />");
            $('#movieModal').modal('show');
        });
       
    });
    
    
    
    
});