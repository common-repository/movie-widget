<?php

/**
 * Adds Movie_Poster_Display widget.
 */
class Movie_Poster_Display_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {

    parent::__construct(
      'movieposterdisplay_widget', // Base ID
      esc_html__( 'Movie Widget', 'mpd_domain' ), // Name
      
      array( 'description' => esc_html__( 'Displays Movie/TV posters, overviews and trailers.', 'mpd_domain' ), ) // Args
    );

  }

  
  /**
  * Front-end display of widget.
  *
  * @see WP_Widget::widget()
  *
  * @param array $args     Widget arguments.
  * @param array $instance Saved values from database.
  */
  public function widget( $args, $instance) {

    echo $args['before_widget']; // whatever you want to display before widget (<div>, etc)
        
    /* widget content output
    *
    */
    $first_movie_result = $this->movie_search($instance); // stores movie data from TMDB query

    echo $this->display_poster($instance, $first_movie_result);
    
    if (($instance['movie_show_poster']) && ($instance['movie_show_overview'])){
      echo "<br>";
    } // line break so that overview is on separate line from poster if both are displayed
    
    echo $this->display_overview($instance, $first_movie_result);

    // Movie trailer display youtube
    if ($instance['movie_show_trailer']){
      $trailer_key ="https://www.youtube.com/embed/" .$this->display_trailer($instance, $first_movie_result)."?enablejsapi=1&rel=0";
      //https://www.youtube.com/embed/$trailer_key?enablejsapi=1
      ?>
        <div class="youtubeplayer">       
          <iframe 
          id="player" type="text/html"
          src="<?php echo $trailer_key;?>"
          frameborder="0" allowfullscreen="allowfullscreen">
          </iframe>
        </div>
      <?php
    } 
    /* widget content output end 
    *
    */

    echo $args['after_widget']; // whatever you want to display after widget (<div>, etc)

  }

  /**
 * Back-end widget form.
 *
 * @see WP_Widget::form()
 *
 * @param array $instance Previously saved values from database.
 */

  public function form( $instance ) {
    $tmdbapikey = ! empty( $instance['tmdbapikey'] ) ? $instance['tmdbapikey'] : esc_html__( '', 'mpd_domain' );

    $movie_title = ! empty( $instance['movie_title'] ) ? $instance['movie_title'] : esc_html__( '', 'mpd_domain' );

    $movie_type = ! empty( $instance['movie_type'] ) ? $instance['movie_type'] : esc_html__( 'movie', 'mpd_domain' );

    $movie_show_poster = isset($instance['movie_show_poster']) ? (bool) $instance['movie_show_poster'] :false;

    $size = ! empty( $instance['size'] ) ? $instance['size'] : esc_html__( 'w185', 'mpd_domain' );

    $movie_click_redirect = isset($instance['movie_click_redirect']) ? (bool) $instance['movie_click_redirect'] :false;

    $link_address = ! empty( $instance['link_address'] ) ? $instance['link_address'] : esc_html__( '', 'mpd_domain' );

    $movie_show_overview = isset($instance['movie_show_overview']) ? (bool) $instance['movie_show_overview'] :false;

    $movie_show_trailer = isset($instance['movie_show_trailer']) ? (bool) $instance['movie_show_trailer'] :false;
?>

  <!-- tmdbapikey -->
  <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'tmdbapikey' ) ); ?>">
      <?php esc_attr_e( 'TMDB API key:', 'mpd_domain' ); ?>
    </label> 

    <input
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'tmdbapikey' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'tmdbapikey' ) ); ?>" 
      type="text" 
      value="<?php echo esc_attr( $tmdbapikey ); ?>">
    </input> 
  </p>

  <!-- HTML link -->
  <p>
    <a href="https://www.themoviedb.org/faq/api">What is a TMDB API key?</a>
  </p>

  <!-- movie_title -->
  <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_title' ) ); ?>">
      <?php esc_attr_e( 'Title:', 'mpd_domain' ); ?>
    </label> 

    <input
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_title' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_title' ) ); ?>" 
      type="text" 
      value="<?php echo esc_attr( $movie_title ); ?>">
    </input> 
  </p>

  <!-- movie_type -->
  <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_type' ) ); ?>">
      <?php esc_attr_e( 'Media Type:', 'mpd_domain' ); ?>
    </label> 

    <select
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_type' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_type' ) ); ?>" >
      <option value="movie" <?php echo ($movie_type == 'movie') ? 'selected':''; ?>>
        Movie
      </option>
      <option value="tv" <?php echo ($movie_type == 'tv') ? 'selected':''; ?>>
        TV Show
      </option>
    </select>
  </p>

  <!--Movie Poster Display -->
  <p>
    <input
      class="checkbox" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_show_poster' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_show_poster' ) ); ?>" 
      type="checkbox" 
      <?php checked( $movie_show_poster ); ?> />
    </input> 

    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_show_poster' ) ); ?>">
      <?php esc_attr_e( 'Show Poster', 'mpd_domain' ); ?>
    </label> 
  </p>

  <!-- size -->
  <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>">
        <?php esc_attr_e( 'Poster Size:', 'mpd_domain' ); ?>
    </label> 

    <select
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" >
      <option value="w45" <?php echo ($size == 'w45') ? 'selected':''; ?>>
          Pixel Width 45
      </option>
      <option value="w92" <?php echo ($size == 'w92') ? 'selected':''; ?>>
          Pixel Width 92
      </option>
      <option value="w154" <?php echo ($size == 'w154') ? 'selected':''; ?>>
          Pixel Width 154
      </option>
      <option value="w185" <?php echo ($size == 'w185') ? 'selected':''; ?>>
          Pixel Width 185 (Recommended Size)
      </option>
      <option value="w342" <?php echo ($size == 'w342') ? 'selected':''; ?>>
          Pixel Width 342
      </option>
      <option value="w500" <?php echo ($size == 'w500') ? 'selected':''; ?>>
          Pixel Width 500
      </option>
      <option value="w780" <?php echo ($size == 'w780') ? 'selected':''; ?>>
          Pixel Width 780
      </option>
      <option value="original" <?php echo ($size == 'original') ? 'selected':''; ?>>
          Original
      </option>
    </select>
  </p>
  
 <!-- Movie Overview Display -->
 <p>
    <input
      class="checkbox" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_click_redirect' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_click_redirect' ) ); ?>" 
      type="checkbox" 
      <?php checked( $movie_click_redirect ); ?> />
    </input> 

    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_click_redirect' ) ); ?>">
      <?php esc_attr_e( 'Clickable Poster Link', 'mpd_domain' ); ?>
    </label> 
  </p>

   <!-- link_address -->
  <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'link_address' ) ); ?>">
      <?php esc_attr_e( 'Link URL:', 'mpd_domain' ); ?>
    </label> 

    <input
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'link_address' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'link_address' ) ); ?>" 
      type="text" 
      value="<?php echo esc_attr( $link_address ); ?>">
    </input> 
  </p>

  <!-- Movie Overview Display -->
  <p>
    <input
      class="checkbox" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_show_overview' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_show_overview' ) ); ?>" 
      type="checkbox" 
      <?php checked( $movie_show_overview ); ?> />
    </input> 

    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_show_overview' ) ); ?>">
      <?php esc_attr_e( 'Show Overview', 'mpd_domain' ); ?>
    </label> 
  </p>

  <!-- Movie Trailer Display -->
  <p>
    <input
      class="checkbox" 
      id="<?php echo esc_attr( $this->get_field_id( 'movie_show_trailer' ) ); ?>"
      name="<?php echo esc_attr( $this->get_field_name( 'movie_show_trailer' ) ); ?>" 
      type="checkbox" 
      <?php checked( $movie_show_trailer ); ?> />
    </input> 

    <label for="<?php echo esc_attr( $this->get_field_id( 'movie_show_trailer' ) ); ?>">
      <?php esc_attr_e( 'Show Trailer', 'mpd_domain' ); ?>
    </label> 
  </p>

<?php 
	}
  
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
    $instance = array();
    
    $instance['tmdbapikey'] = ( ! empty( $new_instance['tmdbapikey'] ) ) ? sanitize_text_field( $new_instance['tmdbapikey'] ) : '';
    
    $instance['movie_title'] = ( ! empty( $new_instance['movie_title'] ) ) ? sanitize_text_field( $new_instance['movie_title'] ) : '';

    $instance['movie_type'] = ( ! empty( $new_instance['movie_type'] ) ) ? sanitize_text_field( $new_instance['movie_type'] ) : '';

    $instance['movie_show_poster'] = !empty($new_instance['movie_show_poster']) ? 1 : 0;

    $instance['size'] = ( ! empty( $new_instance['size'] ) ) ? sanitize_text_field( $new_instance['size'] ) : '';

    $instance['movie_click_redirect'] = !empty($new_instance['movie_click_redirect']) ? 1 : 0;

    $instance['link_address'] = ( ! empty( $new_instance['link_address'] ) ) ? sanitize_text_field( $new_instance['link_address'] ) : '';

    $instance['movie_show_overview'] = !empty($new_instance['movie_show_overview']) ? 1 : 0;

    $instance['movie_show_trailer'] = !empty($new_instance['movie_show_trailer']) ? 1 : 0;

		return $instance;
  }

  // function queries the tmdb database for movies related to inputted movie title
  function movie_search($instance){
  
    // replaces blank spaces in variable with '+' to run query
    $movie_query = preg_replace('/\s+/', '+', $instance['movie_title']);
    
    // adds title to the link address + determines if TV or Movie link is required
    if ($instance['movie_type'] === 'movie') {
      $movie_adress = "https://api.themoviedb.org/3/search/movie?api_key=".$instance['tmdbapikey']."&query=" .$movie_query;
    } else {
      $movie_adress = "https://api.themoviedb.org/3/search/tv?api_key=".$instance['tmdbapikey']."&query=" .$movie_query;
    }
  
    // runs curl to fetch data from TMDB database
    $request = wp_remote_get($movie_adress);
  
    // posting of info
    if (is_wp_error($request)) {
      echo $request->get_error_message();

    } else {
      $response = wp_remote_retrieve_body($request);
      $jsonResponse = json_decode($response);// Convert String to JSON

      if ($jsonResponse->total_results < 1) {
        echo "No Poster Found, please check that a valid TMDB key and Movie/Show has been added";

      } else {
        $first_movie_result = $jsonResponse->results[0];
        return $first_movie_result;
      }

    }

  }

  // function displays movie poster depending on checkbox also handles clickable link of poster
  function display_poster($instance, $first_movie_result){
    if ($instance['movie_show_poster']){
      $movie_poster_path = $first_movie_result->poster_path;
      $movie_poster_url = "https://image.tmdb.org/t/p/".$instance['size'].$movie_poster_path;
      
      if ($instance['movie_click_redirect']){
        return '<a href='.$instance['link_address'].'><img src='.$movie_poster_url.' /></a>';
        // returns a clickable poster that will redirect to inputted URL
      } else{
        return '<img src='.$movie_poster_url.' />';
        // returns just the movie poster URL
      }
      
    } else {  
      return; // return nothing
    }

  }

  // function displays movie overview depending on checkbox
  function display_overview($instance, $first_movie_result){
    if ($instance['movie_show_overview']){
      $movie_overview = $first_movie_result->overview;
      return $movie_overview;

    } else {  
      return; // return nothing
    }

  }

  //function returns youtube movie trailer of movie
  function display_trailer($instance, $first_movie_result){
    $movie_id = $first_movie_result->id;
    
    // adds title to the link address + determines if TV or Movie link is required
    if ($instance['movie_type'] === 'movie') {
      $movie_trailer_adress = "https://api.themoviedb.org/3/movie/".$movie_id."/videos?api_key=".$instance['tmdbapikey'];
    } else {
      $movie_trailer_adress = "https://api.themoviedb.org/3/tv/".$movie_id."/videos?api_key=".$instance['tmdbapikey'];
    }
  
    //runs curl to fetch data from TMDB database
    $request_1 = wp_remote_get($movie_trailer_adress);
  
    // posting of info
    if (is_wp_error($request_1)) {
      echo $request_1->get_error_message();

    } else {
      $response_1 = wp_remote_retrieve_body($request_1);
      $jsonResponse_1 = json_decode($response_1);// Convert String to JSON

      if ($jsonResponse_1->results < 1) {
        echo "No Trailer Found, please check that a valid TMDB key and Movie/Show has been added";

      } else {
        $first_movie_trailer = $jsonResponse_1->results[0];
        $movie_trailer_key = $first_movie_trailer->key;
        return $movie_trailer_key;

      }

    }

  }

} // Movie_Poster_Display_Widget

