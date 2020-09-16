<?php

namespace App\Service;

use App\Repository\BasketRowRepository;
use App\Repository\ProductRepository;

class WebsiteService{

    private $basketRowRepository;
    private $productRepository;

    public function __construct(BasketRowRepository $basketRowRepository, ProductRepository $productRepository)
    {
        $this->basketRowRepository = $basketRowRepository;
        $this->productRepository = $productRepository;
    }

    public function getBasketAmount($customerid)
    {
        if ($this->basketRowRepository->getBasketAmount($customerid) == null){
            return 0;
        }else{
            return $this->basketRowRepository->getBasketAmount($customerid);
        }
    }

    public function getBrandByCategory($category)
    {
        return $this->productRepository->getBrandByCategory($category);
    }

    public function getCategories()
    {
        return $this->productRepository->getCategories();
    }


}