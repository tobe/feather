<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . DS);

// --- WINDOWS FIX ---
// Windows does not include posix_getpwuid most likely!
if(!function_exists('posix_getpwuid')) {
	function posix_getpwuid() {
		$data = array();
		return $data['user'] = 'user';
	}
}

Class feather {

    public $directory, $pattern, $mode;

    public function __construct($directory, $pattern, $mode = NULL) {
        $this->directory    = $directory;
        $this->pattern      = $pattern;
        $this->mode         = $mode;

        // Reformat $this->directory to be pan system friendly
        $this->directory = ROOT . $this->directory;
        // var_dump($this->directory);

        // First of all, check whether the directory exists and it's accessable.
        if(!is_dir($this->directory) || !is_readable($this->directory)) { throw new Exception('Cannot access the given directory'); }

        switch($this->mode) {
            case NULL:
                // Loads all the posts, using a foreach
                $this->load_all();
            break;

            case is_string($this->mode):
                // Loads a single post
                $this->load_single();
            break;

            case is_numeric($this->mode):
                // Loads a number of posts, using a for loop
                $this->load_some();
            break;

            default:
                // Something is wrong.
                throw new Exception('Invalid mode supplied? ($this->mode');
                die();
            break;
        }
    }

    /** Callback, BBCode, htmlspecialchars  */
    private function parse($data, $filename) {
        // We'll save everything in a nice array that we are going to retun
        $content = [];

        // Take care of newlines and sanitize the content.
        $content['data'] = htmlspecialchars($data); // entities don't work with UTF-8
        $content['data'] = str_replace('\n', "<br>", $content['data']); // Replace \n with <br>. 

        $search = array(
    				'/\[bold\](.*?)\[\/bold\]/is',
    				'/\[italic\](.*?)\[\/italic\]/is',
    				'/\[underline\](.*?)\[\/underline\]/is',
                    '/\[strikethrough\](.*?)\[\/strikethrough\]/is',
    				'/\[image\](.*?)\[\/image\]/is',
    				'/\[link=(.*?)\](.*?)\[\/link\]/is',
    				'/\[font color=(.*?)\](.*?)\[\/font\]/is',
    				'/\[heading=(.*?)\](.*?)\[\/heading\]/is',
                    '/\[center\](.*?)\[\/center\]/is',
                    '/\[list\](.*?)\[\/list\]/is',
                    '/\[ul\]/',
                    '/\[\/ul\]/',
    				'/\[code\](.*?)\[\/code\]/is',
    				'/\[codeline\](.*?)\[\/codeline\]/is',
                    '/\[codebox\](.*?)\[\/codebox\]/is',
                    '/\[p\](.*?)\[\/p\]/is'
    	);
    	
    	$replace = array(
    				'<span style="font-weight: 700;">$1</span>',
    				'<span style="font-style: italic;">$1</span>',
    				'<span style="text-decoration: underline;">$1</span>',
                    '<span style="text-decoration: line-through;">$1</span>',
    				'<a href="$1" target="_blank"><img src="$1" class="featherimg" style="max-width: 750px; max-height: 400px;"></a>',
    				'<a href="$1" rel="nofollow">$2</a>',
    				'<span style="color: $1;">$2</span>',
    				'<h$1>$2</h$1>',
                    '<center>$1</center>',
                    '<li>$1</li>',
                    '<ul style="padding-left: 15px;">',
                    '</ul>',
    				'<pre>$1</pre>',
    				'<code>$1</code>',
                    '<pre style="width: auto;padding: 10px; font-family: monospace,inherit;font-size:12px;color: #8C5454;overflow:hidden;">$1</pre>',
                    '<p>$1</p>',
    	);
    	
        $content['data'] = preg_replace($search, $replace, $content['data']);
        
        // That was some BBCode crap...now get the user, domain (hostname), time, date, title, title_raw
        $content['user']        = posix_getpwuid(fileowner($filename));
        $content['hostname']	= gethostname();
        $content['time']		= filemtime($filename);
        $content['nice_time']   = date('H:i:s', filemtime($filename));
        $content['date']		= date('d.m.Y', filemtime($filename));
        #$pt = explode(DS, $filename);
        #$content['title']       = end($pt);
        $content['title']       = $filename;

        $pt = explode(DS, $filename);
        $content['nice_title']  = end($pt);
        $content['nice_title']	= $this->nice_title($content['nice_title']);

        return $content;
    }

    private function nice_title($filename) {
        // Example: hello_world.entry
        $filename = str_replace('_', '&nbsp;', $filename); // _ -> 0x20
        $filename = ucfirst($filename); // Capitalize the first letter.

        // Remove the ".entry" part.
        $filename = explode('.', $filename);
        $filename = $filename[0]; 
        
        return $filename;
    }

    private function load_all() {
        // Load everything.
        $files = glob($this->directory . '*.entry');
        // Sort it by date.
        array_multisort(array_map('filemtime', $files), SORT_DESC, $files);



        // Are there any files at all? - Friendly
        if(empty($files)) {
           throw new Exception('There is nothing here...yet.'); 
        }

        // var_dump($files);

        foreach($files as $file) {
            // Some sanity checking.
            if(!file_exists($file) || !is_readable($file)) {
                throw new Exception('Could not access ' . $file . '. Does it exist? Permissions? $ chmod.');
            }

            // Assign a file handle, rb so it works on *DOS.
            $fp = fopen($file, 'rb');
            if(!$fp) { throw new Exception('Could not fopen ' . $file); }

            // Read into the file handle
            $data = fread($fp, filesize($file));
            if(!$data) { throw new Exception('Could not fread ' . $file); }

            // Close it now
            fclose($fp);

            // Send this off to the parser before outputting.
            $content = $this->parse($data, $file);

            // Output!
            // Grab the pattern and do some replacing
            #$look_for       = ['{user}', '{hostname}', '{time}', '{nice_time}', '{date}', '{title}', ];

            $look_for = [];
            foreach($content as $k => $v) {
                $look_for[$k]     = '{' . $k  . '}';
            }

            $pattern_reset = $this->pattern;
            foreach($look_for as $k => $v) {
                $this->pattern = str_replace($v, $content[$k], $this->pattern);
            }

            echo $this->pattern;
            $this->pattern = $pattern_reset;
            
        }
    }

    private function load_some() {
        
    }

    private function load_single() {

    }

}
