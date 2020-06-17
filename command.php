<?php 

if( ! class_exists( 'WP_CLI' ) ) return;

class removing_cron_jobs extends WP_CLI_Command {

	function __invoke(){
		//Remove duplicate cron jobs without arguments ($args = null) to avoid log thousand of warnings
		$this->remove_duplicate_cron_jobs_all_sites();		
	}

	//Remove duplicate cron jobs without arguments ($args = null) to avoid log thousand of warnings
	public function remove_duplicate_cron_jobs(){
	    $cron_data = get_option('cron');
	    foreach( $cron_data as $timestamps => $plugins ){
	        if( is_array( $plugins ) ){
	            foreach( $plugins as $plugin => $jobs){
	                foreach( $jobs as $job => $data ){
	                    if( is_array( $data ) && array_key_exists('args', $data ) && is_null($data['args']) ){
	                        unset($cron_data[$timestamps][$plugin][$job]);
	                    }
	                }      
	            }
	        }        
	    }
	    update_option('cron', $cron_data);
	    WP_CLI::success( get_current_blog_id(). " done" );
	}

	//recorrer todos los sites
	public function remove_duplicate_cron_jobs_all_sites(){
		global $wpdb;
		//obtener lista de sites
		$blogs_id = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->prefix}blogs" );
		//recorrer con foreach
		//foreach( $blogs_id as $blog_id ){
			switch_to_blog( 1195 );
			$this->remove_duplicate_cron_jobs();
			restore_current_blog();
		//}
		WP_CLI::success( 'Done in all sites');
	}
	
}

WP_CLI::add_command( 'removing_cron_jobs', 'removing_cron_jobs' );


