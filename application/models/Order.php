<?php
class Order extends CI_Model {
    
    protected $xml = null;
    public $customer;
    public $type;
    public $orderInstructions = "";
    public $burgers = array();
    public $total = 0.00;
    
    public function __construct($filename = null) {
        parent::__construct();
        if ($filename == null)
        {
            return;
        }
        
        $this->load->model('menu');
        
        $this->xml = simplexml_load_file(DATAPATH . $filename);
        
        // Assign the customer name
        $this->customer = (string) $this->xml->customer;
        
        // Assign the order type
        $this->type = (string) $this->xml['type'];
        
        // Assign the order instructions
        if (isset($this->xml->special))
        {
            $this->orderInstructions = (string) $this->xml->special;
        }
        
        // Loop through each burger and add it to the order
        $i = 0;
        
        foreach ($this->xml->burger as $burger)
        {
            $i++;
            
            $newBurger = array(
                'patty' => $burger->patty['type']
            );
            
            
            // Set the burger number
            $newBurger['num'] = $i;
            
            // Set cheeses
            $cheeses = "";
            
            if (isset($burger->cheeses['top']))
            {
                $cheeses .= $burger->cheeses['top'] . "(top), ";
            }
            
            if (isset($burger->cheeses['bottom']))
            {
                $cheeses .= $burger->cheeses['bottom'] . "(bottom)";
            }
            
            $newBurger['cheese'] = $cheeses;
            
            // Set toppings
            $toppings = "";
            
            // If we have no toppings
            if (!isset($burger->topping))
            {
                $toppings .= "none";    
            }
            
            foreach($burger->topping as $topping)
            {
                $toppings .= $topping['type'] . ", ";
            }
            
            $newBurger['toppings'] = $toppings;
            
            // Set sauces
            $sauces = "";
            
            // If we have no sauces
            if (!isset($burger->sauce))
            {
                $sauces .= "none";    
            }
            
            foreach($burger->sauce as $sauce)
            {
                $sauces .= $sauce['type'] . ", ";
            }
            
            $newBurger['sauces'] = $sauces;
            
            // Set instructions if they exist
            if (isset($burger->instructions))
            {
                $newBurger['instructions'] = (string) $burger->instructions;
            }
            else
            {
                $newBurger['instructions'] = "";
            }
            
            // Assign costs
            $cost = $this->getBurgerCost($burger);
            
            $newBurger['cost'] = $cost;
            $this->total += $cost;
                        
            // Add the new burger to the array
            $this->burgers[] = $newBurger;
        }
    }
    
    private function getBurgerCost($burger)
    {
        $burgerTotal = 0.00;
        
        // Add the patty price to the total
        $burgerTotal += $this->menu->getPatty((string) $burger->patty['type'])->price;
        
        // Add the cheeses price to the total
        if (isset($burger->cheeses['top']))
        {
            $burgerTotal += $this->menu->getCheese((string) $burger->cheeses['top'])->price; 
        }
        
        if (isset($burger->cheeses['bottom']))
        {
            $burgerTotal += $this->menu->getCheese((string) $burger->cheeses['bottom'])->price; 
        }
        
        // Add the toppings price to the total
        foreach ($burger->topping as $topping)
        {
            $burgerTotal += $this->menu->getTopping((string) $topping['type'])->price; 
        }
        
        // Add the sauces price to the total
        foreach ($burger->sauce as $sauce)
        {
            $burgerTotal += $this->menu->getSauce((string) $sauce['type'])->price; 
        }
        
        return $burgerTotal;
    }
            
}

