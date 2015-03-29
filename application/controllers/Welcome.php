<?php
/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {
    function __construct()
    {
	parent::__construct();
        $this->load->helper('directory');
    }
    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------
    function index()
    {
        $this->load->model('order');
        
	// Build a list of orders
        $dir = directory_map('./data/');
        $files = array();
        
        // Loop through each file
        foreach ($dir as $file)
        {
            // If the file is an XML file
            if (strpos($file, 'order') !== false && strpos($file, '.xml') !== false)
            {        
                $order = new Order($file);
                
                // Add the filename to an array
                $files[] = array(
                    'filename' => substr($file, 0, strlen($file) - 4),
                    'customer' => $order->customer
                );
            }
        }
        // Put the files into the view
	$this->data['orders'] = $files;
        
	// Present the list to choose from
	$this->data['pagebody'] = 'homepage';
	$this->render();
    }
    
    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------
    function order($filename)
    {
        $this->load->model('order');
        $order = new Order($filename . '.xml');
        
	//Build a receipt for the chosen order
	$this->data['filename'] = $filename;
        $this->data['customer'] = $order->customer;
        $this->data['type'] = $order->type;
        $this->data['burgers'] = $order->burgers;
        $this->data['total'] = $order->total;
        $this->data['special'] = $order->orderInstructions;
        
	// Present the list to choose from
	$this->data['pagebody'] = 'justone';
	$this->render();
    }
    
}
