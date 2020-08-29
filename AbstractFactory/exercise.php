<?php

namespace ShopingCartFramework {
    class Shop {
        protected $shopFactory;
        public function __construct(ShopFactoryInterface $shopFactory) {
            $this->shopFactory = $shopFactory;
        }
        public function listProductsWithShippingCost(array $codes, $shippingMethodName, $miles) {
            $output = array();
            foreach ($codes as $code) {
                $product = $this->shopFactory->createProduct($code);
                $shippingMethod = $this->shopFactory->createShippingMethod($shippingMethodName);
                $output[] = $product->getShopProductCode() . ' - '
                    . $product->getShopDescription() . ' / via: '
                    . $shippingMethod->getName() . ', cost: $'
                    . $shippingMethod->getCostEstimate($miles, $product);
            }
            return implode(PHP_EOL, $output);
        }
    }
    interface ShopFactoryInterface {
        public function createProduct($productCode);
        public function createShippingMethod($name);
    }
    interface ProductInterface {
        public function getShopProductCode();
        public function getShopDescription();
    }
    interface ShippingMethodInterface {
        public function getName();
        public function getCostEstimate($miles, ProductInterface $product);
    }
}

namespace MyCompanyShop {

    use ShopingCartFramework\ShopFactoryInterface;
    use ShopingCartFramework\ProductInterface;
    use ShopingCartFramework\ShippingMethodInterface;

    interface Visitors{
        function writeData();   
    }
    
    class MyShopProductFactory implements ShopFactoryInterface{
        function __constructor($productData, $shippingMethodData){
            $this->products = $productData;
            $this->shipping = $shippingMethodData;
        }
        function createProduct($productCode){
            if(!empty($this->products[$productCode]))
                return MyShopProduct(
                    $productCode, 
                    $this->products[$productCode][0],
                    $this->products[$productCode][1]
                );
            return null;
        }
        function createShippingMethod($name){
            if(!empty($this->shipping[$name]))
                return MyShopProduct(
                    $name, 
                    $this->shipping[$name]
                );
            return null;
        }
        
    }

    class MyShopProduct implements ProductInterface{
        private $data = [];
        function __constructor($code, $name, $weight){
               $this->data['code'] = $code;
               $this->data['name'] = $name;
               $this->data['weight'] = $weigth;
        }
        
        function getShopProductCode(){
            return $this->data['code'];
        }
        
        function getShopDescription(){
            return $this->data['code'].' - '.$this->data['name'];
        }
        
        function getCost(Visitors $visitor){
            return $visitor->writeData($this->data);
        }
        
    }

    class MyShippingMethod implements ShippingMethodInterface{
        function __constructor($name, $params){
            $this->data['name'] = $name;
            $this->data['params']['weight'] = $params[1];
            $this->data['params']['miles'] = $params[0];
        }
        
        function getName(){
            return $this->data['name'];
        }
        
        function getCostEstimate($miles, ProductInterface $product){
            return ($product->getCost(new CostEstimate($this->data['params'])))->calc();
        }
    }

    class CostEstimate implements Visitors{
        function __constructor($factors, $miles){
            $this->factors = $factors;
            $this->miles = $miles;
        }
        
        function writeData($data){
            $this->product = $data; 
        }
        
        function calc(){
            return $this->miles * $this->factors['miles'] + $this->product['weight'] * $this->factors['weight'];
        }
    }
    
}

namespace {
    use ShopingCartFramework\Shop;
    use MyCompanyShop\MyShopProductFactory;

    $productData = [
        // id => [name, weight]
        'BumperSticker1' => ['Bumper Sticker Item #1', 1],
        'BumperSticker3' => ['Bumper Sticker Item #3', 1],
        'BumperSticker5' => ['Bumper Sticker Item #5', 1],
        'CoffeeTableBook6' => ['Coffee Table Book Item #6 (500 pages)', 5],
        'CoffeeTableBook7' => ['Coffee Table Book Item #7 (300 pages)', 3],
        'CoffeeTableBook9' => ['Coffee Table Book Item #9 (900 pages)', 9],
    ];

    $shippingMethodData = [
        // code => [miles, weight]
        'UPS' => [1.4, 1.1],
        'FEXED' => [2.2, 1.3],
    ];

    $shop = new Shop(new MyShopProductFactory($productData, $shippingMethodData));

    $targetOutput = <<<EOS
BumperSticker1 - Bumper Sticker Item #1 / via: UPS, cost: $15.1
CoffeeTableBook6 - Coffee Table Book Item #6 (500 pages) / via: UPS, cost: $19.5
EOS;

    // simulation of Shopping Cart Listings Page

    $actualOutput = $shop->listProductsWithShippingCost(['BumperSticker1', 'CoffeeTableBook6'], 'UPS', 10);

    assert($actualOutput == $targetOutput);
}
