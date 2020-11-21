<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

use WP_CLI\Process;

use WP_CLI\Utils;

class Penguinet_Scaffold_Plugin_Command {

	/**
	 * The plugin folder
	 *
	 * @var string
	 */
	private $plugin_folder;

	/**
	 * Scaffold the code for a new OOP plugin
	 *
	 * --name=<plugin_name>
	 * : The plugin name
	 *
	 * --slug=<slug>
	 * : The plugin slug
	 *
	 * --description=<description>
	 * : The plugin description visible in the Plugins' page
	 *
	 * --namespace=<unique-namespace>
	 * : The vendor namespace of your plugin
	 *
	 * --dev-name=<developer-name>
	 * : The developer name
	 *
	 * --dev-email=<email>
	 * : The developer email
	 *
	 * --plugin-url=<url>
	 * : The url of the plugin
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( ! preg_match( '/^[a-z][a-z0-9\-]*$/', $assoc_args['slug'] ) ) {
			WP_CLI::error( "Invalid slug specified. Plugin slugs can contain only lowercase alphanumeric characters or dashes, and start with a letter." );
		}

		$assoc_args['ucwords-slug'] = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $assoc_args['slug'] ) ) );
		$assoc_args['upper-slug']   = strtoupper( $assoc_args['ucwords-slug'] );
		$assoc_args['lower-slug']   = strtolower( $assoc_args['ucwords-slug'] );
		$assoc_args['namespace']    = ucfirst( $assoc_args['namespace'] );
		$assoc_args['lower-namespace']    = strtolower( $assoc_args['namespace'] );
		$this->plugin_folder        = $assoc_args['slug'];
		$this->create_oop_plugin( $assoc_args );
		WP_CLI::success( 'DONE!' );
	}

	private function create_oop_plugin( $assoc_args ) {
		$package_root  = dirname( __FILE__ );
		$template_path = $package_root . '/template/';

		/**
		 * Create plugin folder
		 */
		$this->create_folder();

		/**
		 * Root folder
		 */
		$folder_path    = $template_path;

		$content_ignore  = file_get_contents( "{$folder_path}.gitignore" );
		$filename_ignore = $this->get_output_path( '.gitignore' );
		$this->create_files( array( $filename_ignore => $content_ignore ), false );

		$content_composer  = Utils\mustache_render( "{$template_path}composer.mustache", $assoc_args );
		$filename_composer = $this->get_output_path( 'composer.json' );
		$this->create_files( array( $filename_composer => $content_composer ), false );

		$content_index  = file_get_contents( "{$folder_path}index.php" );
		$filename_index = $this->get_output_path( 'index.php' );
		$this->create_files( array( $filename_index => $content_index ), false );

		$content_uninstall  = Utils\mustache_render( "{$folder_path}uninstall.mustache", $assoc_args );
		$filename_uninstall = $this->get_output_path( 'uninstall.php' );
		$this->create_files( array( $filename_uninstall => $content_uninstall ), false );

		$content_readme  = Utils\mustache_render( "{$folder_path}READMETXT.mustache", $assoc_args );
		$filename_readme = $this->get_output_path( 'README.txt' );
		$this->create_files( array( $filename_readme => $content_readme ), false );

		$content_root  = Utils\mustache_render( "{$folder_path}plugin-name.mustache", $assoc_args );
		$filename_root = $this->get_output_path( $assoc_args['slug'] . '.php' );
		$this->create_files( array( $filename_root => $content_root ), false );

		/**
		 * Admin folder
		 */
		$folder_path = $template_path . 'Admin/';
		$this->create_folder( 'Admin' );
		$content_class  = Utils\mustache_render( "{$folder_path}Admin.mustache", $assoc_args );
		$filename_class = $this->get_output_path( 'Admin/Admin.php' );
		$this->create_files( array( $filename_class => $content_class ), false );

		$content_index  = file_get_contents( "{$folder_path}index.php" );
		$filename_index = $this->get_output_path( 'Admin/index.php' );
		$this->create_files( array( $filename_index => $content_index ), false );

		/**
		 * Admin/Css folder
		 */
		$css_path = $folder_path . 'css/';

		$content  = Utils\mustache_render( "{$css_path}admin.mustache", $assoc_args );
		$filename = $this->get_output_path( 'Admin/css/admin.css' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Admin/Js folder
		 */
		$js_path = $folder_path . 'js/';

		$content  = file_get_contents( "{$js_path}admin.js" );
		$filename = $this->get_output_path( 'Admin/js/admin.js' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Admin/Partials folder
		 */
		$partials_path = $folder_path . 'partials/';
		$content       = Utils\mustache_render( "{$partials_path}admin-display.mustache", $assoc_args );
		$filename      = $this->get_output_path( 'Admin/partials/admin-display.php' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Front folder
		 */
		$folder_path = $template_path . 'Front/';
		$this->create_folder( 'Front' );
		$content_class  = Utils\mustache_render( "{$folder_path}Front.mustache", $assoc_args );
		$filename_class = $this->get_output_path( 'Front/Front.php' );
		$this->create_files( array( $filename_class => $content_class ), false );

		$content_index  = file_get_contents( "{$folder_path}index.php" );
		$filename_index = $this->get_output_path( 'Front/index.php' );
		$this->create_files( array( $filename_index => $content_index ), false );

		/**
		 * Front/Css folder
		 */
		$css_path = $folder_path . 'css/';

		$content  = file_get_contents( "{$css_path}front.css" );
		$filename = $this->get_output_path( 'Front/css/front.css' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Front/Js folder
		 */
		$js_path = $folder_path . 'js/';

		$content  = file_get_contents( "{$js_path}front.js" );
		$filename = $this->get_output_path( 'Front/js/front.js' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Front/Partials folder
		 */
		$partials_path = $folder_path . 'partials/';
		$content       = Utils\mustache_render( "{$partials_path}front-display.mustache", $assoc_args );
		$filename      = $this->get_output_path( 'Front/partials/front-display.php' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Languages folder
		 */
		$folder_path = $template_path . 'Languages/';
		$this->create_folder( 'Languages' );

		$content  = file_get_contents( "{$folder_path}plugin-name.pot" );
		$filename = $this->get_output_path( 'Languages/' . $assoc_args['slug'] . '.pot' );
		$this->create_files( array( $filename => $content ), false );

		/**
		 * Includes folder
		 */
		$folder_path = $template_path . 'Includes/';
		$this->create_folder( 'Includes' );
		$content_class  = Utils\mustache_render( "{$folder_path}plugin-name.mustache", $assoc_args );
		$filename_class = $this->get_output_path( 'Includes/' . $assoc_args['ucwords-slug'] . '.php' );
		$this->create_files( array( $filename_class => $content_class ), false );

		$content_activator  = Utils\mustache_render( "{$folder_path}Activator.mustache", $assoc_args );
		$filename_activator = $this->get_output_path( 'Includes/Activator.php' );
		$this->create_files( array( $filename_activator => $content_activator ), false );

		$content_deactivator  = Utils\mustache_render( "{$folder_path}Deactivator.mustache", $assoc_args );
		$filename_deactivator = $this->get_output_path( 'Includes/Deactivator.php' );
		$this->create_files( array( $filename_deactivator => $content_deactivator ), false );

		$content_i18n  = Utils\mustache_render( "{$folder_path}I18n.mustache", $assoc_args );
		$filename_i18n = $this->get_output_path( 'Includes/I18n.php' );
		$this->create_files( array( $filename_i18n => $content_i18n ), false );

		$content_loader  = Utils\mustache_render( "{$folder_path}Loader.mustache", $assoc_args );
		$filename_loader = $this->get_output_path( 'Includes/Loader.php' );
		$this->create_files( array( $filename_loader => $content_loader ), false );

		$content_index  = file_get_contents( "{$folder_path}index.php" );
		$filename_index = $this->get_output_path( 'Includes/index.php' );
		$this->create_files( array( $filename_index => $content_index ), false );

	}

	/**
	 * Create the folder and return the path to the plugin
	 *
	 * @param string $filepath The file path into the plugin folder.
	 *
	 * @return string
	 */
	private function get_output_path( $filepath ) {
		$path = WP_PLUGIN_DIR . '/' . $this->plugin_folder . '/' . $filepath;

		return $path;
	}

	/**
	 * @param array $files_and_contents Array of filename and relative content.
	 * @param bool  $force Force overwrite.
	 *
	 * @return array
	 */
	protected function create_files( $files_and_contents, $force ) {
		$wp_filesystem = $this->init_wp_filesystem();
		$wrote_files   = array();
		foreach ( $files_and_contents as $filename => $contents ) {
			$should_write_file = $this->prompt_if_files_will_be_overwritten( $filename, $force );
			if ( ! $should_write_file ) {
				continue;
			}
			$wp_filesystem->mkdir( dirname( $filename ) );
			if ( ! $wp_filesystem->put_contents( $filename, $contents ) ) {
				WP_CLI::error( "Error creating file: $filename" );
			} elseif ( $should_write_file ) {
				$wrote_files[] = $filename;
			}
		}

		return $wrote_files;
	}

	protected function create_folder( $path = '' ) {
		$wp_filesystem = $this->init_wp_filesystem();
		$wp_filesystem->mkdir( WP_PLUGIN_DIR . '/' . $this->plugin_folder . '/' . $path );
	}

	/**
	 * Initializes WP_Filesystem.
	 */
	protected function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();

		return $wp_filesystem;
	}

	protected function prompt_if_files_will_be_overwritten( $filename, $force ) {
		$should_write_file = true;
		if ( ! file_exists( $filename ) ) {
			return true;
		}
		WP_CLI::warning( 'File already exists.' );
		WP_CLI::log( $filename );
		if ( ! $force ) {
			do {
				$answer = cli\prompt(
					'Skip this file, or replace it with scaffolding?',
					$default = false,
					$marker = '[s/r]: '
				);
			} while ( ! in_array( $answer, array( 's', 'r' ) ) );
			$should_write_file = 'r' === $answer;
		}
		$outcome = $should_write_file ? 'Replacing' : 'Skipping';
		WP_CLI::log( $outcome . PHP_EOL );

		return $should_write_file;
	}


}

WP_CLI::add_command( 'scaffold oop-plugin', 'Penguinet_Scaffold_Plugin_Command' );
