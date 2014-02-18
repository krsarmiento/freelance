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
            $('#movieModalPlot').html(data.movie.plot);
            
            
            var myRating = parseFloat(data.movie.my_rating).toFixed(1);
            var imdbRating = parseFloat(data.movie.imdb_rating).toFixed(1);
            var metascore = parseFloat(data.movie.metascore).toFixed(1);
            
            if (data.movie.my_rating == 10)
                myRating = 10;
            
            $('#movieModalMyRating').html(myRating);
            $('#movieModalImdbRating').html(imdbRating);
            $('#movieModalMetacriticRating').html(metascore);
            
            $('#movieModalImdbUrl').attr('href', data.imdb_url +data.movie.code);
            $('#movieModalPoster').html("<img style='max-width: 264px;' src='load/poster/"+data.movie.id+"' />");
            $('#movieModal').modal('show');
        });
       
    });
    
    
    
    
});